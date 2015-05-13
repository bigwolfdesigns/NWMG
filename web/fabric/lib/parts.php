<?php

if(!defined('BASEPATH')){
	exit('No direct script access allowed');
}

class parts extends table_prototype {
	public function __construct(){
		parent::__construct();
		$this->set_table_name('part')->set_auto_lock_in_shared_mode(true);
	}
	public function get_image($part_id){
		return ll('limages')->get_image($part_id, 'part');
	}
}
