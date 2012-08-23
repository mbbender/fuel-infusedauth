<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Michael Bender
 * Date: 6/11/12
 * Time: 6:46 AM
 *
 * Requires Auth to be enabled
 */


//Autoloader::add_core_namespace('InfusedAuth');

\Fuel\Core\Autoloader::add_namespace('InfusedAuth', __DIR__.'/classes/');

Autoloader::add_classes(array(
    'NinjAuth\\Adapter_InfusedAuth' => __DIR__.'/classes/adapter/infusedauth.php',
    'InfusedAuth\\Model_User' => __DIR__.'/classes/model/user.php',
    'InfusedAuth\\Model_TempUser' => __DIR__.'/classes/model/tempuser.php'
));

if(!class_exists('Model_User')) Autoloader::alias_to_namespace('InfusedAuth\\Model_User');
if(!class_exists('Model_TempUser')) Autoloader::alias_to_namespace('InfusedAuth\\Model_TempUser');


