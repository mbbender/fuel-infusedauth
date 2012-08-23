<?php
/**
 * InfusedAuth is an add on to SimpleAuth
 *
 * Primary login driver that extends SimpleAuth. Most of the work gets done here!
 *
 * @package    InfusedAuth
 * @version    2.0
 * @author     Michael Bneder
 * @license    MIT License
 * @copyright  2012 Infused Industries, Inc.
 * @link       https://github.com/michael-bender/fuel-infusedauth
 */


namespace InfusedAuth;

use \Auth\SimpleUserUpdateException;

class InfusedAuthException extends \FuelException{}
class SimpleUserValidationException extends InfusedAuthException {}
class Auth_Login_InfusedAuth extends \Auth\Auth_Login_SimpleAuth
{

    /**
     * @static
     * Loads simpleauth, ninjauth, and infused auth config files.
     */
    public static function _init(){
        parent::_init();
        \Config::load('simpleauth',true,true,false);
        \Config::load('ninjauth',true,true,false);
        \Config::load('infusedauth',true,true,false);
    }


    /**
     * This function either creates a new user of \Model_User or of \Model_TempUser. If the configuration setting of
     * account_validation is set to true it will create a Model_TempUser and send the user an email which can be configured
     * in the config file. If bypass_verification is set to true a Model_User will be created with no email verification
     * regardless of settings.
     *
     * If the beta_wall configuration is set it will assign the beta_group_id instead of the default.
     *
     * @param   string  username
     * @param   string  password
     * @param   string  email address
     * @param   int     group id
     * @param   Array   profile fields
     * @param   bool    thirdparty adapter making the call
     * @param   bool    bypass_verification overrides config setting if verification is enabled
     * @throws SimpleUserUpdateException, SimpleUserValidationException
     * @returns \Model_User|\Model_TempUser|false
     */
    public function create_user($username, $password, $email, $group = 1, Array $profile_fields = array(), $thirdparty=false, $bypass_verification=false)
    {
        $password = trim($password);
        $email = filter_var(trim($email), FILTER_VALIDATE_EMAIL);

        if (empty($username) or empty($password) or empty($email))
        {
            throw new SimpleUserUpdateException('Username, password and email address can\'t be empty.', 1);
        }

        // Set group for beta
        if(\Config::get('infusedauth.beta_wall',false)) $group = \Config::get('infusedauth.beta_group_id',-5);

        // Check to see if they are using non-unique credentials.
        $same_users = \DB::select_array(\Config::get('simpleauth.table_columns', array('*')))
            ->where('username', '=', $username)
            ->or_where('email', '=', $email)
            ->from(\Config::get('simpleauth.table_name'))
            ->execute(\Config::get('simpleauth.db_connection'));

        if ($same_users->count() > 0)
        {
            if (in_array(strtolower($email), array_map('strtolower', $same_users->current())))
            {
                throw new SimpleUserUpdateException('Email address already exists', 2);
            }
            else
            {
                throw new SimpleUserUpdateException('Username already exists', 3);
            }
        }

        // Handle account validation stuff
        if(!$bypass_verification and (\Config::get('infusedauth.account_validation',false) and
            (!$thirdparty or ($thirdparty and \Config::get('infusedauth.account_validation_3rdparty',false)))))
        {
            if(($user_temp_table = \Config::get('infusedauth.temp_table_name',false)) === FALSE)
                throw new SimpleUserValidationException('Must set temp_table_name configuration in infusedauth config file.', 1);

            $same_users = \DB::select_array(\Config::get('infusedauth.temp_table_columns', array('*')))
                ->where('username', '=', $username)
                ->or_where('email', '=', $email)
                ->from($user_temp_table)
                ->execute(\Config::get('infusedauth.db_connection'));

            if ($same_users->count() > 0)
            {
                if (in_array(strtolower($email), array_map('strtolower', $same_users->current())))
                {
                    throw new SimpleUserUpdateException('Email address already exists', 4, $same_users[0]['id']);
                }
                else
                {
                    throw new SimpleUserUpdateException('Username already exists', 5, $same_users[0]['id']);
                }
            }

            $temp_user_data = array(
                'username'        => (string) $username,
                'password'        => $this->hash_password((string) $password),
                'email'           => $email,
                'group'           => (int) $group,
                'profile_fields'  => json_encode($profile_fields),
                'created_at'      => \Date::forge()->get_timestamp(),
                'validation_code1' =>(string) $email,
                'validation_code2' => \Str::random('alnum', 10)

            );


            try{
                $temp_user = \Model_TempUser::forge($temp_user_data);
                if($temp_user->save() !== FALSE)
                {
                    $this->send_validation($temp_user->id);
                    return $temp_user;
                }
            }
            catch (\FuelException $e){
                \Session::set_flash('error',$e->getMessage());
            }

            return FALSE;

        }

        // Just go ahead and create a regular user, no temp user required.
        $user_data = array(
            'username'        => (string) $username,
            'password'        => $this->hash_password((string) $password),
            'email'           => $email,
            'group'           => (int) $group,
            'profile_fields'  => json_encode($profile_fields),
            'last_login'      => 0,
            'login_hash'      => '',
            'created_at'      => \Date::forge()->get_timestamp()
        );
        try{
            $user = \Model_User::forge($user_data);
            if($user->save() !== FALSE)
            {

                $temp_user = \Model_TempUser::find_by_email($user_data['email']);
                if(!empty($temp_user)) $temp_user->delete();

                if(\Config::get('infusedauth.send_user_signup_notification_to_system',false)) $this->send_signup_system_notification($user);
                return $user;
            }
        }
        catch (\FuelException $e){
            \Session::set_flash('error',$e->getMessage());
        }

        return FALSE;


    }

    /**
     * @param $temp_user_id
     * @return bool
     * @throws SimpleUserValidationException
     */
    public function send_validation($temp_user_id)
    {
        $user_temp = \Model_TempUser::find($temp_user_id);
        if(empty($user_temp)) throw new SimpleUserValidationException('Can not find user with id '.$temp_user_id,0);

        $send_method = \Config::get('infusedauth.notification.method','email');

        switch($send_method)
        {
            case 'email':

                $validation_link = \Uri::create(\Config::get('verify_action','auth/verify').'/'.$user_temp['email'].'/'.$user_temp['validation_code2']);

                \Package::load('email');
                $email = \Email::forge();
                $email->from(\Config::get('email.defaults.from.email',false),\Config::get('email.defaults.from.name',false));
                $email->to($user_temp['email']);
                $email->subject(\Config::get('infusedauth.notification.subject','ACTION REQUIRED! Validate your account.'));
                $email->html_body(\View::forge('account_confirmation_email',array('user'=>$user_temp,'validation_link'=>$validation_link)));
                try{
                    $email->send();
                }

                catch(\EmailValidationFailedException $e){
                    throw new SimpleUserValidationException('Unable to send email notification. '.$e->getMessage(),6);
                }
                catch(\EmailSendingFailedException $e){
                    throw new SimpleUserValidationException('Unable to send email notification. '.$e->getMessage(),7);
                }
                break;

            default:
                throw new SimpleUserValidationException('Unsupported notification method specified in configuration file.',8);
        }

        return $user_temp;
    }

    /**
     * Uses email to check to see if the user has connected with Facebook or another way and already verified
     * their account that way.
     * @param $code1
     * @param $code2
     * @return bool|\Model_User
     */
    public function verify($code1,$code2)
    {
        $temp_user = \Model_TempUser::find_by_email($code1);
        $user = \Model_User::find_by_email($code1);

        // If a valid user with this email already exists then delete the temp account and return the user.
        if(!empty($user)){
            //@todo: Validate an authentication from a 3rd parth exists and if so consider updating the users password with whatever the temp password was.
            // This will likely cause the need to modify login code to login for 3rd parties without the password. Not sure if it already does this or not.
            if(!empty($temp_user)) $temp_user->delete();
            return $user;
        }

        // If we found the temp user, turn it into a valid user and return the user.
        if(!empty($temp_user)){

            $temp_user_data = \DB::select()->from('users_temp')->where_open()
                ->where('email',$code1)
                ->and_where('validation_code2',$code2)->where_close()
                ->as_assoc()->execute()->as_array();

            if(empty($temp_user_data)) throw new InfusedAuthException('Invalid verification code.');


            $user = $this->create_user($temp_user->username,$temp_user->password,
                $temp_user->email,$temp_user->group,json_decode($temp_user->profile_fields,true),false,true);
            if($user !== false){
                $user->password = $temp_user_data[0]['password'];
                $user->save();
                return $user;
            }

        }

        // If we got here we could not find the temp user or an existing valid user.
        \Log::error('Auth_Login_InfusedAuth::verify(): Invalid codes or failed to execute on validate user. Code1: '.$code1.', code2: '.$code2);
        return false;

    }

    /**
     * Identifies if the credentials belong to a temp_user
     * @param $email
     * @param $password
     */
    public function validate_tempuser($username_or_email = '', $password = '')
    {
        $username_or_email = trim($username_or_email) ?: trim(\Input::post(\Config::get('simpleauth.username_post_key', 'username')));
        $password = trim($password) ?: trim(\Input::post(\Config::get('simpleauth.password_post_key', 'password')));

        if (empty($username_or_email) or empty($password))
        {
            return false;
        }

        $password = $this->hash_password($password);
        $this->user = \DB::select_array(\Config::get('simpleauth.table_columns', array('*')))
            ->where_open()
            ->where('username', '=', $username_or_email)
            ->or_where('email', '=', $username_or_email)
            ->where_close()
            ->where('password', '=', $password)
            ->from(\Config::get('infusedauth.temp_table_name'))
            ->execute(\Config::get('simpleauth.db_connection'))->current();

        return $this->user ?: false;
    }

    protected function send_signup_system_notification($user)
    {
        $to_address = \Config::get('app.system_email',false);
        if($to_address === false) throw new InfusedAuthException('Attempting to send a system notification but app.system_email configuration was not set. Please disable the new user signup system notification or add a system email address.');

        \Package::load('email');
        $email = \Email::forge();
        $email->from(\Config::get('email.defaults.from.email',false),\Config::get('email.defaults.from.name',false));
        $email->to($to_address);
        //$email->to('michael@sociablegroup.com');
        $email->subject(\Config::get('app.name','').' New user created.');
        $email->html_body(\View::forge('system/new_user_email',array('user'=>$user,'profile'=>$user->profile_fields()),false));
        try{
            $email->send();
        }

        catch(\EmailValidationFailedException $e){
            \Log::error('Unable to send email notification. '.$e->getMessage());
        }
        catch(\EmailSendingFailedException $e){
            \Log::error('Unable to send email notification. '.$e->getMessage());
        }
    }

    public function get_profile_fields()
    {
        if (empty($this->user))
        {
            return false;
        }

        if (isset($this->user['profile_fields']))
        {
            is_array($this->user['profile_fields']) or $this->user['profile_fields'] = json_decode($this->user['profile_fields']);
        }
        else
        {
            $this->user['profile_fields'] = array();
        }

        return $this->user['profile_fields'];
    }



}