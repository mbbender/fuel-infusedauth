<?php

namespace Fuel\Migrations;

class Create_users {

	public function up()
	{
		\DBUtil::create_table(
            'users',
            array(
                'id' => array('type' => 'int unsigned', 'auto_increment' => true),
                'username' => array('constraint' => 50, 'type' => 'varchar'),
                'password' => array('constraint' => 255, 'type' => 'varchar'),
                'group' => array('constraint' => 255, 'type' => 'int', 'default'=>1),
                'email' => array('constraint' => 255, 'type' => 'varchar'),
                'last_login' => array('constraint' => 25, 'type' => 'varchar'),
                'login_hash' => array('constraint' => 255, 'type' => 'varchar'),
                'profile_fields' => array('type' => 'text'),
                'created_at' => array('constraint' => 11, 'type' => 'int'),
                'updated_at' => array('constraint' => 11, 'type' => 'int'),
            ),
            array('id'),
            true,
            'InnoDB',
            'utf8_bin'
        );
		
		\DBUtil::create_index('users', array('username','email'), 'email_uname', 'unique');
	}

	public function down()
	{
		\DBUtil::drop_table('users');
	}
}