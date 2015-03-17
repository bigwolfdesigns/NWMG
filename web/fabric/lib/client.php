<?php

if(!defined('BASEPATH')){
	exit('No direct script access allowed');
}

class client extends table_prototype {
	private $config;
	private $privileges	 = array();
	private $info		 = array();
	public function __construct(){
		//IMPORTANT !!! KEEP SESSION BEFORE PARENT CONSTRUCT
		//SESSIONS NEEDS TO BE INITIALIZED BEFORE DATABASE CONNECTION
		ll('sessions')->start();
		parent::__construct();
		$this->file_conf_sites	 = USRPATH.'.config.inc.php';
		//loading configuration
		$config					 = lc('config')->get_and_unload_config('client');
		if(is_array($config)){
			foreach($config as $key => $value){
				$this->config[$key] = $value;
			}
		}
		$this->set_table_name('config')->set_auto_lock_in_shared_mode(true);
		$this->_fill_client_info();
	}
	private function _fill_client_info($force_subsite_id = 0, $force_subsite_alias = ''){
		$force_create_config = $this->reset_info;
		$this->reset_info	 = false;
		if(empty($this->info) || !is_array($this->info)){
			$sess_settings					 = ll('sessions')->get('_settings');
			$host							 = lc('uri')->get_host();
			$cfg							 = array();
			$sess_settings['host']			 = $host;
			$sess_settings['secure_host']	 = $host;
			lc('uri')->set_default_uri_domain($host);
			$filters						 = array();
			$tmps							 = $this->get_raw($filters, array(), array(), '', 'config');
			if(is_array($tmps)){
				foreach($tmps as $tmp){
					if($tmp['privilege']){
						$this->privileges[$tmp['field']] = $tmp['value'];
					}else{
						$cfg[$tmp['field']] = $tmp['value'];
					}
				}
			}
			if(!isset($cfg['template']) || $cfg['template'] == ''){
				$cfg['template'] = 'default';
			}
			$t_task					 = lc('uri')->get(TASK_KEY, 'all');
			$cfg['client_template']	 = $cfg['template'];
			if(isset($this->config['control_classes'][lc('uri')->get(CLASS_KEY, 'home')][$t_task]) || isset($this->config['control_classes'][lc('uri')->get(CLASS_KEY, 'home')]['all'])){
				$cfg['template'] = 'control';
			}
			$cfg['folder_template']		 = TPLPATH.$cfg['template'].'/';
			$cfg['folder_web_template']	 = TPLWEBPATH.$cfg['template'].'/';
			$this->info					 = $cfg;
			$this->_parse_config();
		}
		$_domain = $host;
		if(substr_count($_domain, '.') > 1){
			$_domain = substr($_domain, strpos($_domain, '.'));
		}
		$sess_settings['domain'] = $_domain;
		if(ini_get('session.use_cookies') == 1){
			//no cookies.. no need to store anything in the session as it will be "forgot: it anyway
			ll('sessions')->set('_settings', $sess_settings);
		}
		return $this;
	}
	public function set_initial(){
		$display	 = ll('display');
		$ll_client	 = ll('client');
		$template	 = $this->get('template', 'default');
		if($this->initial_set != true){
			//unify all the CSS and JS
			$uri			 = lc('uri');
			$old_task		 = $uri->get(TASK_KEY, NULL);
			$uri->set(TASK_KEY, 'null');
			//CSS
			$links			 = $display->get_config('link');
			$scripts		 = $display->get_config('script');
			$query_string	 = '';
			if($template == 'control'){
				$query_string	 = '?c=1';
				//add control style and js
				$new			 = array();
				$new['type']	 = 'text/css';
				$new['rel']		 = 'stylesheet';
				$new['title']	 = 'default';
				$new['href']	 = '/css/bootstrap/bootstrap.css'.$query_string;
				$new['media']	 = 'all';
				array_push($links, $new);
				$new			 = array();
				$new['type']	 = 'text/css';
				$new['rel']		 = 'stylesheet';
				$new['title']	 = 'default';
				$new['href']	 = '/css/bootstrap/bootstrap.css.map'.$query_string;
				$new['media']	 = 'all';
				array_push($links, $new);


				$new			 = array();
				$new['type']	 = 'text/css';
				$new['rel']		 = 'stylesheet';
				$new['title']	 = 'default';
				$new['href']	 = '/css/fontawesome/font-awesome.min.css'.$query_string;
				$new['media']	 = 'all';
				array_push($links, $new);

				$new			 = array();
				$new['type']	 = 'text/css';
				$new['rel']		 = 'stylesheet';
				$new['title']	 = 'default';
				$new['href']	 = '/css/sb-admin.css'.$query_string;
				$new['media']	 = 'all';
				array_push($links, $new);
				$new			 = array();
				$new['type']	 = 'text/javascript';
				$new['title']	 = 'default';
				$new['src']		 = '/js/bootstrap/bootstrap.min.js'.$query_string;
				array_push($scripts, $new);

				$new			 = array();
				$new['type']	 = 'text/javascript';
				$new['title']	 = 'default';
				$new['src']		 = '/js/ckeditor/ckeditor.js'.$query_string;
				array_push($scripts, $new);
			}
			$display->set_config('link', $links);
			$display->assign('link', $links);
//			//END CSS
			$display->set_config('script', $scripts);
			$display->assign('script', $scripts);
			$uri->set(TASK_KEY, $old_task);
			//END consolidate the CSS/JS
			//a few things we want to do only once
			$display->set_template($template);
			$display->set_fail_over_template('default');
			$display->assign('title', $this->get('name', ''));
			$ext_file = 'default';
			if($ext_file != '' && (ll('files')->file_exists(TPLPATH.$template.'/css/'.$ext_file.'.css') || ll('files')->file_exists(TPLPATH.'default/css/'.$ext_file.'.css'))){
				$display->add_link('text/css', 'stylesheet', 'default', '/css/'.$ext_file.'.css'.$query_string, 'all');
			}
			if($ext_file != '' && (ll('files')->file_exists(TPLPATH.$template.'/js/'.$ext_file.'.js') || ll('files')->file_exists(TPLPATH.'default/js/'.$ext_file.'.js'))){
				$display->add_script('text/javascript', 'javascript', '/js/'.$ext_file.'.js'.$query_string);
			}
			//if this is not the default template, check for any CSS or JS to append
			$ext_file = 'append_default';
			if($template != 'default'){
				if($ext_file != '' && ll('files')->file_exists(TPLPATH.$template.'/css/'.$ext_file.'.css')){
					$display->add_link('text/css', 'stylesheet', 'default', '/css/'.$ext_file.'.css'.$query_string, 'all');
				}
				if($ext_file != '' && ll('files')->file_exists(TPLPATH.$template.'/js/'.$ext_file.'.js')){
					$display->add_script('text/javascript', 'javascript', '/js/'.$ext_file.'.js'.$query_string);
				}
			}
//			if(ll('files')->file_exists(ll('client')->get_home().'top_left_logo.jpg')){
//				$logo_file = '/images/home/top_left_logo.jpg';
//			}else{
//				$logo_file = '/images/logo_header.gif';
//			}
//			$display->assign('logo_file', $logo_file);
		}
		$ext_file = lc('uri')->get(CLASS_KEY, '');
		if($ext_file != ''){
			if($ext_file != '' && (ll('files')->file_exists(TPLPATH.$template.'/css/'.$ext_file.'.css') || ll('files')->file_exists(TPLPATH.'default/css/'.$ext_file.'.css'))){
				$display->add_link('text/css', 'stylesheet', 'default', '/css/'.$ext_file.'.css'.$query_string, 'all');
			}
			if($ext_file != '' && (ll('files')->file_exists(TPLPATH.$template.'/js/'.$ext_file.'.js') || ll('files')->file_exists(TPLPATH.'default/js/'.$ext_file.'.js'))){
				$display->add_script('text/javascript', 'javascript', '/js/'.$ext_file.'.js'.$query_string);
			}
			//if this is not the default template, check for any CSS or JS to append
			if($template != 'default'){
				$ext_file = 'append_'.$ext_file;
				if($ext_file != '' && ll('files')->file_exists(TPLPATH.$template.'/css/'.$ext_file.'.css')){
					$display->add_link('text/css', 'stylesheet', 'default', '/css/'.$ext_file.'.css'.$query_string, 'all');
				}
				if($ext_file != '' && ll('files')->file_exists(TPLPATH.$template.'/js/'.$ext_file.'.js')){
					$display->add_script('text/javascript', 'javascript', '/js/'.$ext_file.'.js'.$query_string);
				}
			}
		}
		$this->initial_set = true;
		$display->assign('class', lc('uri')->get(CLASS_KEY, ''));
		return true;
	}
	public function show_top_menu(){
		$top_nav_bars	 = $this->get_nav_options('top_menu');
		$return			 = ll('display')->grab('top_menu', array('top_nav_bars' => $top_nav_bars['top_menu']));
		return $return;
	}
	public function show_banner(){
		$bnumber		 = date('w');
		$banner_image	 = "/images/banner-i$bnumber.png";
		$return			 = ll('display')->grab('banner', array('banner_image' => $banner_image));
		return $return;
	}
	public function show_nav_menu(){
		//get all categories for left nav
		$categories	 = ll('categories')->get_nav_categories();
		$return		 = ll('display')->grab('left_nav', array('categories' => $categories));
		return $return;
	}
	public function show_footer_links(){
		$footer_links	 = array(
			array('page' => '', 'name' => 'Home'),
			array('page' => 'about_us', 'name' => 'About Us'),
			array('page' => 'products', 'name' => 'Products'),
			array('page' => 'services', 'name' => 'Services'),
			array('page' => 'frequently-asked-questions', 'name' => 'FAQ'),
			array('page' => 'request-quote', 'name' => 'Request Quote'),
			array('page' => 'engineering-request', 'name' => 'Engineering'),
			array('page' => 'contact_us', 'name' => 'Contact Us'),
			array('page' => 'approved_vendors', 'name' => 'Approved Vendors'),
			array('page' => 'industries_served', 'name' => 'Our Industries'),
			array('page' => 'privacy_policy', 'name' => 'Privacy Policy'),
		);
		$return			 = ll('display')->grab('footer_links', array('footer_links' => $footer_links));
		return $return;
	}
	public function get_nav_options($var = 'all'){
		$top_menu					 = array('top_menu' => $this->get('top_menu', array()));
		usort($top_menu['top_menu'], array('client', '_sort_top_menus'));
		$left_nav					 = array('left_nav' => $this->get('left_nav', array()));
		$banner_img					 = array('banner_img' => $this->get('banner_img', array()));
		$footer_links				 = array('footer_links' => $this->get('footer_links', array()));
		$nav_opts					 = array();
		$nav_opts['top_menu']		 = $top_menu;
		$nav_opts['left_nav']		 = $left_nav;
		$nav_opts['banner_image']	 = $banner_img;
		$nav_opts['footer_links']	 = $footer_links;
		$return						 = $nav_opts;
		if($var != 'all' && isset($return[$var])){
			$return = $return[$var];
		}
		return $return;
	}
	private static function _sort_top_menus($a, $b){
		return $a['sort'] - $b['sort'];
	}
	public function get_privileges(){
		$permissions = array(
			array(
				'name'				 => 'Contact Resource Manager',
				'icon'				 => 'group',
				'uri'				 => lc('uri')->create_uri(array(CLASS_KEY => 'crm')),
				'second_level_links' => array(),
				'is_privileged'		 => $this->is_privileged('CRM')
			),
			array(
				'name'				 => 'Category Management',
				'icon'				 => 'list',
				'uri'				 => lc('uri')->create_uri(array(CLASS_KEY => 'category', TASK_KEY => 'manage')),
				'second_level_links' => array(),
				'is_privileged'		 => $this->is_privileged('CAT')
			),
			array(
				'name'				 => 'Product Management',
				'icon'				 => 'list',
				'uri'				 => lc('uri')->create_uri(array(CLASS_KEY => 'product', TASK_KEY => 'manage')),
				'second_level_links' => array(),
				'is_privileged'		 => $this->is_privileged('PROD')
			),
			array(
				'name'				 => 'Feature Management',
				'icon'				 => 'cogs',
				'uri'				 => lc('uri')->create_uri(array(CLASS_KEY => 'feature', TASK_KEY => 'manage')),
				'second_level_links' => array(),
				'is_privileged'		 => $this->is_privileged('FEAT')
			),
			array(
				'name'				 => 'Image Management',
				'icon'				 => 'image',
				'uri'				 => lc('uri')->create_uri(array(CLASS_KEY => 'image', TASK_KEY => 'manage')),
				'second_level_links' => array(),
				'is_privileged'		 => $this->is_privileged('IMG')
			)
		);
		return $permissions;
	}
	public function show_control_nav(){
		$permissions = $this->get_privileges();
		$client_name = $this->get('name', 'CLIENT');
		$return		 = ll('display')->grab('nav', array('permissions' => $permissions, 'client_name' => $client_name));
		return $return;
	}
	public function show_control_home(){
		$permissions = $this->get_privileges();
		$return		 = ll('display')->grab('home_links', array('permissions' => $permissions));
		return $return;
	}
	public function update_nav_options(){
		if(lc('uri')->is_post()){
			//do the dirty work
			$top_menu_navs = json_decode(lc('uri')->post('top_menu_json', '[]'), true);
			if(is_array($top_menu_navs) && !empty($top_menu_navs)){
				//get the original_top_menu_navs
				$original_top_menu_navs = $this->get('top_menu', array());
				if(is_array($original_top_menu_navs) && count($original_top_menu_navs) > 0){
					//Basically what's happening here is we're changing the sort ordfer on all existing
					//top menu links by updating what's given back to us in the post.
					usort($original_top_menu_navs, array('client', '_sort_top_menus'));
					foreach($top_menu_navs as $k => $sort_order){
						$original_top_menu_navs[$sort_order - 1]['sort'] = $k + 1;
					}
					usort($original_top_menu_navs, array('client', '_sort_top_menus'));
					$update_top_menu = json_encode($original_top_menu_navs);
					$filters		 = array();
					$filters[]		 = array('field' => 'field', 'operator' => '=', 'value' => 'top_menu');
					$this->update()->set('value', $update_top_menu)->where($filters)->do_db();
					$this->_set('top_menu', $original_top_menu_navs);
				}else{
					//error there were no original top navs... how'd you get here??
				}
			}
			$top_menu_nav_edits = lc('uri')->post('edit_top_menu_json', NULL);
			if(!is_null($top_menu_nav_edits)){
				$top_menu_nav_edits_decoded = json_decode($top_menu_nav_edits, true);
				foreach(array_keys($top_menu_nav_edits_decoded) as $k){
					$top_menu_nav_edits_decoded[$k]['sort'] = ($k + 1);
				}
				$filters	 = array();
				$filters[]	 = array('field' => 'field', 'operator' => '=', 'value' => 'top_menu');
				$this->update()->set('value', json_encode($top_menu_nav_edits_decoded))->where($filters)->do_db();
				$this->_set('top_menu', $top_menu_nav_edits_decoded);
			}
			//really all we need to do is save it in the db.. js has already done everything for us.
		}
	}
	public function get($field = '', $default = NULL){
		if($field == '' || is_array($field)){
			$return = $this->info;
		}else{
			$return = isset($this->info[$field])?$this->info[$field]:$default;
		}
		return $return;
	}
	private function _set($field = '', $value = NULL){
		$this->info[$field] = $value;
	}
	private function _parse_config(){
		$json_encoded_vars = array('top_menu', 'left_nav', 'footer_links');
		foreach($json_encoded_vars as $var){
			$this->_set($var, json_decode($this->get($var, '{}'), true));
		}
	}
	public function is_privileged($client_privilege){
		return (isset($this->privileges[$client_privilege]) && $this->privileges[$client_privilege])?true:false;
	}
	public function get_states(){
		$states	 = ll('table_prototype')->get_raw(array(), array(), array(), '', 'state');
		$return	 = array();
		foreach($states as $state){
			$return [$state['id']] = $state;
		}
		return $return;
	}
	public function get_countries(){
		$countries	 = ll('table_prototype')->get_raw(array(), array(), array(), '', 'country');
		$return		 = array();
		foreach($countries as $country){
			$return [$country['id']] = $country;
		}
		return $return;
	}
	public function coming_soon(){
		$return = false;
		if(lc('uri')->is_post()){
			$email	 = trim(lc('uri')->post('email', NULL));
			$return	 = "You must submit an email to be notified....";
			if($email != ''){
				$return = "We're sorry that doesn't seem to be a valid email...";
				if(ll('verification')->email($email)){
					ll('email')->AddAddress(ll('client')->get('contact_email', ll('client')->get('smtp_user')));
					ll('email')->Subject = "A customer has requested you to stay in contact with them, from your website.";
					ll('email')->MsgHTML("The customer email address is $email.");
					if(ll('email')->Send()){
						$return = "We will be sure to keep in contact with you via this email <strong>$email!</strong>";
					}else{
						$return = "We're sorry something went wrong.. Please try again...";
					}
				}
			}
		}
		return $return;
	}
}
