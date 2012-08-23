<?php
/**
 * Created by JetBrains PhpStorm.
 * User: michael
 * Date: 8/14/12
 * Time: 11:06 AM
 * To change this template use File | Settings | File Templates.
 */

namespace InfusedAuth;

/**
 * Baseline for implementing an authentication controller in your application using InfusedAuth.
 *
 * You must create a controller that extends this controller such
 *     class Controller_Auth extends \InfusedAuth\Controller_InfusedAuth
 *
 * Usage:
 * You can use the built in view scaffolding by default or create your views and link actions as follows:
 * 3rd party login = /session/provider //see NinjAuth for more details
 * properitery login = just post back to same script. you can override posthandler_login and posthandler_register to customize
 * forgot password = /reset_password
 * resend validation = /send_validation
 *      - requires post values of username which is username_or_email and password
 *
 * Customizing Validation
 * coming soon... TODO: Add validation customization info
 */
class Controller_InfusedAuth extends \NinjAuth\Controller
{
    public function before()
    {
        parent::before();

        // Since we can not extend more than one class at a time this is copied from Controller_Base and should be kept
        // in sync until a better way is determined.
        //
        // Assign current_user to the instance so controllers can use it
        $user_screen_name = \Auth::instance()->get_screen_name();
        $user = \Model_User::find_by_username($user_screen_name);
        $this->current_user = \Auth::instance()->check() ? $user : null;
        // Set a global variable so views can use it
        \View::set_global('current_user', $this->current_user);
        if(!empty($this->current_user)) \View::set_global('profile_fields', $this->current_user->profile_fields());

        // Load the configuration for this provider and overwrite default configurations from the NinjAuth package
        \Config::load('simpleauth',true,true,false);
        \Config::load('ninjauth',true,true,false);
        \Config::load('infusedauth',true,true,false);

        self::$linked_redirect = \Config::get('infusedauth.urls.linked_redirect','/');
        self::$login_redirect = \Config::get('infusedauth.urls.login_redirect','/');
        self::$register_redirect = \Config::get('infusedauth.urls.register_redirect','/');
        self::$registered_redirect = \Config::get('infusedauth.urls.registered_redirect','/');
    }

    /**
     * Defaults to login view
     */
    public function action_index()
    {
        // @TODO: Implement base controller index
        \Response::redirect(\Uri::string().'/login');
    }

    //-------------------------------------------------------------------------------
    // Login related functionality
    //-------------------------------------------------------------------------------

    public function action_login()
    {
        if(\Auth::instance()->check()){
            \Response::redirect(self::$login_redirect);
        }

        $login_fieldset = \Fieldset::forge('login');
        $fields = \Config::get('infusedauth.fieldsets.login',null);
        if(empty($fields)) throw new \Exception('The infusedauth.fieldsets.login configuration is required.');
        for($i=0;$i<count($fields);$i++){
            $login_fieldset->add($fields[$i]['name'],$fields[$i]['label'],$fields[$i]['attributes'],$fields[$i]['rules']);
        }

        if(\Request::active()->get_method() == "POST" && \Auth::instance()->login(\Input::post('email'),\Input::post('password'))){
            $next_location = (\Session::get('next_url',false)) ?  \Session::get('next_url') : \Config::get('infusedauth.urls.login_redirect','/');
            \Response::redirect($next_location);
        }elseif(\Request::active()->get_method() == "POST" && ($user = \Auth::instance()->validate_tempuser(\Input::post('email'),\Input::post('password')))){
            return $this->verification_pending_view(array('user'=>(object)$user));
        }elseif(\Request::active()->get_method() == "POST"){
             \Session::set_flash('error','Invalid username or password');
        }


        $login_fieldset->populate(\Input::post());
        $data['providers'] = \Config::get('ninjauth.providers');
        $data['login_fieldset'] = $login_fieldset;
        return $this->login_view($data);
    }

    /**
     * Override this function to customize the login view generation such as for themes or customized views outside
     * the scope of just setting the location to your view file in the config setting.
     * @param $data View data. Recommend you add data to this vs. overwrite.
     * @return mixed
     */
    public function login_view($data)
    {
        return \View::forge(\Config::get('infusedauth.views.login','login'), $data);
    }

    //-------------------------------------------------------------------------------
    // Registration related functionality
    //-------------------------------------------------------------------------------

    public function action_register()
    {
        if(\Auth::instance()->check() && !\Session::get('ninjauth',false)){
            \Response::redirect(self::$login_redirect);
        }

        // Build fieldset and send to create view hook
        $registration_fieldset = \Fieldset::forge('register');
        $fields = \Config::get('infusedauth.fieldsets.register',null);
        if(empty($fields)) throw new \Exception('The infusedauth.fieldsets.register configuration is required.');
        for($i=0;$i<count($fields);$i++){
            $registration_fieldset->add($fields[$i]['name'],$fields[$i]['label'],$fields[$i]['attributes'],$fields[$i]['rules']);
        }

        //@TODO: Add code to catch NinjAuth session data to determine if additional info is needed to complete registration
        // Check for NinjAuth session. If exists it means we were unable to autologin and create the user so some
        // mapping or additional fields must be required.
        if($ninjauth = \Session::get('ninjauth',false)){
            $user = $ninjauth['user'];
            $authentication = $ninjauth['authentication'];
        }

        if(\Request::active()->get_method() == "POST")
        {
            // Validate data
            if($registration_fieldset->validation()->run())
            {
                // Create user
                $username = \Input::post('username',false) ? \Input::post('username') : \Input::post('email');
                $password = \Input::post('password');
                $email = \Input::post('email',false) ? \Input::post('email') : '';
                $group = \Config::get('ninjauth.default_group',1);
                $profile_fields = $this->build_profile_fields(\Input::post(),\Config::get('infusedauth.profile_fields',false));
                try{
                    if($user = \Auth::instance()->create_user($username,$password,$email,$group,$profile_fields))
                    {
                        // Check to see if it is a temp user
                        if(is_subclass_of($user,'InfusedAuth\Model_TempUser')){
                            // Show verification pending view
                            return $this->verification_pending_view(array('user'=>$user));
                        }elseif(is_subclass_of($user,'InfusedAuth\Model_User')){
                            if(\Auth::instance()->force_login($user->id))
                                \Response::redirect(\Config::get('infusedauth.urls.registered_redirect','/'));
                            \Log::error('Unable to force user login. Please try to login');
                            \Response::redirect('login');
                        }else{
                            throw new \Fuel\Core\FuelException('User must extend \InfusedAuth\Model_User and \InfusedAuth\Model_TempUser');
                        }

                    }
                }
                catch(\Auth\SimpleUserUpdateException $e)
                {
                    \Session::set_flash('error',$e->getMessage());
                }
                catch(\Fuel\Core\FuelException $e){
                    \Log::error('infusedauth controller: '.$e->getMessage());
                    \Session::set_flash('error',$e->getMessage());
                }
            };
        }

        $registration_fieldset->populate(\Input::post());
        $data['providers'] = \Config::get('ninjauth.providers');
        $data['registration_fieldset'] = $registration_fieldset;
        return $this->registration_view($data);
    }

    /**
     * Override this function to customize the login view generation such as for themes or customized views outside
     * the scope of just setting the location to your view file in the config setting.
     * @param $data View data. Recommend you add data to this vs. overwrite.
     * @return mixed
     */
    public function registration_view($data)
    {
        return \View::forge(\Config::get('infusedauth.views.register','register'), $data);
    }

    /**
     * @param $all_fields This is the Input::post() values for the submitted registration form
     * @param $profile_fields This ist he configuration value that identifies which fields to include in the profile
     * @return $profile Array of fields.
     */
    protected function build_profile_fields($all_fields,$profile_fields)
    {
        $profile = array();
        for($i=0;$i<count($profile_fields);$i++){
            if(array_key_exists($profile_fields[$i]['form_field_name'],$all_fields)){
                $profile[$profile_fields[$i]['name']] = $all_fields[$profile_fields[$i]['form_field_name']];
            }
        }
        return $profile;
    }

    public function action_logout()
    {
        \Auth::instance()->logout();
        \Response::redirect(\Config::get('infusedauth.urls.logout_redirect','/'));
    }

    //------------------------------------
    // ACCOUNT VERIFICATION
    //------------------------------------
    public function verification_pending_view($data)
    {
        return \View::forge(\Config::get('infusedauth.views.verification_pending','registration_success'), $data);
    }

    public function action_verify($code1=null,$code2=null)
    {
        if(empty($code1) or empty($code2))
        {
            \Request::show_404();
        }

        try{
            $user = \Auth::instance()->verify($code1,$code2);
            if($user !== FALSE)
            {
                if(\Auth::instance()->force_login($user->id)){
                    \Session::set_flash('success','Thank you for verifying your account.');
                    \Response::redirect(\Config::get('infusedauth.urls.login_redirect','/'));
                }else{
                    \Session::set_flash('success','Thank you for verifying your account.');
                    $uri_path = \Uri::segments();
                    array_pop($uri_path);
                    array_pop($uri_path);
                    array_pop($uri_path);
                    \Response::redirect(implode('/',$uri_path).'/login');
                }
            }
            else
            {
                \Session::set_flash('error','Sorry but we failed to validate your user account. You can try signing up again. If you continue running into problems please contact support.');
                $uri_path = \Uri::segments();
                array_pop($uri_path);
                array_pop($uri_path);
                array_pop($uri_path);
                \Response::redirect(implode('/',$uri_path).'/register');
            }
        }
        catch(\Exception $e)
        {
            \Log::error('infuasedauth conroller: '.$e->getMessage());
            \Session::set_flash('error',$e->getMessage());
        }

    }

    public function action_send_validation_request()
    {
        $user = \Auth::instance()->send_validation(\Input::post('user_id'));
        return json_encode(array('success'=>'true','email'=>$user->email));
    }

    public function action_wall(){
        return $this->wall_view(null);
    }

    public function wall_view($data){
        return \View::forge('wall',$data);
    }



}
