<?php

if(!defined('BASEPATH')) exit('No direct script access allowed');

class control {
	/**
	 * Initialize the default class
	 *
	 * @access	private
	 * @return	void
	 */
	public function __construct(){
		ll('client')->set_initial();
		$task = lc('uri')->get(TASK_KEY, 'login');
		if(ll('users')->is_logged()){
			if(method_exists($this, 'web_'.$task) && is_callable(array($this, 'web_'.$task))){
				ll('display')->assign('task', $task);
				$this->{'web_'.$task}();
			}else{
				ll('display')->assign('task', 'login');
				$this->web_login();
			}
		}
	}
	public function web_login(){
		$errors = ll('users')->login_user();
		if(!is_array($errors) && $errors > 0){
			//we're logged in go to control home
			fabric::redirect(lc('uri')->create_auto_uri(array(CLASS_KEY => 'control', TASK_KEY => 'home')));
		}
		ll('display')
				->assign('errors', $errors)
				->show('login');
	}
	public function web_home(){
		ll('display')->show('home');
	}
}
