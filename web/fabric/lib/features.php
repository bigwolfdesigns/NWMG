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
		if($value!==''){
			$return = explode('|',$value);
		}
		return $return;
	}
}
