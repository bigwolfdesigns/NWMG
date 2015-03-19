<?php

if(!defined('BASEPATH')) exit('No direct script access allowed');

class navigation {
	/**
	 * Initialize the default class
	 *
	 * @access	private
	 * @return	void
	 */
	public function __construct(){
		ll('client')->set_initial();
		$tasks_need_login	 = array('', 'add', 'manage', 'edit', 'delete');
		$is_logged			 = ll('users')->is_logged();
		$task				 = lc('uri')->get(TASK_KEY, 'nav');
		if(ll('client')->is_privileged('NAV')){
			if(((!in_array($task, $tasks_need_login)) || ((in_array($task, $tasks_need_login) && $is_logged)))){
				if(method_exists($this, 'web_'.$task) && is_callable(array($this, 'web_'.$task))){
					ll('display')->assign('task', $task);
					$this->{'web_'.$task}();
				}else{
					ll('display')->assign('task', 'nav');
					$this->web_nav();
				}
			}else{
				fabric::redirect('/control/login.html', "You must be logged in to view this page.", 5, true);
			}
		}else{
			fabric::redirect('/control.html', "Insufficient Privileges", 5, true);
		}
	}
	public function web_nav(){
		ll('client')->update_nav_options();
		$nav_opts = ll('client')->get_nav_options();
		ll('display')
				->assign('nav_options', $nav_opts)
				->show('navigation/nav');
	}
}
