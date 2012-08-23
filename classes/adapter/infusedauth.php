<?php
/**
 * InfusedAuth is an add on to SimpleAuth
 *
 * This is an adapter for NinjAuth
 *
 * @package    InfusedAuth
 * @version    1.0
 * @author     Michael Bneder
 * @license    Commercial License
 * @copyright  2012 Infused Industries, Inc.
 * @link       http://sociablegroup.com
 */

namespace NinjAuth;

class Adapter_InfusedAuth extends Adapter_SimpleAuth
{

    /**
     * This function parses the various 3rd party accounts to turn them into consistent user profile information
     *
     * @param array $user_hash
     * @return bool|void
     */
    public function create_user(array $user_hash)
    {
        try
        {

            // Munging data from Facebook into a standard user profile format. This should be configurable and likely
            // happen in a different place on a per provider basis.
            if(!isset($user_hash['username'])) $user_hash['username'] = $user_hash['uid'];
            if(!isset($user_hash['password'])) $user_hash['password'] = \Str::random('unique');

            $profile = array();
            isset($user_hash['image']) ? $profile['avatar'] = $user_hash['image'] : null;
            isset($user_hash['urls']) ? $profile['urls'] = array('facebook'=>$user_hash['urls']['Facebook']) : null;
            isset($user_hash['name']) ? $profile['full_name'] = $user_hash['name'] : null;


            $user = \Auth::create_user(

            // Username
                isset($user_hash['username']) ? $user_hash['username'] : null,

                // Password (random string will do if none provided)
                isset($user_hash['password']) ? $user_hash['password'] : \Str::random(),

                // Email address
                isset($user_hash['email']) ? $user_hash['email'] : null,

                // Which group are they?
                \Config::get('ninjauth.default_group'),

                // Extra information
                $profile,

                true
            );

            return $user->id ?: false;
        }
        catch (SimpleUserUpdateException $e)
        {
            \Session::set_flash('error', $e->getMessage());
        }

        return false;
    }

    public function can_auto_login(array $user_hash)
    {
        //return isset($user_hash['username']) or isset($user_hash['email']) and isset($user['password']);
        //return (isset($user_hash['username']) or isset($user_hash['email'])) and isset($user['password']);
        //@todo: Make this contingent upon adapter, and config settings for required registration fields.
        return true;
    }

}
