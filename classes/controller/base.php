<?php

namespace InfusedAuth;

class Controller_Base extends \Controller_Hybrid {

	public function before()
	{
        parent::before();
		// Assign current_user to the instance so controllers can use it
        $user_screen_name = \Auth::instance()->get_screen_name();
        $user = \Model_User::find_by_username($user_screen_name);
		$this->current_user = \Auth::instance()->check() ? $user : null;
		// Set a global variable so views can use it
		\View::set_global('current_user', $this->current_user);
        if(!empty($this->current_user)) \View::set_global('profile_fields', $this->current_user->profile_fields());
	}
}