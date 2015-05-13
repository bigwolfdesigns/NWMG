<?php

if(!defined('BASEPATH')) exit('No direct script access allowed');

// ------------------------------------------------------------------------

class search {
	/**
	 * Initialize the default class
	 *
	 * @access	private
	 * @return	void
	 */
	public function __construct(){
		ll('client')->set_initial();
		$task = lc('uri')->get(TASK_KEY, 'search');
		if(method_exists($this, 'web_'.$task)&&is_callable(array($this, 'web_'.$task))){
			ll('display')->assign('task', $task);
			$this->{'web_'.$task}();
		}else{
			ll('display')->assign('task', 'search');
			$this->web_search();
		}
	}
	public function web_search(){
		$query	 = lc('uri')->get('q', '');
		$results = ll('searches')->search(trim($query));
		ll('display')
				->assign('results', $results)
				->show('search');
	}
}
