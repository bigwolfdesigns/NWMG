<?php

if(!defined('BASEPATH')) exit('No direct script access allowed');

class css{
	protected $config = array();
	/**
	 * Initialize the default class
	 *
	 * @access	private
	 * @return	void
	 */
	public function __construct(){
		ini_set('session.use_cookies', '0');
		$this->config['uri']		 = &lc('uri');
		$this->config['template']	 = ll('client')->get('template', 'default');
		$task						 = $this->config['uri']->get(TASK_KEY, 'common');
		$task						 = preg_replace('/(.*)\.[\d]{10}/', '$1', $task);
		if(!method_exists($this, 'web_'.$task)||!is_callable(array($this, 'web_'.$task))){
			$task = 'common';
		}
		$this->{'web_'.$task}();
	}
	private function send_file($file_cache, $file_type = 'css', $expires = 604800){
		$is_content = (substr($file_cache, 0, 8)=='content:');
		if($is_content||ll('files')->file_exists($file_cache)){
			$is_304 = false;
			if($is_content){
				$file_content		 = substr($file_cache, 8);
				$file_stat			 = array();
				$file_stat['size']	 = strlen($file_content);
				$file_stat['mtime']	 = time();
			}else{
				$file_stat = stat($file_cache);
			}
			$now_gmt = date('D, d M Y H:i:s T', time());
			$exp_gmt = date('D, d M Y H:i:s T', time()+$expires);
			$mod_gmt = date('D, d M Y H:i:s T', $file_stat['mtime']);

			$etag	 = md5($file_stat['size'].'-'.$file_stat['mtime'].'-'.$file_cache);
			$etag	 = substr($etag, 0, 4).'-'.substr($etag.$etag, 5, 13);

			header('Date: '.$now_gmt);
//			header('Age: '.$expires);
			header('ETag: '.$etag);
			header('Pragma: public');
			header('Proxy-Connection: keep-alive');
			header('Vary: Accept-Encoding');
			if($file_type!=''){
				$file_type1 = '';
				switch($file_type){
					case 'javascript':
//						$file_type1 = 'application';	//'causes issues with IE6/7
//						break;
					case 'css':
					case 'htm':
					case 'html':
					case 'txt':
						$file_type1	 = 'text';
						break;
					case 'bmp':
					case 'png':
					case 'gif':
					case 'jpg':
					case 'jpeg':
						$file_type1	 = 'image';
						break;
				}
				if($file_type1!=''){
					header('Content-type: '.$file_type1.'/'.$file_type);
				}
			}
			header('Last-Modified: '.$mod_gmt);
			header('Cache-Control: public, max-age='.$expires);
			header('Expires: '.$exp_gmt);
			if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])||isset($_SERVER['HTTP_IF_NONE_MATCH'])){
				// parse header
				$if_modified_since	 = '';
				$if_none_match		 = '';
				if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])){
					$if_modified_since = preg_replace('/;.*$/', '', $_SERVER['HTTP_IF_MODIFIED_SINCE']);
				}
				if(isset($_SERVER['HTTP_IF_NONE_MATCH'])){
					$if_none_match = $_SERVER['HTTP_IF_NONE_MATCH'];
				}
				if($if_modified_since==$mod_gmt||$if_none_match==$etag){
					header($_SERVER['SERVER_PROTOCOL'].' 304 Not Modified');
					// the browser's cache is still up to date
					$is_304 = true;
				}
			}
			if(!$is_304){
				$config = lc('config')->get_and_unload_config('display');
				if($config['gzip']&&extension_loaded('zlib')&&ob_start('ob_gzhandler')){
					ob_implicit_flush(0);
				}else{
					header('Content-Length: '.$file_stat['size']);
				}
				if($is_content){
					echo $file_content;
				}else{
					readfile($file_cache);
				}
			}
		}else{
			lc('error')->show_error(404);
		}
//		exit(1);
	}
	private function _ash_file($file){
		$is_content = (substr($file, 0, 8)=='content:');
		if($is_content){
			$return = substr('0123456789'.abs(crc32($file)), -10);
		}elseif(ll('files')->file_exists($file)){
			$return = substr('0123456789'.abs(crc32(filemtime($file))), -10);
		}else{
			$return = '0123456789';
		}
		return $return;
	}
	public function web_common($return_real_path = false, $return_ash = false){
		$uri			 = $this->config['uri'];
		$file			 = str_replace('|', DIRECTORY_SEPARATOR, $uri->get('f', ''));
		$found_extension = '';
		if($file==''){
			$tmps = $uri->get_num();
			foreach($tmps as $k => $tmp){
				if($k>=3&&$tmp!=''){
					$file .= $tmp.DIRECTORY_SEPARATOR;
				}
			}
		}
		if(substr($file, -1)==DIRECTORY_SEPARATOR){
			$file = substr($file, 0, -1);
		}
		$file		 = preg_replace('/(.*)\.[\d]{10}/', '$1', $file);
		$file		 = str_replace(array('..', '|'), array('', DIRECTORY_SEPARATOR), $file);
		$template	 = $this->config['template'];
		$file_cache	 = TPLPATH.$template.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.$file;
		$allowedExt	 = array('css', $uri->get_extension());
		foreach($allowedExt as $ext){
			if(ll('files')->file_exists($file_cache.'.'.$ext)&&is_file($file_cache.'.'.$ext)){
				$found_extension = $ext;
				$file_cache .= '.'.$ext;
				break;
			}
		}
		if(!ll('files')->file_exists($file_cache)&&$template!='default'){
			$file_cache = TPLPATH.'default'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.$file;
			if(ll('files')->file_exists($file_cache.'.'.$ext)&&is_file($file_cache.'.'.$ext)){
				$found_extension = $ext;
				$file_cache .= '.'.$ext;
			}
		}
		if(!ll('files')->file_exists($file_cache)){
			$file_cache = TPLPATH.'css'.DIRECTORY_SEPARATOR.$file;
			foreach($allowedExt as $ext){
				if(ll('files')->file_exists($file_cache.'.'.$ext)&&is_file($file_cache.'.'.$ext)){
					$found_extension = $ext;
					$file_cache .= '.'.$ext;
					break;
				}
			}
		}
		if(ll('files')->file_exists($file_cache)&&is_file($file_cache)){
			if($return_real_path){
				$return = $file_cache;
			}elseif($return_ash){
				$return = $this->_ash_file($file_cache);
			}else{
				$file		 = $file_cache;
				$stats		 = ll('files')->stat($file);
				$file_cache	 = md5(serialize($file).$stats['ctime']).'.'.$found_extension;
				$cache_file	 = strtolower(crc32($file_cache));
				$folder		 = TMPPATH.'tmp_files'.DIRECTORY_SEPARATOR.substr($cache_file, 0, 2).DIRECTORY_SEPARATOR;
				$file_cache	 = $folder.$file_cache;
				$content	 = false;//lc('cache')->get($file_cache, false);
				if($content==false){
					if($found_extension=='css'){
						$content = ll('format')->minify_css(file_get_contents($file));
					}else{
						$content = file_get_contents($file);
					}
//					lc('cache')->set($file_cache, $content, true, 7200);
				}
				$return = $this->send_file('content:'.$content, $found_extension);
			}
		}else{
			$return = false;
		}
		return $return;
	}
	public function web_load($return_ash = false){
		//this load all the CSS necessary for this page in one time
		$config	 = lc('config')->get_and_unload_config('display');
		$links	 = isset($config['link'])?$config['link']:array();
		$uri	 = $this->config['uri'];
		$return	 = '';
		if(is_array($links)){
			$files	 = array();
			$old_f	 = $uri->get('f', NULL);
			foreach($links as $link){
				if(
						isset($link['type'])&&
						isset($link['media'])&&
						isset($link['href'])&&
						strtolower($link['type'])=='text/css'&&
						strtolower($link['media'])=='all'&&
						strtolower(substr($link['href'], 0, 5))=='/css/'
				){
					$uri->set('f', str_replace('/css/', '', $link['href']));
					$files[$link['href']]			 = $this->web_common(true);
					$files['time_'.$link['href']]	 = filemtime($this->web_common(true));
				}
			}
			if(is_null($old_f)){
				$uri->delete('f');
			}else{
				$uri->set('f', $old_f);
			}
//			$cache_file	 = strtolower(crc32($this->config['subsite_id']));
//			$folder		 = TMPPATH.'tmp_files'.DIRECTORY_SEPARATOR.substr($cache_file, 0, 2).DIRECTORY_SEPARATOR;
//			$file_cache	 = $folder.$this->config['subsite_id'].md5(serialize($files)).'.css';
			$content	 = false;//lc('cache')->get($file_cache, false);
			if($content==false){
				$content = '';
				foreach($files as $file){
					if(ll('files')->file_exists($file)){
						$content .= file_get_contents($file);
					}
				}
				if($content!=''){
//					lc('cache')->set($file_cache, $content, true, 7200);
					$content = ll('format')->minify_css($content);
				}else{
					$return = false;
				}
			}
			if($return!==false){
				if($return_ash){
					$return = $this->_ash_file('content:'.$content);
				}else{
					$return = $this->send_file('content:'.$content);
				}
			}
			return $return;
		}
	}
	public function web_null(){
		//this function is here on purpose.. it doesn't do anything
	}
}
