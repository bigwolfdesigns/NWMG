<?php

if(!defined('BASEPATH')) exit('No direct script access allowed');

// ------------------------------------------------------------------------

class home {
	/**
	 * Initialize the default class
	 *
	 * @access	private
	 * @return	void
	 */
	public function __construct(){
		ll('client')->set_initial();
		$task = lc('uri')->get(TASK_KEY, 'home');
		if(method_exists($this, 'web_'.$task) && is_callable(array($this, 'web_'.$task))){
			ll('display')->assign('task', $task);
			$this->{'web_'.$task}();
		}else{
			ll('display')->assign('task', 'home');
			$this->web_home();
		}
	}
	public function web_home(){
		ll('display')->show('home');
	}
}
