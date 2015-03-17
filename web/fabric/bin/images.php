<?php

if(!defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Created on May 5, 2010 by fabrizio
 *
 */

// ------------------------------------------------------------------------

class images {
	private $cache_folder	 = '';
	private $already_sent	 = false;
	/**
	 * Initialize the default class
	 *
	 * @access	private
	 * @return	void
	 */
	public function __construct(){
		ini_set('session.use_cookies', '0');
		$task = lc('uri')->get(TASK_KEY, 'common');
		if(!method_exists($this, 'web_'.$task) || !is_callable(array($this, 'web_'.$task))){
			$task = 'common';
		}
		$this->{'web_'.$task}();
	}
	/**
	 * Send the image to the browser
	 */
	private function send_image($file_cache, $file_type = 'png', $expires = 604800){
		/**
		 * CLOSING DATABASE
		 */
		ll('table_prototype')->close();
		/**
		 * END CLOSING DATABASE
		 */
		if(!$this->already_sent){
			$this->already_sent = true;

			$is_304				 = false;
			$now_gmt			 = date('D, d M Y H:i:s T', time());
			$exp_gmt			 = date('D, d M Y H:i:s T', time() + $expires);
			$error				 = false;
			$final_file_cache	 = $file_cache;
			if(ll('files')->exists($final_file_cache, false)){
				$file_stat	 = ll('files')->stat($final_file_cache);
				$mod_gmt	 = date('D, d M Y H:i:s T', $file_stat['mtime']);
				if($file_type == 'jpg'){
					$file_type = 'jpeg';
				}
				if(ll('files')->exists($final_file_cache, false)){
					$t_stat				 = ll('files')->stat($final_file_cache);
					$file_stat['size']	 = $t_stat['size'];
					$etag				 = md5($file_stat['size'].'-'.$file_stat['mtime'].'-'.$final_file_cache);
					$etag				 = substr($etag, 0, 4).'-'.substr($etag.$etag, 5, 13);
				}else{
					$error = true;
				}
			}else{
				$error = true;
			}
			if(!$error && ll('files')->exists($final_file_cache, false)){
				$file_type == 'svg' && $file_type	 = 'svg+xml';
				$file_type == 'ico' && $file_type	 = 'x-icon';
				header('Date: '.$now_gmt);
//				header('Age: '.$expires);
				header('ETag: '.$etag);
				header('Pragma: public');
				header('Content-type: image/'.$file_type);
				header('Last-Modified: '.$mod_gmt);
				header('Cache-Control: public, max-age='.$expires);
				header('Expires: '.$exp_gmt);
				header('Vary: Accept-Encoding');
				header('Proxy-Connection: keep-alive');
				header('Access-Control-Allow-Origin:*');
				if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) || isset($_SERVER['HTTP_IF_NONE_MATCH'])){
					// parse header
					$if_modified_since	 = '';
					$if_none_match		 = '';
					if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])){
						$if_modified_since = preg_replace('/;.*$/', '', $_SERVER['HTTP_IF_MODIFIED_SINCE']);
					}
					if(isset($_SERVER['HTTP_IF_NONE_MATCH'])){
						$if_none_match = $_SERVER['HTTP_IF_NONE_MATCH'];
					}
					if($if_modified_since == $mod_gmt || $if_none_match == $etag){
						header($_SERVER['SERVER_PROTOCOL'].' 304 Not Modified');
						// the browser's cache is still up to date
						$is_304 = true;
					}
				}
				if(!$is_304){
					$fh = fopen($final_file_cache, 'r');
					while(!feof($fh) && !connection_aborted()){
						// send the current file part to the browser
						echo fread($fh, round(32 * 1024));
						flush();
					}
					fclose($fh);
				}
			}else{
				//			header('X-Info: '.$file_cache);
				header('Date: '.$now_gmt);
				header('Expires: '.$now_gmt);
				header('Last-Modified: '.$now_gmt);
				header('Access-Control-Allow-Origin:*');
				echo 'File Not Found... Try refreshing';
			}
		}
		exit(1);
	}
	//this display one of the standard images that are found into /tpl/img
	//it is a function to make it easier to load those images from javascript/css
	//it might be expanded in the future to include also templates specific files
	public function web_load(){
		$this->common();
	}
	/**
	 * this display one of the standard images that are found into /tpl/img
	 * it is a function to make it easier to load those images from javascript/css
	 */
	public function web_common(){
		//get_num(4) because: 0 = CLASS_KEY, 1=CLASS, 2=TASK_KEY, 3=TASK, 4=remainder
		$file = str_replace('|', DIRECTORY_SEPARATOR, lc('uri')->get('f', ''));
		if($file == ''){
			$tmps = lc('uri')->get_num();
			foreach($tmps as $k => $tmp){
				if($k >= 3 && $tmp != ''){
					$file .= $tmp.DIRECTORY_SEPARATOR;
				}
			}
		}
		if(substr($file, -1) == DIRECTORY_SEPARATOR){
			$file = substr($file, 0, -1);
		}
		$file		 = str_replace(array('..', '|'), array('', DIRECTORY_SEPARATOR), $file);
		$template	 = ll('store')->get('template', 'default');
		$allowedExt	 = array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'ico');
		$file_cache	 = TPLPATH.$template.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.$file;
		foreach($allowedExt as $ext){
			if(ll('files')->exists($file_cache.'.'.$ext)){
				$file_cache .= '.'.$ext;
				break;
			}
		}
		if(!ll('files')->exists($file_cache)){
			$file_cache = TPLPATH.'default'.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.$file;
			foreach($allowedExt as $ext){
				if(ll('files')->exists($file_cache.'.'.$ext)){
					$file_cache .= '.'.$ext;
					break;
				}
			}
		}
		if(!ll('files')->exists($file_cache)){
			$file_cache = TPLPATH.'img'.DIRECTORY_SEPARATOR.$file;
			foreach($allowedExt as $ext){
				if(ll('files')->exists($file_cache.'.'.$ext)){
					$file_cache .= '.'.$ext;
					break;
				}
			}
		}
		if(!ll('files')->exists($file_cache)){
			lc('error')->show_error(404, 'Page not Found');
		}else{
			$this->send_image($file_cache, $ext);
		}
	}
	public function web_image(){
		//WEB FILES
		//the ID of the web_files is passed as get_num(4)
		//get_num(4) because: 0 = CLASS_KEY, 1=CLASS, 2=TASK_KEY, 3=TASK, 4=remainder
		$image_id	 = lc('uri')->get_num(4, 0);
		$lib		 = ll('table_prototype');
		$filters	 = array();
		$filters[]	 = array('field' => 'id', 'operator' => '=', 'value' => $image_id);
		$filters[]	 = array('field' => 'active', 'operator' => '=', 'value' => 'y');
		$tmp		 = $lib->set_sql_cache('once')->set_read('once')->get_raw($filters, array(NULL), array(), '1', 'image');
		$file_cache	 = IMAGEPATH.'image'.DIRECTORY_SEPARATOR.'NO_FILE_FOUND';
		if(isset($tmp[0])){
			$temp			 = ll('client')->get('client_template', '');
			$template_folder = $temp == ''?'':($temp.'/');
			$file			 = $tmp[0]['name'];
			$file_type		 = $tmp[0]['ext'];
			$file_cache		 = IMAGEPATH."$template_folder/image".DIRECTORY_SEPARATOR.$file.'.'.$file_type;
		}
		//Done this way in case the parameters of this function change we only need to change one line instead of two, also duplication of code.
//		$this->send_image($file_cache, $file_type);
		if(!ll('files')->exists($file_cache)){
			lc('error')->show_error(404, 'Page not Found');
		}else{
			$this->send_image($file_cache, $file_type);
		}
	}
	public function web_product(){
		//WEB FILES
		//the ID of the web_files is passed as get_num(4)
		//get_num(4) because: 0 = CLASS_KEY, 1=CLASS, 2=TASK_KEY, 3=TASK, 4=remainder
		$product_image_id	 = lc('uri')->get_num(4, 0);
		$lib				 = ll('table_prototype');
		$filters			 = array();
		$filters[]			 = array('field' => 'id', 'operator' => '=', 'value' => $product_image_id);
		$ttmp				 = $lib->set_sql_cache('once')->set_read('once')->get_raw($filters, array(NULL), array(), '1', 'product_image');
		$file_cache			 = IMAGEPATH.'image'.DIRECTORY_SEPARATOR.'NO_FILE_FOUND';
		$ilib				 = ll('table_prototype')->set_table_name('image');
		$image_id			 = $ttmp[0]['image_id'];
		$filters			 = array();
		$filters[]			 = array('field' => 'id', 'operator' => '=', 'value' => $image_id);
		$filters[]			 = array('field' => 'active', 'operator' => '=', 'value' => 'y');
		$_info				 = $ilib->get_info($filters);
		if(is_array($_info) && !empty($_info)){
			$temp			 = ll('client')->get('client_template', '');
			$template_folder = $temp == ''?'':($temp.'/');
			$file_type		 = $_info['ext'];
			$file			 = $_info['name'];
			$file_cache		 = IMAGEPATH."$template_folder/image".DIRECTORY_SEPARATOR.$file.'.'.$file_type;
		}
		//Done this way in case the parameters of this function change we only need to change one line instead of two, also duplication of code.
//		$this->send_image($file_cache, $file_type);
		if(!ll('files')->exists($file_cache)){
			lc('error')->show_error(404, 'Page not Found');
		}else{
			$this->send_image($file_cache, $file_type);
		}
	}
	public function web_category(){
		//WEB FILES
		//the ID of the web_files is passed as get_num(4)
		//get_num(4) because: 0 = CLASS_KEY, 1=CLASS, 2=TASK_KEY, 3=TASK, 4=remainder
		$category_id = lc('uri')->get_num(4, 0);
		$lib		 = ll('table_prototype');
		$filters	 = array();
		$filters[]	 = array('field' => 'category_id', 'operator' => '=', 'value' => $category_id);
		$filters[]	 = array('field' => 'main', 'operator' => '=', 'value' => 'y');
		$ttmp		 = $lib->set_sql_cache('once')->set_read('once')->get_raw($filters, array(NULL), array(), '1', 'category_image');
		if(!is_array($ttmp) || empty($ttmp)){
			$filters	 = array();
			$filters[]	 = array('field' => 'category_id', 'operator' => '=', 'value' => $category_id);
			$ttmp		 = $lib->set_sql_cache('once')->set_read('once')->get_raw($filters, array(NULL), array(), '1', 'category_image');
		}
		$file_cache = IMAGEPATH.'image'.DIRECTORY_SEPARATOR.'NO_FILE_FOUND';
		if(isset($ttmp[0])){
			$ilib		 = ll('table_prototype')->set_table_name('image');
			$image_id	 = $ttmp[0]['image_id'];
			$filters	 = array();
			$filters[]	 = array('field' => 'id', 'operator' => '=', 'value' => $image_id);
			$filters[]	 = array('field' => 'active', 'operator' => '=', 'value' => 'y');
			$_info		 = $ilib->get_info($filters);
			if(is_array($_info) && !empty($_info)){
				$temp			 = ll('client')->get('client_template', '');
				$template_folder = $temp == ''?'':($temp.'/');
				$file_type		 = $_info['ext'];
				$file			 = $_info['name'];
				$file_cache		 = IMAGEPATH."$template_folder/image".DIRECTORY_SEPARATOR.$file.'.'.$file_type;
			}
		}
		//Done this way in case the parameters of this function change we only need to change one line instead of two, also duplication of code.
//		$this->send_image($file_cache, $file_type);
		if(!ll('files')->exists($file_cache)){
			lc('error')->show_error(404, 'Page not Found');
		}else{
			$this->send_image($file_cache, $file_type);
		}
	}
	public function web_category_image(){
		//WEB FILES
		//the ID of the web_files is passed as get_num(4)
		//get_num(4) because: 0 = CLASS_KEY, 1=CLASS, 2=TASK_KEY, 3=TASK, 4=remainder
		$category_image_id	 = lc('uri')->get_num(4, 0);
		$lib				 = ll('table_prototype');
		$filters			 = array();
		$filters[]			 = array('field' => 'id', 'operator' => '=', 'value' => $category_image_id);
		$ttmp				 = $lib->set_sql_cache('once')->set_read('once')->get_raw($filters, array(NULL), array(), '1', 'category_image');
		$file_cache			 = IMAGEPATH.'image'.DIRECTORY_SEPARATOR.'NO_FILE_FOUND';
		if(isset($ttmp[0])){
			$ilib		 = ll('table_prototype')->set_table_name('image');
			$image_id	 = $ttmp[0]['image_id'];
			$filters	 = array();
			$filters[]	 = array('field' => 'id', 'operator' => '=', 'value' => $image_id);
			$filters[]	 = array('field' => 'active', 'operator' => '=', 'value' => 'y');
			$_info		 = $ilib->get_info($filters);
			if(is_array($_info) && !empty($_info)){
				$temp			 = ll('client')->get('client_template', '');
				$template_folder = $temp == ''?'':($temp.'/');
				$file_type		 = $_info['ext'];
				$file			 = $_info['name'];
				$file_cache		 = IMAGEPATH."$template_folder/image".DIRECTORY_SEPARATOR.$file.'.'.$file_type;
			}
		}
		//Done this way in case the parameters of this function change we only need to change one line instead of two, also duplication of code.
//		$this->send_image($file_cache, $file_type);
		if(!ll('files')->exists($file_cache)){
			lc('error')->show_error(404, 'Page not Found');
		}else{
			$this->send_image($file_cache, $file_type);
		}
	}
	public function web_product_image(){
		//WEB FILES
		//the ID of the web_files is passed as get_num(4)
		//get_num(4) because: 0 = CLASS_KEY, 1=CLASS, 2=TASK_KEY, 3=TASK, 4=remainder
		$product_image_id	 = lc('uri')->get_num(4, 0);
		$lib				 = ll('table_prototype');
		$filters			 = array();
		$filters[]			 = array('field' => 'id', 'operator' => '=', 'value' => $product_image_id);
		$ttmp				 = $lib->set_sql_cache('once')->set_read('once')->get_raw($filters, array(NULL), array(), '1', 'product_image');
		$file_cache			 = IMAGEPATH.'image'.DIRECTORY_SEPARATOR.'NO_FILE_FOUND';
		if(isset($ttmp[0])){
			$ilib		 = ll('table_prototype')->set_table_name('image');
			$image_id	 = $ttmp[0]['image_id'];
			$filters	 = array();
			$filters[]	 = array('field' => 'id', 'operator' => '=', 'value' => $image_id);
			$filters[]	 = array('field' => 'active', 'operator' => '=', 'value' => 'y');
			$_info		 = $ilib->get_info($filters);
			if(is_array($_info) && !empty($_info)){
				$temp			 = ll('client')->get('client_template', '');
				$template_folder = $temp == ''?'':($temp.'/');
				$file_type		 = $_info['ext'];
				$file			 = $_info['name'];
				$file_cache		 = IMAGEPATH."$template_folder/image".DIRECTORY_SEPARATOR.$file.'.'.$file_type;
			}
		}
		//Done this way in case the parameters of this function change we only need to change one line instead of two, also duplication of code.
//		$this->send_image($file_cache, $file_type);
		if(!ll('files')->exists($file_cache)){
			lc('error')->show_error(404, 'Page not Found');
		}else{
			$this->send_image($file_cache, $file_type);
		}
	}
}
