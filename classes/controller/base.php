<?php

namespace InfusedAuth;

class Controller_Base extends \Controller_Hybrid {

	public function before()
	{
		parent::before();
		
		// Assign current_user to the instance so controllers can use it
		$this->current_user = \Auth::instance()->check() ? Model_User::find_by_username(\Auth::instance()->get_screen_name()) : null;
		
		// Set a global variable so views can use it
		\View::set_global('current_user', $this->current_user);
	}

}