<?php

if(!defined('BASEPATH')){
	exit('No direct script access allowed');
}

class contacts extends table_prototype {
	protected $fields;
	public function __construct(){
		parent::__construct();
		$this->set_table_name('contact')->set_auto_lock_in_shared_mode(true);
		$config = lc('config')->get_and_unload_config('contact');
		if(is_array($config)){
			foreach($config as $key => $value){
				$this->fields[$key] = $value;
			}
		}
	}
	public function add_contact(){
		//$return is either the errors or true is the form passes or empty array if not submitted
		$return = array();
		if(lc('uri')->is_post()){
			$post = lc('uri')->post();
			if(isset($post['email']) && !empty($post['email'])){
				//get customer_id for this email
				$customer_id = ll('customers')->get_id_from_email($post['email']);
				if($customer_id <= 0){
					//create new customer from email
					$customer_id = ll('customers')->register(lc('uri')->is_post('email'));
				}
				lc('uri')->set_post('customer_id', $customer_id);
			}
			$return = $this->add('contact');
			ll('client')->inform('contact', lc('uri')->post());
			if($return === false){
				$return		 = array();
				$return[]	 = "Something went wrong.. Please try again.";
			}
		}
		return $return;
	}
	public function add($config = 'contact'){
		$return = false;
		if(lc('uri')->is_post()){
			$return = parent::add($config);
		}
		return $return;
	}
	public function edit($id, $config = 'contact'){
		$return = false;
		if(lc('uri')->is_post() && $id > 0){
			$return = parent::edit($id, $config);
		}
		return $return;
	}
}
