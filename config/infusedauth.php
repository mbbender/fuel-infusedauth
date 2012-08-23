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
        'linked_redirect' => '/',
        'login_redirect' => '/',
        'register_redirect' => '/',
        'registered_redirect' => '/',
        'logout_redirect' => '/',
        'acccount_validation_required' => 'verify',
        'verify_action' => 'auth/verify' // path to controller that verifies a user account
    ),

    /**
     * DB connection, leave null to use default
     */
    'db_connection' => null,

    /**
     * Puts a block into any view that extends the app
     */
    'beta_wall' => true,
    'beta_group_id' => -5,

    /**
     * Require account validation during registration. This will result in a user being created in the user_temp
     * table first and once the account has been validated via validate() it will migrate the user into the user table.
     * (Requires user_temp table)
     */
    'account_validation' => true,

    /**
     * This determines if we should try to create a user and log them in automatically if they connect through
     * a third party provider like facebook.
     *
     */
     'account_validation_3rdparty' => false,


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
    'account_validation_route' => 'auth/validate',

    'views'=> array(
        'login'=>'login',
        'register'=>'register'
    ),

    /**
     * These fields will be made available as their associated fieldsets in view files. If default views are used,
     * these fields will show up in the scaffolding views that come with the package. See FuelPHP's fieldsets for mroe
     * information on how to define the fields. Edit the form.php config file to change the way the form is rendered.
     */
    'fieldsets'=> array(
        'register'=>array(
            array(
                'required'=>true,
                'name'=>'fullname',
                'label'=>'Full Name',
                'attributes'=>array(),
                'rules'=>array(array('max_length',255), array('required'))
            ),
            array(
                'required'=>true,
                'name'=>'email',
                'label'=>'Email',
                'attributes'=>array(),
                'rules'=>array(array('max_length',255), array('required'), array('valid_email'))
            ),
            array(
                'required'=>true,
                'name'=>'password',
                'label'=>'Password',
                'attributes'=>array('type'=>'password'),
                'rules'=>array(array('max_length',255), array('required'))
            ),
            array(
                'required'=>true,
                'name'=>'password2',
                'label'=>'Password Validation',
                'attributes'=>array('type'=>'password'),
                'rules'=>array(array('max_length',255), array('required'), array('match_field','password'))
            )
        ),
        'login'=>array(
            array(
                'required'=>true,
                'name'=>'email',
                'label'=>'Email',
                'attributes'=>array(),
                'rules'=>array(array('max_length',255), array('required'), array('valid_email'))
            ),
            array(
                'required'=>true,
                'name'=>'password',
                'label'=>'Password',
                'attributes'=>array('type'=>'password'),
                'rules'=>array(array('max_length',255), array('required'))
            )
        )
    ),

    /**
     * These values will be stored as profile fields. The name key will be used to access the profile field value and
     * the form_field_name field must equal the 'name' properity of one of the fieldsets set in fieldsets.register
     */
    'profile_fields' => array(
        array('name'=>'full_name','form_field_name'=>'fullname')
    ),

    /**
     * Will send a notification to the system email address if defined in app config every time a user signs-up.
     */
    'send_user_signup_notification_to_system' => false

);
 