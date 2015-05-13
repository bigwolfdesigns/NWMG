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
		$tasks_need_login	 = array('home');
		ll('client')->set_initial();
		$task				 = lc('uri')->get(TASK_KEY, 'login');
		$is_logged			 = ll('users')->is_logged();
		if(method_exists($this, 'web_'.$task)&&is_callable(array($this, 'web_'.$task))&&((!in_array($task, $tasks_need_login))||((in_array($task, $tasks_need_login)&&$is_logged)))){
			ll('display')->assign('task', $task);
			$this->{'web_'.$task}();
		}else{
			ll('display')->assign('task', 'login');
			$this->web_login();
		}
	}
	public function web_login(){
		$is_logged	 = ll('users')->is_logged();
		$redirect	 = true;
		if(!$is_logged){
			$redirect	 = false;
			$errors		 = ll('users')->login_user();
			if(!is_array($errors)&&$errors>0){
				$redirect = true;
				//we're logged in go to control home
			}
		}
		if($redirect){
			fabric::redirect(lc('uri')->create_auto_uri(array(CLASS_KEY => 'control', TASK_KEY => 'home')));
		}
		ll('display')
				->set_hide_show('nav', false)
				->assign('errors', $errors)
				->show('login');
	}
	public function web_logout(){
		$is_logged = ll('users')->is_logged();
		if($is_logged){
			ll('users')->logout();
		}
		fabric::redirect(lc('uri')->create_auto_uri(array(CLASS_KEY => 'control', TASK_KEY => 'login')));
	}
	public function web_home(){
		ll('display')->show('home');
	}
}
