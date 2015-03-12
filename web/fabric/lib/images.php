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
		$return				 = '/images/no-image.png';
		$lib				 = ll('table_prototype');
		$field				 = 'id';
		$skip_initial_query	 = false;
		switch($type){
			case'category':
			case'product':
			case'component':
			case'part':
				$lib->set_table_name($type.'_image');
				$field				 = $type.'_id';
				break;
			case'image':
			default:
				$skip_initial_query	 = true;
				break;
		}
		$filters	 = array();
		$filters[]	 = array('field' => $field, 'operator' => '=', 'value' => $id);
		$filters[]	 = array('field' => 'main', 'operator' => '=', 'value' => 'y');
		$_info		 = $lib->get_info($filters);
		if(is_array($_info) && !empty($_info) || $skip_initial_query){
			$image_id	 = $skip_initial_query?$id:$_info['image_id'];
			$image		 = $this->get_info($image_id);
			if(is_array($image) && !empty($image)){
				$path	 = $image['path'];
				$ext	 = $image['ext'];
				$return	 = "/images/$path.$ext";
			}
		}
		return $return;
	}
}
