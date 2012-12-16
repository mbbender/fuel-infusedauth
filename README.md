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

Database tables required
Users
sql coming soon...

Users_temp
sql coming soon...

Authentications
sql comming soon...

### Login/Logout/Register
Login
Auth::instance()->login($username_or_email='',$password='');
Auth::instance()->create_user();


## Configuration
### infusedauth.php config file
* Set which fields show up in the registration view and login view
* Set view file locations for all views used (can also override in extended controller for theme support)
* Set social integration configuration options
* Set verification requirements, method and options


## Setup Controllers and Models
Create a controller that extends \InfusedAuth\Controller_InfusedAuth
Create two models that extend \InfusedAuth\Model_User and \InfusedAuth\Model_Temp_User


