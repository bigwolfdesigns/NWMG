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
				$customer_id		 = ll('customers')->get_id_from_email($post['email']);
				$post['customer_id'] = $customer_id;
			}
			$return = $this->add($post);
			if($return === false){
				$return		 = array();
				$return[]	 = "Something went wrong.. Please try again.";
			}
		}
		return $return;
	}
}
