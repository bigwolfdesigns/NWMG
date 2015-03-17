<?php

if(!defined('BASEPATH')){
	exit('No direct script access allowed');
}

class features extends table_prototype {
	public function __construct(){
		parent::__construct();
		$this->set_table_name('feature')->set_auto_lock_in_shared_mode(true);
	}
	public function parse_value($value){
		$return = array();
		if($value !== ''){
			$return = explode('|', $value);
		}
		return $return;
	}
	public function get_features($id, $type = 'feature'){
		//returns the info
		$lib				 = ll('table_prototype');
		$field				 = 'id';
		$skip_initial_query	 = false;
		switch($type){
			case'category':
			case'product':
			case'component':
			case'part':
				$lib->set_table_name($type.'_feature');
				$field				 = $type.'_id';
				break;
			case'feature':
			default:
				$type				 = 'feature';
				$skip_initial_query	 = true;
				break;
		}
		$filters	 = array();
		$filters[]	 = array('field' => $field, 'operator' => '=', 'value' => $id);
		$return		 = $lib->get_all($filters);
		if(!is_array($return) || empty($return)){
			$return = array();
		}
		return $return;
	}
	public function add($config = 'feature'){
		$return = false;
		if(lc('uri')->is_post()){
			$return = parent::add($config);
		}
		return $return;
	}
	public function edit($id, $config = 'feature'){
		$return = false;
		if(lc('uri')->is_post() && $id > 0){
			$return = parent::edit($id, $config);
		}
		return $return;
	}
}
