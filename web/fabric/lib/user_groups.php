<?php

if(!defined('BASEPATH')){
	exit('No direct script access allowed');
}

// ------------------------------------------------------------------------
class user_groups extends table_prototype {
	/**
	 * Initialize the default class
	 *
	 * @access	private
	 * @return	void
	 */
	public function __construct(){
		parent::__construct();
		$this->set_table_name('user_group')->set_auto_lock_in_shared_mode(true);
	}
	//Get groups for a specific user
	public function get_groups($user_id = 0){
		$user_id = intval($user_id);
		$return	 = array();
		if($user_id){
			//get _all_groups associated with this user
			$filters	 = array();
			$filters[]	 = array();
			$groups		 = $this->get_raw($filters);
			if(is_array($groups)){
				$return = $groups;
			}
		}
		return $return;
	}
}
