<?php

if(!defined('BASEPATH')){
	exit('No direct script access allowed');
}

class customer {
	/**
	 * Initialize the default class
	 *
	 * @access	private
	 * @return	void
	 */
	public function __construct(){
		$tasks_need_login	 = array('', 'add', 'manage', 'edit', 'delete');
		ll('client')->set_initial();
		$is_logged			 = ll('users')->is_logged();
		$task				 = lc('uri')->get(TASK_KEY, 'manage');
		if(ll('client')->is_privileged('CUST')){
			if(((!in_array($task, $tasks_need_login)) || ((in_array($task, $tasks_need_login) && $is_logged)))){
				if(method_exists($this, 'web_'.$task) && is_callable(array($this, 'web_'.$task))){
					ll('display')->assign('task', $task);
					$this->{'web_'.$task}();
				}else{
					ll('display')->assign('task', 'manage');
					$this->web_manage();
				}
			}else{
				fabric::redirect('/control/login.html', "You must be logged in to view this page.", 5, true);
			}
		}else{
			fabric::redirect('/control.html', "Insufficient Privileges", 5, true);
		}
	}
	public function web_manage(){
		//get all customers
		//list them and click links to edit them
		$config		 = lc('config')->get_and_unload_config('customer');
		$filters	 = ll('display')->get_filter_filters($config);
		$customers	 = ll('customers')->get_all($filters, array(), array(), '', 'customer', array(), array());

		$customer_count = count($customers);
		if($customer_count == 1){
			fabric::redirect('/customer/edit/id/'.$customers[0]['id']);
		}
		ll('display')
				->assign('_config', $config)
				->assign('display_table', 'customer')
				->assign('rows', $customers)
				->assign('row_count', $customer_count)
				->show('list');
	}
	public function web_add(){
		$return	 = ll('customers')->add();
		$errors	 = array();
		if($return !== false){
			if(is_array($return)){
				//we have errors
				$errors = $return;
			}else{
				//we did it!
				fabric::redirect(lc('uri')->create_auto_uri(array(CLASS_KEY => 'customer', TASK_KEY => 'edit', 'id' => $return)));
			}
		}
		$form_url	 = lc('uri')->create_auto_uri(array(CLASS_KEY => 'customer', TASK_KEY => 'add'));
		$config		 = lc('config')->get_and_unload_config('customer');
		ll('display')
				->assign('_config', $config)
				->assign('display_table', 'customer')
				->assign('action', 'add')
				->assign('errors', $errors)
				->assign('form_url', $form_url)
				->show('form');
	}
	public function web_edit(){
		$id = intval(lc('uri')->get('id', 0));
		if($id > 0){
			$return			 = ll('customers')->edit($id);
			$errors			 = array();
			$customer_info	 = ll('customers')->get_info($id);
			$orders			 = ll('customers')->get_customer_orders($id);
			$contacts		 = ll('customers')->get_customer_contacts($id);
			$order_config	 = lc('config')->get_and_unload_config('order');
			$contact_config	 = lc('config')->get_and_unload_config('contact');
			if($return !== false){
				if(is_array($return)){
					//we have errors
					$errors = $return;
				}else{
					//we did it!
					fabric::redirect(lc('uri')->create_auto_uri(array(CLASS_KEY => 'customer', TASK_KEY => 'edit', 'id' => $id)));
				}
			}
			$form_url	 = lc('uri')->create_auto_uri(array(CLASS_KEY => 'customer', TASK_KEY => 'edit', 'id' => $id));
			$config		 = lc('config')->get_and_unload_config('customer');
			ll('display')
					->assign('_config', $config)
					->assign('display_table', 'customer')
					->assign('action', 'edit')
					->assign('errors', $errors)
					->assign('orders', $orders)
					->assign('contacts', $contacts)
					->assign('order_config', $order_config)
					->assign('contact_config', $contact_config)
					->assign('info', $customer_info)
					->assign('id', $id)
					->assign('form_url', $form_url)
					->show('customer/view');
		}
	}
	public function web_delete(){
		$id		 = intval(lc('uri')->get('id', 0));
		$return	 = false;
		if($id > 0){
			if(lc('uri')->post('delete', NULL) != ''){
				$return = ll('customers')->remove($id);
			}
		}
		ll('display')
				->assign('class_key', 'customer')
				->assign('deleted', $return)
				->assign('id', $id)
				->show('delete');
	}
}
