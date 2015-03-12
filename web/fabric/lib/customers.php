<?php

if(!defined('BASEPATH')){
	exit('No direct script access allowed');
}

class customers extends table_prototype {
	public function __construct(){
		parent::__construct();
		$this->set_table_name('customer')->set_auto_lock_in_shared_mode(true);
	}
	public function get_id_from_email($email){
		$return		 = 0;
		$filters	 = array();
		$filters[]	 = array('field' => 'email', 'operator' => 'LIKE', 'value' => $email);
		$customer	 = $this->get_info($filters);
		if(is_array($customer) && isset($customer['id'])){
			$return = $customer['id'];
		}
		return $return;
	}
}
