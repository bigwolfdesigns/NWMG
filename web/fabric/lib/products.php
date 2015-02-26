<?php

if(!defined('BASEPATH')){
	exit('No direct script access allowed');
}

class products extends table_prototype {
	public function __construct(){
		parent::__construct();
		$this->set_table_name('product')->set_auto_lock_in_shared_mode(true);
	}
	public function get_image($product_id){
		return ll('images')->get_image($product_id);
	}
	public function get_products_for_category($cat_id){
		$filters	 = array();
		$filters[]	 = array('field' => 'category_id', 'operator' => '=', 'value' => $cat_id);
		$products	 = $this->get_raw($filters);
		$return		 = array();
		if(is_array($products && !empty($products))){
			$return = $products;
		}
		return $return;
	}
}
