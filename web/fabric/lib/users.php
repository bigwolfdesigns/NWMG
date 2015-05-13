<?php

if(!defined('BASEPATH')){
	exit('No direct script access allowed');
}

// ------------------------------------------------------------------------
class users extends table_prototype {
	private $user		 = array(); //current user
	private $privileges	 = array(); //current user permissions
	private $logged		 = NULL;
	private $admin		 = NULL;
	/**
	 * Initialize the default class
	 *
	 * @access	private
	 * @return	void
	 */
	public function __construct(){
		parent::__construct();
		$this->set_table_name('user')->set_auto_lock_in_shared_mode(true);
		$this->_fill_user_info();
	}
	private function _fill_user_info($reset = false, $user_id = 0){
		if($user_id>0){
			$filters = array(
				array('field' => 'id', 'operator' => '=', 'value' => intval($user_id)),
			);
			$tmp	 = $this->select()->field('*')->where($filters)->do_db()->db->fetch_array($this->last_result());
			if(is_array($tmp)){
				$this->user = $tmp;
			}
		}else{
			if($reset){
				$this->logged = NULL;
			}
			if(is_array(ll('sessions')->get('user_info', false))&&$reset===false){
				//check from sessions:
				$this->user = ll('sessions')->get('user_info', false);
				$this->_fill_user_permissions();
			}elseif(ll('cookies')->get('uid', '')!=''){
				//check from cookies:
				$filters	 = array(
					array('field' => 'id', 'operator' => '=', 'value' => intval(ll('cookies')->get('uid', ''))),
				);
				$this->user	 = $this->select()->field('*')->where($filters)->do_db()->db->fetch_array($this->last_result());
				ll('sessions')->set('user_info', $this->user);
				$this->_fill_user_permissions();
			}elseif($reset){
				$this->user = array();
				ll('sessions')->set('user_info', false);
			}
		}
	}
	private function _fill_user_permissions($user_id = 0){
		if(empty($this->privileges)){
			if(is_array(ll('sessions')->get('user_privileges', false))){
				//check from sessions:
				$this->privileges = ll('sessions')->get('user_privileges', array());
			}else{
				if($user_id<=0){
					if(empty($this->user)){
						if(is_array(ll('sessions')->get('user_info', false))){
							//check from sessions:
							$user_info	 = ll('sessions')->get('user_info', false);
							$user_id	 = $user_info['id'];
						}
					}else{
						$user_id = $this->user['id'];
					}
				}
				$user_privileges = $this->get_all_privileges($user_id);
				if(is_array($user_privileges)){
					foreach($user_privileges as $priv){
						$privilege_name						 = $priv['name'];
						$this->privileges[$privilege_name]	 = true;
					}
				}
				ll('sessions')->set('user_privileges', $this->privileges);
			}
		}
	}
	public function get_all_privileges($user_id){
		$user_id	 = intval($user_id);
		$user_groups = ll('user_groups')->get_groups($user_id);
		$group_tmp	 = array();
		foreach($user_groups as $user_group){
			$group_tmp[] = $user_group['group_id'];
		}
		$groups	 = implode(',', $group_tmp);
		$fields	 = array('privilege.name');
		$join	 = array();
		$join[]	 = array('table' => 'user_privilege', 'how' => "user_privilege.user_id = $user_id");
		if($groups!==''){
			$join[]	 = array('table' => 'group_privilege', 'how' => "group_privilege.group_id IN($groups)");
			$join[]	 = array('table' => 'privilege', 'how' => "group_privilege.privilege_id");
		}
//		$join[]	 = array('table' => 'privilege', 'how' => "user_privilege.privilege_id");
		$privs = $this->get_raw(array(), array(), array(), '', 'privilege', $join, $fields);
		return $privs;
		"SELECT 
			p.id,p.name
			FROM 
			privilege
			LEFT JOIN user_privilege up ON up.user_id = $user_id
			LEFT JOIN group_privilege gp ON gp.group_id IN($groups)
			LEFT JOIN privilege p ON p.id = up.privilege_id
			LEFT JOIN privilege p ON p.id = gp.privilege_id		
		";
	}
	public function is_privileged($privilege = ''){
		$return = false;
		if($privilege!==''){
			$return = isset($this->privileges[$privilege])?true:false;
		}
		return $return;
	}
	public function is_logged($force_check_password = false){
		if(is_null($this->logged)||$force_check_password){
			if(!isset($this->user['emailaddr'])){
				$this->_fill_user_info(true);
			}
			/**
			 * CODE FOR MULTI SERVER - AUTOMATICALLY LOG OUT OR LOG IN BASED ON MAIN SERVER COMMANDS
			 */
//			$last_login	 = ll('sessions')->get('last_login', array('time' => 0, 'customer_id' => 0));
//			$last_logout = ll('sessions')->get('last_logout', 0);
//			$last_action = ($last_login['time'] >= $last_logout)?'in':'out';
//
//			$last_action_for_servers = ll('sessions')->get('last_action', array());
//			$this_server_key		 = md5(lc('uri')->get_domain());
//			$this_server_last_action = isset($last_action_for_servers[$this_server_key])?$last_action_for_servers[$this_server_key]:'';
//			if($this_server_last_action != $last_action){
//				if($last_action == 'in'){
//					$timeout_cookies = 24 * 3600 * 30; //30 days
//					ll('cookies')->set('uid', intval($last_login['customer_id']), $timeout_cookies); //,$sett['domain']);
//				}else{
//					$this->_delete_login_cookies();
//				}
//				$last_action_for_servers[$this_server_key] = $last_action;
//				ll('sessions')->set('last_action', $last_action_for_servers);
//			}
			/**
			 * END CODE FOR MULTI SERVER
			 */
			if(isset($this->user['id'])&&intval(ll('cookies')->get('uid', 0))<=0){
				//$sett			= ll('sessions')->get('_settings');
				$timeout_cookies = 24; //24 Hours
				ll('cookies')->set('uid', intval($this->user['id']), $timeout_cookies); //,$sett['domain']);
			}
			if(isset($this->user['emailaddr'])&&isset($this->user['id'])&&$this->user['id']==ll('cookies')->get('uid', '')){
				$return = true;
			}elseif(ll('cookies')->get('cu', '')!=''&&isset($this->user['id'])){
				//a more secure one
				$return = $this->user['id']==lc('crypt')->decrypt_id(ll('cookies')->get('cu', ''));
//			}elseif(isset($this->user['id'])){
//				$return = $this->user['id']>0;
			}else{
				$return = false;
			}
			if($return){
				if($force_check_password&&ll('cookies')->get('pu', '')==''){
					$return = false;
				}elseif(ll('cookies')->get('pu', '')!=''){
					//is the password correct
					$return = $this->user['password']==lc('crypt')->decrypt_str(ll('cookies')->get('pu', ''));
				}
			}
		}else{
			$return = $this->logged;
		}
		if(!$force_check_password){
			$this->logged = $return;
		}
//		var_dump($return);
		return $return;
	}
	public function check_login($emailaddr, $pws = '', $user_id = NULL){
		$return	 = array();
		$filters = array();
		if(!is_null($user_id)&&$user_id>0){
			$filters[0] = array('field' => 'id', 'operator' => '=', 'value' => $user_id);
		}elseif($emailaddr!=''){
			if(is_numeric($emailaddr)){
				$filters[]	 = array('field' => 'id', 'operator' => '=', 'value' => intval($emailaddr));
				$return[]	 = "No account was found with this user id.";
			}else{
				$filters[0][]	 = array('field' => 'email', 'operator' => 'LIKE', 'value' => $emailaddr);
				$return[]		 = "No account was found with this email address.";
			}
		}
		$i	 = count($filters);
		$tmp = false;
		if($i>0){
			$filters[$i][]	 = array('field' => 'active', 'operator' => '=', 'value' => 'y');
			$order_by		 = array();
			$fields			 = array("id", "password", "email");
			$ttmp			 = $this->set_sql_cache('once')->set_read('once')->get_raw($filters, $order_by, array(), '1', 'user', array(), $fields);
			if(is_array($ttmp)&&isset($ttmp[0]['id'])){
				$tmp = $ttmp[0];
			}else{
				$ttmp = $this->set_sql_cache('once')->get_raw($filters, $order_by, array(), '1', 'user', array(), $fields);
				if(is_array($ttmp)&&isset($ttmp[0]['id'])){
					$tmp = $ttmp[0];
				}
			}
		}
		if(is_array($tmp)&&isset($tmp['id'])){
			/*
			 * True if:
			 * if they are trying to login without a password and their account doesn't have one
			 * if they are succesfully trying to login with email/password
			 */
			$success	 = false;
			$return		 = array();
			$return[]	 = "That user was not found.";
			if(!is_null($user_id)&&$user_id>0){
				$success = true;
			}
			$return		 = array();
			$return[]	 = "You've entered the wrong password";
			if($pws==''||($tmp['password']==''&&$pws=='')||$tmp['password']==md5($pws)){
				$success = true;
			}
			if($success){
				$return = intval($tmp['id']);
			}
		}
		return $return;
	}
	public function login($email, $pws = NULL, $user_id = NULL){
		$user_id = $this->check_login($email, $pws, $user_id);
		if($user_id!==false&&!is_array($user_id)){
			//need to set the cookies
			$timeout_cookies = 24*3600*30; //30 days
			ll('cookies')->set('im', $user_id, $timeout_cookies);
			ll('cookies')->set('uid', $user_id, $timeout_cookies);
			ll('cookies')->set('cu', lc('crypt')->crypt_id($user_id), $timeout_cookies);
			if(!is_null($pws)){
				ll('cookies')->set('pu', lc('crypt')->crypt_str(md5($pws)));
			}else{
				ll('cookies')->delete('pu');
			}
			$last_login = array('time' => time(), 'customer_id' => $user_id);
			ll('sessions')->set('last_login', $last_login);
			$this->_fill_user_info(true);
		}
		return $user_id;
	}
	public function logout(){
		$this->_delete_login_cookies();
		ll('sessions')->reset();
		$this->logged = false;
	}
	public function login_user(){
		$return = false;
		if(lc('uri')->is_post()){
			$return		 = array();
			$return[]	 = "You must pass an email and password.";
			$email		 = trim(lc('uri')->post('email', ''));
			$password	 = trim(lc('uri')->post('password', ''));
			if($email!=''&&$password!=''){
				$return = $this->login($email, $password);
			}
		}
		return $return;
	}
	private function _delete_login_cookies(){
		$keys = array_keys(ll('cookies')->get_all());
		foreach($keys as $key){
			ll('cookies')->delete($key);
		}
		return $this;
	}
}
