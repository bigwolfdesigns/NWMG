<?php

if(!defined('BASEPATH')) exit('No direct script access allowed');

// ------------------------------------------------------------------------

class home {
	/**
	 * Initialize the default class
	 *
	 * @access	private
	 * @return	void
	 */
	public function __construct(){
		ll('client')->set_initial();
		$task = lc('uri')->get(TASK_KEY, 'home');
		if(method_exists($this, 'web_'.$task)&&is_callable(array($this, 'web_'.$task))){
			ll('display')->assign('task', $task)
					->set_hide_show('nav', false);
			$this->{'web_'.$task}();
		}else{
			ll('display')->assign('task', 'home')
					->set_hide_show('nav', false);
			$this->web_home();
		}
	}
	public function web_home(){
		//get all the home categories
		//get all the products for those categories
		//assign them to the page
		$categories		 = ll('categories')->get_home_categories();
		$coming_soon_url = lc('uri')->create_auto_uri(array(CLASS_KEY => 'home', TASK_KEY => 'coming_soon'));
		ll('display')
				->assign('categories', $categories)
				->assign('coming_soon_url', $coming_soon_url)
				->assign('site_name', ll('client')->get('name', 'This Website'))
				->assign('site_tagline', ll('client')->get('tagline', 'test'))
				->show('home');
	}
	public function web_coming_soon(){
		$coming_soon_url = lc('uri')->create_auto_uri(array(CLASS_KEY => 'home', TASK_KEY => 'coming_soon'));
		$message		 = ll('client')->coming_soon();
		ll('display')
				->assign('coming_soon_url', $coming_soon_url)
				->assign('site_name', ll('client')->get('name', 'This Website'))
				->assign('site_tagline', ll('client')->get('tagline', 'test'))
				->assign('message', $message)
				->show('home');
	}
	public function web_temp(){
		$categories		 = ll('categories')->get_home_categories();
		$coming_soon_url = lc('uri')->create_auto_uri(array(CLASS_KEY => 'home', TASK_KEY => 'coming_soon'));
		ll('display')
				->assign('categories', $categories)
				->assign('coming_soon_url', $coming_soon_url)
				->show('home_temp');
	}
	public function web_template(){
		$tpl = lc('uri')->get('tpl', 'max_tmp');
		ll('cookies')->set('template', $tpl, 60*60*24);
		fabric::redirect('/');
	}
	public function web_remove_template(){
		ll('cookies')->delete('template');
		fabric::redirect('/');
	}
}
