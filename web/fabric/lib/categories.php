<?php

if(!defined('BASEPATH')){
	exit('No direct script access allowed');
}

class categories extends table_prototype {
	public function __construct(){
		parent::__construct();
		$this->set_table_name('category')->set_auto_lock_in_shared_mode(true);
	}
	public function get_nav_categories(){
		//get all categories where nav == 'y'
		$filters	 = array();
		$filters[]	 = array('field' => 'nav', 'operator' => '=', 'value' => 'y');
		$categories	 = $this->get_raw($filters);
		if(!is_array($categories)){
			$categories = array();
		}
		foreach($categories as $k => $category){
			$cat_id								 = $category['id'];
			$categories[$k]['sub_categories']	 = array();
			//get second level categories
			$sub_categories						 = $this->get_sub_categories($cat_id);
			$categories[$k]['sub_categories']	 = $sub_categories;
		}
		return $categories;
	}
	public function get_sub_categories($parent_id){
		$filters		 = array();
		$filters[]		 = array('field' => 'parent_id', 'operator' => '=', 'value' => $parent_id);
		$sub_categories	 = $this->get_raw($filters);
		if(!is_array($sub_categories)){
			$sub_categories = array();
		}
		return $sub_categories;
	}
	public function get_id_from_alias($alias){
		$filters	 = array();
		$filters[]	 = array('field' => 'alias', 'operator' => '=', 'value' => $alias);
		$ret		 = $this->get_info($filters);
		$return		 = false;
		if(is_array($ret) && isset($ret['id'])){
			$return = $ret['id'];
		}
		return $return;
	}
	public function get_image($cat_id){
		return ll('images')->get_image($cat_id,'category');
	}
	public function get_pages($cat_id){
		return ll('pages')->get_pages($cat_id,'category');
	}
}
