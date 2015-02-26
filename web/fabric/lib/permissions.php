<?php

if(!defined('BASEPATH')) exit('No direct script access allowed');

// ------------------------------------------------------------------------
class permissions extends table_prototype {
	private $user		 = array(); //current user
	private $perm_item	 = array();
	private $logged		 = NULL;
	private $root		 = NULL;
	private $admin		 = NULL;
	protected $customers = array();
	/**
	 * Initialize the default class
	 *
	 * @access	private
	 * @return	void
	 */
	public function __construct(){
		parent::__construct();

		$this->set_table_name('customer')->set_auto_lock_in_shared_mode(true);
		$this->_fill_user_info();
	}
	private function _fill_user_info($reset = false, $customer_id = 0){
		if($customer_id > 0){
			$filters = array(
				array('field' => 'id', 'operator' => '=', 'value' => intval($customer_id)),
			);
			$tmp	 = $this->select()->field('*')->where($filters)->do_db()->db->fetch_array($this->last_result());
			return $tmp;
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
	}}
