<?php

if(!defined('BASEPATH')){
	exit('No direct script access allowed');
}

class image {
	/**
	 * Initialize the default class
	 *
	 * @access	private
	 * @return	void
	 */
	public function __construct(){
		$tasks_need_login	 = array('', 'add', 'manage', 'edit', 'delete', 'upload');
		ll('client')->set_initial();
		$is_logged			 = ll('users')->is_logged();
		$task				 = lc('uri')->get(TASK_KEY, 'manage');
		if(ll('client')->is_privileged('IMG')){
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
		$config		 = array(); //lc('config')->get_and_unload_config('image');
		$filters	 = ll('display')->get_filter_filters($config);
		$limit		 = ll('display')->get_limit();
		$images		 = ll('images')->get_all($filters, array(), array(), $limit, 'image', array(), array());
		$image_count = count($images);
		if($image_count == 1){
//			fabric::redirect('/image/edit/id/'.$images[0]['id']);
		}
		ll('display')
				->assign('display_table', 'Images')
				->assign('rows', $images)
				->assign('_config', $config)
				->assign('row_count', $image_count)
				->show('images/list');
	}
	public function web_upload(){
		$return = ll('images')->upload_file();
	}
	public function web_add(){
		//for now add using the uplaod feature on the list page
		return false;
	}
	public function web_edit(){
		return false; //we dont want to edit until we can also change the file around which will be a little later
		$id = intval(lc('uri')->get('id', 0));
		if($id > 0){
			$return		 = ll('images')->edit($id);
			$errors		 = array();
			$img_info	 = ll('images')->get_info($id);
			if($return !== false){
				if(is_array($return)){
					//we have errors
					$errors = $return;
				}else{
					//we did it!
					fabric::redirect(lc('uri')->create_auto_uri(array(CLASS_KEY => 'image', TASK_KEY => 'edit', 'id' => $id)));
				}
			}
			$form_url	 = lc('uri')->create_auto_uri(array(CLASS_KEY => 'image', TASK_KEY => 'edit', 'id' => $id));
			$config		 = lc('config')->get_and_unload_config('image');
			ll('display')
					->assign('_config', $config)
					->assign('display_table', 'Image')
					->assign('action', 'edit')
					->assign('errors', $errors)
					->assign('info', $img_info)
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
				$return = ll('images')->remove($id);
			}
		}
		ll('display')
				->assign('class_key', 'image')
				->assign('deleted', $return)
				->assign('id', $id)
				->show('delete');
	}
}
