<?php

if(!defined('BASEPATH')){
	exit('No direct script access allowed');
}

class test {
	/**
	 * Initialize the default class
	 *
	 * @access	private
	 * @return	void
	 */
	public function __construct(){
		$task = lc('uri')->get(TASK_KEY, 'test');
		ll('client')->set_initial();
		if(method_exists($this, 'web_'.$task)&&is_callable(array($this, 'web_'.$task))){
			ll('display')->assign('task', $task);
			$this->{'web_'.$task}();
		}else{
			ll('display')->assign('task', 'test');
			$this->web_test();
		}
	}
	public function web_test(){
		var_dump();
		phpinfo();
	}
}
