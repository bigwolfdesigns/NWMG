<?php

if(!defined('BASEPATH')){
	exit('No direct script access allowed');
}

class contact_us {
	/**
	 * Initialize the default class
	 *
	 * @access	private
	 * @return	void
	 */
	public function __construct(){
		ll('client')->set_initial();
		$task = lc('uri')->get(TASK_KEY, 'contact');
		if(method_exists($this, 'web_'.$task) && is_callable(array($this, 'web_'.$task))){
			ll('display')->assign('task', $task);
			$this->{'web_'.$task}();
		}else{
			ll('display')->assign('task', 'contact');
			$this->web_contact();
		}
	}
	public function web_contact(){
		//show the contact-us form
		//get the contact-us ecom-page
		$states			 = ll('client')->get_states();
		$countries		 = ll('client')->get_countries();
		$ecom_page		 = ll('pages')->get_info('contact-us');
		$ecom_content	 = isset($ecom_page['content'])?$ecom_page['content']:'';
		$errors			 = ll('contacts')->add_contact();
		if(!is_array($errors) && $errors != false){
			//the contact went through successfully
			fabric::redirect('/contact/thank_you.html');
		}
		ll('display')
				->assign('countries', $countries)
				->assign('errors', $errors)
				->assign('states', $states)
				->assign('ecom_content', $ecom_content)
				->show('contact/contact_us');
	}
	public function web_thank_you(){
		//get the contact-us thank you ecom-page
		$ecom_page		 = ll('pages')->get_info('contact-us-thank-you');
		$ecom_content	 = isset($ecom_page['content'])?$ecom_page['content']:'';
		ll('display')
				->assign('ecom_content', $ecom_content)
				->show('contact/thank_you');
	}
}
