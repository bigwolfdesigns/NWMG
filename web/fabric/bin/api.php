<?php

if(!defined('BASEPATH')){
	exit('No direct script access allowed');
}

class api {
	/**
	 * Initialize the default class
	 *
	 * @access	private
	 * @return	void
	 */
	public function __construct(){
		ll('client')->set_initial();
		$task = lc('uri')->get(TASK_KEY, '');
		if(method_exists($this, 'web_'.$task) && is_callable(array($this, 'web_'.$task))){
			ll('display')->assign('task', $task);
			$this->{'web_'.$task}();
		}
	}
	public function web_save_related_field(){
		$return			 = array();
		$return['error'][] = "You must pass a valid ID.";
		$id				 = lc('uri')->post('id', 0);
		$value			 = lc('uri')->post('value', NULL);
		$field			 = lc('uri')->post('field', NULL);
		$table			 = lc('uri')->post('table', NULL);
		if($id > 0){
		$return			 = array();
		$return['error'][] = "You must pass a valid value, field and table.";
			$filters	 = array();
			$filters[]	 = array('field' => 'id', 'operator' => '=', 'value' => $id);
			if($value != '' && $field != '' && $table != ''){
				$lib				 = ll('table_prototype');
				$lib->update()->from($table)->where($filters)->set($field, $value)->run();
				$return				 = array();
				$return['success']	 = 'success';
			}
		}
		ll('output')->set_display(ll('display'))->json($return);
	}
}
