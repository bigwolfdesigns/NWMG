<?php

if(!defined('BASEPATH')){
	exit('No direct script access allowed');
}

class pages extends table_prototype {
	public function __construct(){
		parent::__construct();
		$this->set_table_name('page')->set_auto_lock_in_shared_mode(true);
	}
	public function get_pages($id, $type = 'category'){
		$lib = ll('table_prototype');
		switch($type){
			case'product':
				$lib->set_table_name($type.'_pages');
				$field	 = $type.'_id';
				break;
			case'category':
			default:
				$field	 = 'category_id';
				$lib->set_table_name('category_pages');
		}
		$filters	 = array();
		$filters[]	 = array('field' => $field, 'operator' => '=', 'value' => $id);
		$t_pages	 = $lib->get_raw($filters);
		$return		 = array();
		if(is_array($t_pages) && !empty($t_pages)){
			$page_ids = array();
			foreach($t_pages as $page){
				$page_ids[] = $page['page_id'];
			}
			$filters	 = array();
			$filters[]	 = array('field' => 'id', 'operator' => 'IN', 'value' => $page_ids);
			$return		 = $this->get_raw($filters);
			if(!is_array($return) || empty($return)){
				$return = array();
			}
		}
		return $return;
	}
	public function prep_content($content){
		$prepped = '';
		
		return $prepped;
	}
}
