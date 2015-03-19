<?php

if(!defined('BASEPATH')){
	exit('No direct script access allowed');
}

class order_items extends table_prototype {
	public function __construct(){
		parent::__construct();
		$this->set_table_name('order_item')->set_auto_lock_in_shared_mode(true);
	}
	public function add_order_item($order_id, $params){
		lc('uri')->set_post('order_id', $order_id);
		foreach($params as $k => $value){
			lc('uri')->set_post($k, $value);
		}
		return $this->add();
	}
	public function add($config = 'order_item'){
		$return = false;
		if(lc('uri')->is_post()){
			$return = parent::add($config);
		}
		return $return;
	}
	public function edit($id, $config = 'order_item'){
		$return = false;
		if(lc('uri')->is_post() && $id > 0){
			$return = parent::edit($id, $config);
		}
		return $return;
	}
}
