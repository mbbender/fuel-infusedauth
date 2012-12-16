<?php
/**
 * InfusedAuth is an add on to SimpleAuth
 * @package    InfusedAuth
 * @version    1.0
 * @author     Michael Bneder
 * @license    Commercial License
 * @copyright  2012 Infused Industries, Inc.
 * @link       http://sociablegroup.com
 */

return array(

    'urls' => array(
        'acccount_validation_required' => 'register/verify',  //Controller action to show when register success but validation is required. User_id is appended to the end
        'registered' => 'admin',
        'verify_action' => 'auth/verify',  // This should point to your controller function that implements the Auth::verify() function
        'resend_verification_request' => 'resend_verification_request'
    ),


    /**
     * DB connection, leave null to use default
     */
    'db_connection' => null,

    /**
     * Require account validation during registration. This will result in a user being created in the user_temp
     * table first and once the account has been validated via validate() it will migrate the user into the user table.
     * (Requires user_temp table)
     */
    'account_validation' => true,

    /**
     * Table name for temp users. This table must mimic the User table with two additional fields:
     *      `validation_code1` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
     *      `validation_code2` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
     */
    'temp_table_name' => 'users_temp',
    'temp_table_columns' => array('*'),

    /**
     * Details of the notification sent to validate the account. Only email validation is currently supported, but
     * sms/text validation is coming soon.
     * This requires email to be configured properly.
     */
    'notification'=> array(
        'method'=>'email',
        'field'=>'email',
        'subject' => 'ACTION REQUIRED! Validate your account.'
    ),

    /**
     * This should point to your controllers validation action. In other words, if a user clicks on a link in an email
     * this is the endpoint that it should go to.
     */
    'account_validation_route' => 'auth/validate'

);
 