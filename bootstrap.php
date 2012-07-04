<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Michael Bender
 * Date: 6/11/12
 * Time: 6:46 AM
 *
 * Requires Auth to be enabled
 */


Autoloader::add_core_namespace('InfusedAuth');

Autoloader::add_classes(array(
    'InfusedAuth\\Auth_Login_InfusedAuth'      => __DIR__.'/classes/infusedauth.php',
    'InfusedAuth\\SimpleUserValidationException'      => __DIR__.'/classes/infusedauth.php',
    'InfusedAuth\\InfusedAuthException' => __DIR__.'/classes/infusedauth.php',

    'InfusedAuth\\Model_User'      => __DIR__.'/classes/model/user.php',
    'InfusedAuth\\Model_Temp_User'      => __DIR__.'/classes/model/temp_user.php',

    'InfusedAuth\\Controller_Base' => __DIR__.'/classes/controller/base.php',
    'InfusedAuth\\Controller_Admin' => __DIR__.'/classes/controller/admin.php',
    'InfusedAuth\\Controller_Infusedauth' => __DIR__.'/classes/controller/infusedauth.php',
));