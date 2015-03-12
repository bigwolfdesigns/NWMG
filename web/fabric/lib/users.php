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
	private function _fill_user_info($user_id = 0, $reset = false){
		if($user_id > 0){
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
			if(is_array(ll('sessions')->get('user_info', false)) && $reset === false){
				//check from sessions:
				$this->user = ll('sessions')->get('user_info', false);
			}elseif(ll('cookies')->get('cid', '') != ''){
				//check from cookies:
				$filters	 = array(
					array('field' => 'id', 'operator' => '=', 'value' => intval(ll('cookies')->get('cid', ''))),
				);
				$this->user	 = $this->select()->field('*')->where($filters)->do_db()->db->fetch_array($this->last_result());
				ll('sessions')->set('user_info', $this->user);
			}elseif($reset){
				$this->user = array();
				ll('sessions')->set('user_info', false);
			}
		}
		$this->_fill_user_permissions();
	}
	private function _fill_user_permissions($user_id = 0){
		if(empty($this->privileges)){
			if(is_array(ll('sessions')->get('user_privileges', false))){
				//check from sessions:
				$this->privileges = ll('sessions')->get('user_privileges', array());
			}else{
				if($user_id <= 0){
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
		if($groups !== ''){
			$join[]	 = array('table' => 'group_privilege', 'how' => "group_privilege.group_id IN($groups)");
			$join[]	 = array('table' => 'privilege', 'how' => "group_privilege.privilege_id");
		}
//		$join[]	 = array('table' => 'privilege', 'how' => "user_privilege.privilege_id");
		$privs	 = $this->get_raw(array(), array(), array(), '', 'privilege', $join, $fields);
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
		if($privilege !== ''){
			$return = isset($this->privileges[$privilege])?true:false;
		}
		return $return;
	}
	public function is_logged(){
		return true;
	}
}
