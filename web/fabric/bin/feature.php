<?php

if(!defined('BASEPATH')){
	exit('No direct script access allowed');
}

class feature {
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
		if(ll('client')->is_privileged('FEAT')){
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
		//get all features
		//list them and click links to edit them
		$config			 = lc('config')->get_and_unload_config('feature');
		$filters		 = ll('display')->get_filter_filters($config);
		$features		 = ll('features')->get_all($filters, array(), array(), '', 'feature', array(), array());
		$feature_count	 = count($features);
		if($feature_count == 1 &&lc('uri')->get('filter_submit','')!=''){
			fabric::redirect('/feature/edit/id/'.$features[0]['id']);
		}
		ll('display')
				->assign('_config', $config)
				->assign('display_table', 'Feature')
				->assign('rows', $features)
				->assign('row_count', $feature_count)
				->show('list');
	}
	public function web_add(){
		$return	 = ll('features')->add();
		$errors	 = array();
		if($return !== false){
			if(is_array($return)){
				//we have errors
				$errors = $return;
			}else{
				//we did it!
				fabric::redirect(lc('uri')->create_auto_uri(array(CLASS_KEY => 'feature', TASK_KEY => 'edit', 'id' => $return)));
			}
		}
		$form_url	 = lc('uri')->create_auto_uri(array(CLASS_KEY => 'feature', TASK_KEY => 'add'));
		$config		 = lc('config')->get_and_unload_config('feature');
		ll('display')
				->assign('_config', $config)
				->assign('display_table', 'Feature')
				->assign('action', 'add')
				->assign('errors', $errors)
				->assign('form_url', $form_url)
				->show('form');
	}
	public function web_edit(){
		$id = intval(lc('uri')->get('id', 0));
		if($id > 0){
			$return			 = ll('features')->edit($id);
			$errors			 = array();
			$feature_info	 = ll('features')->get_info($id);
			if($return !== false){
				if(is_array($return)){
					//we have errors
					$errors = $return;
				}else{
					//we did it!
					fabric::redirect(lc('uri')->create_auto_uri(array(CLASS_KEY => 'feature', TASK_KEY => 'edit', 'id' => $id)));
				}
			}
			$form_url	 = lc('uri')->create_auto_uri(array(CLASS_KEY => 'feature', TASK_KEY => 'edit', 'id' => $id));
			$config		 = lc('config')->get_and_unload_config('feature');
			ll('display')
					->assign('_config', $config)
					->assign('display_table', 'Feature')
					->assign('action', 'edit')
					->assign('errors', $errors)
					->assign('info', $feature_info)
					->assign('id', $id)
					->assign('form_url', $form_url)
					->show('form');
		}
	}
	public function web_delete(){
		$id		 = intval(lc('uri')->get('id', 0));
		$return	 = false;
		if($id > 0){
			if(lc('uri')->post('delete', NULL) != ''){
				$return = ll('features')->remove($id);
			}
		}
		ll('display')
				->assign('class_key', 'feature')
				->assign('deleted', $return)
				->assign('id', $id)
				->show('delete');
	}
}
