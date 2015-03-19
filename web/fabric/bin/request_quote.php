<?php

if(!defined('BASEPATH')){
	exit('No direct script access allowed');
}

class request_quote {
	/**
	 * Initialize the default class
	 *
	 * @access	private
	 * @return	void
	 */
	public function __construct(){
		ll('client')->set_initial();
		$is_logged	 = ll('users')->is_logged();
		$task		 = lc('uri')->get(TASK_KEY, 'quote');
//		if(ll('client')->is_privileged('CAT')){
//			if(((!in_array($task, $tasks_need_login)) || ((in_array($task, $tasks_need_login) && $is_logged)))){
		if(method_exists($this, 'web_'.$task) && is_callable(array($this, 'web_'.$task))){
			ll('display')->assign('task', $task);
			$this->{'web_'.$task}();
		}else{
			ll('display')->assign('task', 'quote');
			$this->web_quote();
		}
//			}else{
//				fabric::redirect('/control/login.html', "You must be logged in to view this page.", 5, true);
//			}
//		}else{
//			fabric::redirect('/control.html', "Insufficient Privileges", 5, true);
//		}
	}
	public function web_quote(){
		//show the contact-us form
		//get the contact-us ecom-page
		$states			 = ll('client')->get_states();
		$countries		 = ll('client')->get_countries();
		$products		 = ll('products')->get_select_list();
		$ecom_page		 = ll('pages')->get_info('request-quote');
		$ecom_content	 = isset($ecom_page['content'])?$ecom_page['content']:'';
		$errors			 = ll('orders')->add_quote();
		if(!is_array($errors) && $errors != false){
			//the contact went through successfully
			fabric::redirect('/request_quote/thank_you.html');
		}
		ll('display')
				->assign('countries', $countries)
				->assign('errors', $errors)
				->assign('states', $states)
				->assign('products', $products)
				->assign('ecom_content', $ecom_content)
				->show('request_quote/quote');
	}
}
