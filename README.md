# InfusedAuth
A FuelPHP Auth driver package that extends SimpleAuth and integrates NinjAuth to provide
* Seemless social integrations for login and register
* User verification process for non 3rd party accounts via email, sms, or custom methods
* 'Invitation Only' block which will allow you to screen all registered users before granting them access
* Basic view scaffolding to quickly load in registration, login, forgot password, verification in process, and emails.

## Usage
Basic usage is just like using Simple Auth.
### Setup / Configuration
Package dependencies:
* ninjauth
* oauth
* oauth2

Set InfusedAuth as your Auth driver like so in fuel/app/config/auth.php
    'driver' => array('InfusedAuth\\InfusedAuth'),

If you do not already have NinjAuth configured add a fuel/app/config/ninjauth.php and set the providers you want.

#### Database tables required
Users
    CREATE TABLE IF NOT EXISTS `users` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `username` varchar(50) COLLATE utf8_bin NOT NULL,
      `password` varchar(255) COLLATE utf8_bin NOT NULL,
      `group` int(255) NOT NULL DEFAULT '1',
      `email` varchar(255) COLLATE utf8_bin NOT NULL,
      `last_login` varchar(25) COLLATE utf8_bin NOT NULL,
      `login_hash` varchar(255) COLLATE utf8_bin NOT NULL,
      `profile_fields` text COLLATE utf8_bin NOT NULL,
      `created_at` int(11) NOT NULL,
      `updated_at` int(11) NOT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `email_uname` (`username`,`email`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=73 ;

Users_temp
    CREATE TABLE IF NOT EXISTS `users_temp` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `username` varchar(50) COLLATE utf8_bin NOT NULL,
      `password` varchar(255) COLLATE utf8_bin NOT NULL,
      `group` int(255) NOT NULL DEFAULT '1',
      `email` varchar(255) COLLATE utf8_bin NOT NULL,
      `profile_fields` text COLLATE utf8_bin NOT NULL,
      `validation_code1` varchar(10) COLLATE utf8_bin NOT NULL,
      `validation_code2` varchar(10) COLLATE utf8_bin NOT NULL,
      `created_at` int(11) NOT NULL,
      `updated_at` int(11) NOT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `email_uname` (`username`,`email`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=6 ;


Authentications
    CREATE TABLE IF NOT EXISTS `authentications` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `user_id` int(10) unsigned NOT NULL,
      `provider` varchar(50) NOT NULL,
      `uid` varchar(255) NOT NULL,
      `secret` varchar(255) DEFAULT NULL,
      `created_at` int(11) NOT NULL,
      `updated_at` int(11) NOT NULL,
      `access_token` varchar(255) DEFAULT NULL,
      `expires` int(12) DEFAULT '0',
      `refresh_token` varchar(255) DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `user_id` (`user_id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

### Login/Logout/Register
Login
    Auth::instance()->login($username_or_email='',$password='');
    Auth::instance()->create_user($username, $password, $email, $group = 1, Array $profile_fields = array(), $thirdparty=false, $bypass_verification=false);
    Auth::instance()->logout();


## Configuration
### infusedauth.php config file
File is documented for each option
* Set which fields show up in the registration view and login view
* Set view file locations for all views used (can also override in extended controller for theme support)
* Set social integration configuration options
* Set verification requirements, method and options
* Enable/Disable invitation only sign-up

### Defaults
* A SimpleGroup with id of -5 named betablock with a role of block associated to it is created for invitation only
* NinjAuth adapter sent to InfusedAuth
* Account validation enabled requiring email link click to go from a tempuser to a user

## How to Get Started
###Setup Controllers and Models
* Create a controller that extends \InfusedAuth\Controller_InfusedAuth
* Create two models that extend \InfusedAuth\Model_User and \InfusedAuth\Model_Temp_User and make sure they are accessible via \Model_User and \Model_TempUser. You can accomplish this by creating the models in the app root classes directory or by creating another package and in your bootstrap file adding the package namespace to core.

###Customize Views
To customize the views you can override the following methods to return the view output. Data is passed in from the auth_controller:
* public function login_view($data)
* public function registration_view($data)
* public function verification_pending_view($data)
* public function wall_view($data)

####An example
    public function registration_view($data)
    {
        \View::set_global('title','Sign up.');
        \View::set_global('auth_data',$data);
        $this->theme->set_partial('content',\Theme::instance()->view('auth/register')); // Your view
        return \Theme::instance(); // Must return the view content for FuelPHP to render it
    }