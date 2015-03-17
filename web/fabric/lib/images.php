<?php

if(!defined('BASEPATH')){
	exit('No direct script access allowed');
}

class images extends table_prototype {
	public function __construct(){
		parent::__construct();
		$this->set_table_name('image')->set_auto_lock_in_shared_mode(true);
	}
	public function get_image($id, $type = 'image'){
		//returns the path
		$return				 = '/images/image/no-image.png';
		$lib				 = ll('table_prototype');
		$field				 = 'id';
		$skip_initial_query	 = false;
		switch($type){
			case'category':
			case'product':
			case'component':
			case'part':
				$lib->set_table_name($type.'_image');
				$field				 = $type.'_id';
				break;
			case'image':
			default:
				$type				 = 'image';
				$skip_initial_query	 = true;
				break;
		}
		$filters	 = array();
		$filters[]	 = array('field' => $field, 'operator' => '=', 'value' => $id);
		if(!$skip_initial_query){
			$filters[] = array('field' => 'main', 'operator' => '=', 'value' => 'y');
		}
		$_info = $lib->get_info($filters);
		if(is_array($_info) && !empty($_info) || $skip_initial_query){
			$image_id	 = $skip_initial_query?$id:$_info['id'];
		}
		$return		 = "/images/$type/$id.png";
		return $return;
	}
	public function get_images($id, $type = 'image'){
		//returns the info
		$lib				 = ll('table_prototype');
		$field				 = 'id';
		$skip_initial_query	 = false;
		switch($type){
			case'category':
			case'product':
			case'component':
			case'part':
				$lib->set_table_name($type.'_image');
				$field				 = $type.'_id';
				break;
			case'image':
			default:
				$type				 = 'image';
				$skip_initial_query	 = true;
				break;
		}
		$filters	 = array();
		$filters[]	 = array('field' => $field, 'operator' => '=', 'value' => $id);
		$return		 = $lib->get_all($filters);
		if(!is_array($return) || empty($return)){
			$return = array();
		}
		return $return;
	}
	public function upload_file(){
		$return	 = array();
		$json	 = lc('uri')->get('json', false);
		if(lc('uri')->is_post()){
			//re-configure the _FILES array
			$tfiles	 = lc('uri')->file('file', array());
			$files	 = array();
			foreach($tfiles as $prop_name => $file){
				foreach($file as $k => $v){
					$files[$k]	 = (isset($files[$k]))?$files[$k]:array();
					$files[$k]	 = array_merge($files[$k], array($prop_name => $v));
				}
			}
			$temp		 = ll('client')->get('client_template', '');
			$template	 = $temp == ''?'':($temp.'/');
			$path		 = IMAGEPATH.$template.'image/';
			if(!is_dir($path)){
				mkdir($path, 0775, true);
			}
			foreach($files as $file){
				$f_info		 = pathinfo($file['name']);
				$name		 = $f_info['filename'];
				$ext		 = strtolower($f_info['extension']);
				$basename	 = $name.'.'.$ext;
				$target		 = $path.$basename;
				if(move_uploaded_file($file['tmp_name'], $target)){
					//insert into the db
					$name	 = $f_info['filename'];
					$ext	 = strtolower($f_info['extension']);
					$insert	 = array(
						'name'	 => $name,
						'ext'	 => $ext,
						'active' => 'y'
					);
					$id		 = ll('images')->insert()->set($insert)->run()->get_last_inserted_id();
					if($id > 0){
						$return[] = "The file $basename has been uploaded. The file id is: $id";
					}else{
						$return[] = "The file $basename has been uploaded, but not updated in the database.. Please contact your Administrator for assistance.";
					}
				}else{
					$return[] = "Sorry, there was a problem uploading $basename.";
				}
			}
		}
		if($json){
			ll('output')->set_display(ll('display'))->json($return);
		}else{
			return $return;
		}
	}
}
