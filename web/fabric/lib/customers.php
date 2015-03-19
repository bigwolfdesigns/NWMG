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
		$email		 = strtolower($email);
		$return		 = 0;
		$filters	 = array();
		$filters[]	 = array('field' => 'email', 'operator' => 'LIKE', 'value' => $email);
		$customer	 = $this->get_info($filters);
		if(is_array($customer) && isset($customer['id'])){
			$return = $customer['id'];
		}
		return $return;
	}
	public function register($email, $password = ''){
		$customer_id = 0;
		$email		 = strtolower(trim($email));
		//generating a random passowrd
		//10 chars should be enough
		$ret		 = $this->insert()
						->set('email', $email)
						->set('password', $password != ''?md5($password):'')
						->set('date_registered', 'NOW()', true)
						->set('active', 'y')->run();
		if($ret){
			$customer_id = intval($this->db->insert_id());
		}
		return $customer_id;
	}
}
