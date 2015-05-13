<?php

if(!defined('BASEPATH')){
	exit('No direct script access allowed');
}

class orders extends table_prototype {
	public function __construct(){
		parent::__construct();
		$this->set_table_name('order')->set_auto_lock_in_shared_mode(true);
	}
	public function add_quote(){
		$return = false;
		if(lc('uri')->is_post()){
			// extract email, check for customer_id,
			$customer_id = 0;
			$email		 = lc('uri')->post('email', '');
			if($email != ''){
				$customer_id = ll('customers')->get_id_from_email($email);
				if($customer_id <= 0){
					//create new customer from email
					$return		 = array();
					$return[]	 = "You must enter a valid Email Address.";
					if(ll('verification')->email($email)){
						$customer_id = ll('customers')->register($email);
					}
				}
				lc('uri')->set_post('customer_id', $customer_id);
			}
//			var_dump(lc('uri')->post());die();
			if($customer_id > 0){
//				var_dump(lc('uri')->post());
//				die();
				$return = $this->add('order');
				if($return === false){
					$return		 = array();
					$return[]	 = "Something went wrong.. Please try again.";
				}elseif($return > 0){
					//yay it worked
					$products	 = lc('uri')->post('products', array());
					$qty_post	 = lc('uri')->post('qtys', array());
					$quantities	 = is_array($qty_post)?$qty_post:array();
					if(is_array($products)){
						foreach($products as $k => $product_id){
							if($product_id > 0){
								$qty					 = intval(isset($quantities[$k]) && $quantities[$k] != ''?$quantities[$k]:1);
								$params					 = array();
								$params['product_id']	 = $product_id;
								$params['qty']			 = $qty;
								ll('order_items')->add_order_item($return, $params);
							}
						}
					}
					$order_comment_post	 = lc('uri')->post('order_comments', array());
					$order_comments		 = is_array($order_comment_post)?$order_comment_post:array();
					foreach($order_comments as $order_comment){
						if(trim($order_comment) != ''){
							$params				 = array();
							$params['comment']	 = $order_comment;
							ll('order_comments')->add_order_comment($return, $params);
						}
					}
					ll('client')->inform('quote', lc('uri')->post());
				}
			}else{
				$return		 = array();
				$return[]	 = "No account found... You must enter an email address.";
			}
		}
		return $return;
	}
	public function add($config = 'order'){
		$return = false;
		if(lc('uri')->is_post()){
			$return = parent::add($config);
		}
		return $return;
	}
	public function edit($id, $config = 'order'){
		$return = false;
		if(lc('uri')->is_post() && $id > 0){
			$return = parent::edit($id, $config);
		}
		return $return;
	}
}
