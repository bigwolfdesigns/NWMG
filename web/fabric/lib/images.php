<?php

if(!defined('BASEPATH')){
	exit('No direct script access allowed');
}

class images extends table_prototype {
	public function __construct(){
		parent::__construct();
		$this->set_table_name('image')->set_auto_lock_in_shared_mode(true);
	}
	public function get_image($id, $type = 'product'){
		$return	 = '/images/no-image.png';
		$lib	 = ll('table_prototype');
		switch($type){
			case'category':
				$lib->set_table_name($type);
				break;
			case'product':
			default:
				$lib->set_table_name('product');
				break;
		}
		$filters	 = array();
		$filters[]	 = array('field' => 'id', 'operator' => '=', 'value' => $id);
		$filters[]	 = array('field' => 'main', 'operator' => '=', 'value' => 'y');
		$_info		 = $lib->get_info($filters);
		if(is_array($_info) && !empty($_info)){
			$path = $_info['path'];
			$ext = $_info['ext'];
			$return ="/images/$path.$ext";
		}
		return $return;
	}
}
