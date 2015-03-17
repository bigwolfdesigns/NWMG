<?php

class display{
	private $config				 = array();
	private $output				 = array();
	private $internal_temp_var	 = array();
	private $tpl				 = array();
	protected $cache			 = false;
	protected $cache_folder		 = '';
	protected static $instance	 = NULL;
	protected $content			 = false;
	protected $use_start		 = false;
	protected $use_end			 = false;
	protected $replace			 = array();
	public static function &get_instance(){
//		if (is_null(self::$instance)){
//			self::$instance = new display();
//		}
		return self::$instance;
	}
	public function __construct(){
		ll('display'.DIRECTORY_SEPARATOR.lc('config')->load('display')->get('display_driver', 'default'));
		self::$instance->config	 = lc('config')->get_and_unload_config('display');
		/*
		  if(isset(self::$instance->config['group'])){
		  $uri = lc('uri');
		  if(isset(self::$instance->config['group']['css']) && self::$instance->config['group']['css']){
		  foreach(self::$instance->config['link'] as $k => $link){
		  if(
		  isset($link['type']) &&
		  isset($link['media']) &&
		  isset($link['href']) &&
		  strtolower($link['type']) == 'text/css' &&
		  strtolower($link['media']) == 'all' &&
		  strtolower(substr($link['href'], 0, 5)) == '/css/'
		  ){
		  unset(self::$instance->config['link'][$k]);
		  }
		  }
		  $old_task		 = $uri->get(TASK_KEY, NULL);
		  $uri->set(TASK_KEY, 'null');
		  $ash			 = lc('css')->web_load(true);
		  $uri->set(TASK_KEY, $old_task);
		  $new			 = array();
		  $new['type']	 = 'text/css';
		  $new['rel']		 = 'stylesheet';
		  $new['title']	 = 'default';
		  $new['href']	 = '/css/load.'.$ash.'.css';
		  $new['media']	 = 'all';
		  array_push(self::$instance->config['link'], $new);
		  }
		  if(isset(self::$instance->config['group']['js']) && self::$instance->config['group']['js']){
		  foreach(self::$instance->config['script'] as $k => $script){
		  if(
		  isset($script['type']) &&
		  isset($script['language']) &&
		  isset($script['src']) &&
		  strtolower($script['type']) == 'text/javascript' &&
		  strtolower($script['language']) == 'javascript' &&
		  strtolower(substr($script['src'], 0, 4)) == '/js/'
		  ){
		  unset(self::$instance->config['script'][$k]);
		  }
		  }
		  $old_task		 = $uri->get(TASK_KEY, NULL);
		  $uri->set(TASK_KEY, 'null');
		  $ash			 = lc('js')->web_load(true);
		  $uri->set(TASK_KEY, $old_task);
		  $new			 = array();
		  $new['type']	 = 'text/javascript';
		  $new['language'] = 'javascript';
		  $new['src']		 = '/js/load.'.$ash.'.js';
		  array_push(self::$instance->config['script'], $new);
		  }
		  }
		 */
		self::$instance->cache	 = intval(self::$instance->config['cache_timeout']) > 0;
		self::$instance->assign('link', self::$instance->config['link']);
		self::$instance->assign('script', self::$instance->config['script']);
		self::$instance->assign('meta', self::$instance->config['meta']);
	}
	public function add_replace($key, $value){
		$this->replace[$key] = (string)$value;
		return $this;
	}
	public function get_replace($key, $default = NULL){
		if(isset($this->replace[$key])){
			$return = $default;
		}else{
			$return = $this->replace[$key];
		}
		return $return;
	}
	public function delete_replace($key){
		if(isset($this->replace[$key])){
			unset($this->replace[$key]);
		}
		return $this;
	}
	public function set_cache($enabled, $folder = ''){
		$this->cache = (bool)$enabled;
		if($this->cache){
			if(trim($folder) == ''){
				$folder = TMPPATH.'cached_files'.DIRECTORY_SEPARATOR;
			}
			if(!ll('files')->exists($folder)){
				if(@!ll('files')->mkdir($folder, 0766, true)){
					die('Please Create '.$folder);
				}
			}
			if(!is_dir($folder)){
				die($folder.' is not a folder !');
			}
			if(!is_writable($folder)){
				die('Folder '.$folder.' not writeable !');
			}
			$this->cache_folder = $folder;
		}
	}
	public function add_script($type, $language = '', $src = ''){
		$scripts = $this->get('script');
		if(is_array($type)){
			$v = $type;
		}else{
			$v['type']		 = $type;
			$v['language']	 = $language;
			$v['src']		 = $src;
		}
		//ckeditor has a problem with changing the file sname
//		if(strtolower(substr($v['src'], 0, 4)) == '/js/' && strpos($v['src'], 'ckeditor') === false){
//			$uri		 = lc('uri');
//			$old_f		 = $uri->get('f', NULL);
//			$old_task	 = $uri->get(TASK_KEY, NULL);
//			$uri->set('f', str_replace('/js/', '', $v['src']));
//			$uri->set(TASK_KEY, 'null');
//			$ash		 = lc('js')->web_common(false, true);
//			$v['src']	 = str_replace('.js', '.'.$ash.'.js', $v['src']);
//			$uri->set(TASK_KEY, $old_task);
//			if(is_null($old_f)){
//				$uri->delete('f');
//			}else{
//				$uri->set('f', $old_f);
//			}
//		}
		$scripts[]				 = $v;
		$this->output['script']	 = $scripts;
		return $this;
	}
	public function add_link($type, $rel = '', $title = '', $href = '', $media = 'all'){
		$links = $this->get('link');
		if(is_array($type)){
			$v = $type;
		}else{
			$v			 = array();
			$v['type']	 = $type;
			$v['rel']	 = $rel;
			$v['title']	 = $title;
			$v['href']	 = $href;
			$v['media']	 = $media;
		}
//		if(strtolower(substr($v['href'], 0, 5)) == '/css/'){
//			$uri		 = lc('uri');
//			$old_f		 = $uri->get('f', NULL);
//			$old_task	 = $uri->get(TASK_KEY, NULL);
//			$uri->set('f', str_replace('/css/', '', $v['href']));
//			$uri->set(TASK_KEY, 'null');
//			$ash		 = lc('css')->web_common(false, true);
//			$v['href']	 = str_replace('.css', '.'.$ash.'.css', $v['href']);
//			$uri->set(TASK_KEY, $old_task);
//			if(is_null($old_f)){
//				$uri->delete('f');
//			}else{
//				$uri->set('f', $old_f);
//			}
//		}
		$links[]				 = $v;
		$this->output['link']	 = $links;
		return $this;
	}
	public function delete_link($matching){
		// this function will remove any link that matches the filter passed
		// for example if $matching = array('rel'=>'canonical')
		// this function will remove ANY link where there is a KEY called "rel" and the value is set to "canonical"
		// if matching has multiple key all of the keys will need to match for it to be removed
		if(is_array($matching) && count($matching) > 0){
			$links					 = $this->output['link'];
			$this->output['link']	 = array();
			foreach($links as $link){
				$remove = true;
				foreach($matching as $k=> $v){
					if(!isset($link[$k]) || $link[$k] != $v){
						$remove = false;
					}
				}
				if(!$remove){
					$this->output['link'][] = $link;
				}
			}
		}
		return $this;
	}
	public function add_meta($values){
		if(is_array($values)){
			$metas					 = $this->get('meta');
			$metas[]				 = $values;
			$this->output['meta']	 = $metas;
		}
		return $this;
	}
	public function add_header($key, $value){
		$headers				 = $this->config['headers'];
		if(!is_array($headers)) $headers				 = array();
		$headers[$key]			 = $value;
		$this->config['headers'] = $headers;
		return $this;
	}
	public function _process_headers(){
		$tmp = isset($this->config['headers'])?$this->config['headers']:array();

		if(is_array($tmp)){
			//first, let's remove what was sent previously
			if(!headers_sent()){
				$headers = headers_list();
				foreach($headers as $header){
					list($header) = explode(':', $header, 2);
					if(strtolower($header) != 'set-cookie'){
//						header_remove($header);
					}
				}
				foreach($tmp as $key=> $value){
					if(is_array($value)){
						foreach($value as $vv){
							$this->_send_header($key, $vv);
						}
					}else{
						$this->_send_header($key, $value);
					}
				}
			}
		}
		return $this;
	}
	private function _send_header($key, $value){
		$value = trim($value);
		if(!headers_sent()){
			if($value != '') $key .=': ';
			header($key.$value);
		}
		return $this;
	}
	public function start(){
		$this->use_start = true;
		return $this;
	}
	private function _start(){
		static $firsttime = true;
		if($firsttime){
			$this->_process_headers();
			ob_implicit_flush(0);
			ob_start();
			if($this->config['gzip'] && isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') && extension_loaded('zlib')){// && ob_start('ob_gzhandler')){
				//YEAH !! gzip is enabled !!
				if(function_exists('apache_setenv')){
					apache_setenv('no-gzip', 1);
				}
			}else{
				$this->config['gzip'] = false;
			}
			$firsttime = false;
		}
		return $this;
	}
	public function end(){
		$this->use_end = true;
		return $this;
	}
	private function _end(){
		static $firsttime = true;
		if($firsttime){
			$content = '';
			while(ob_get_level() > 0){
				$content .= ob_get_clean();
			}
			if($this->config['gzip']){
				$this->_send_header('Content-Encoding', 'gzip');
				$content = "\x1f\x8b\x08\x00\x00\x00\x00\x00".substr(gzcompress($content, 6), 0, -4);
			}
			if(count($this->replace) > 0){
				//let's replace the variables
				$from	 = array();
				$to		 = array();
				foreach($this->replace as $f=> $t){
					$from[]	 = $f;
					$to[]	 = $t;
				}
				$content = str_replace($from, $to, $content);
			}
			$this->_send_header('Content-Length', strlen($content));
			echo $content;
			$firsttime = false;
		}
		return $this;
	}
	public function assign($var, $value = NULL){
		if(is_array($var) && is_null($value)){
			foreach($var as $k=> $v){
				$this->assign($k, $v);
			}
		}else{
			$this->output[$var] = $value;
		}
		return $this;
	}
	public function delete($var){
		if(isset($this->output[$var])){
			unset($this->output[$var]);
		}
		return $this;
	}
	public function get($var, $default = NULL){
		return isset($this->output[$var])?$this->output[$var]:$default;
	}
	/*	 * *********************************************
	 * display on screen the template
	 * temp_var will override the global variables
	 * ********************************************* */
	public function show($tplFile, $temp_var = array()){
		return $this->_show($tplFile, $temp_var);
	}
	/*
	 * this is the function that will be called by the extensions
	 */
	protected function _show($tplFile, $temp_var = array(), $cache_timeout = false){
		$this->tpl[] = array(
			'file'			=>$tplFile,
			'vars'			=>$temp_var,
			'cache_timeout'	=>$cache_timeout,
		);
	}
	private function _get_cache($cache_file, $tplFile, $cache_timeout = false){
		$return = false;
		
		if($cache_timeout === false){
			$cache_timeout = intval(self::$instance->config['cache_timeout']);
		}
		if($this->cache && $cache_timeout > 0){
			$folder		 = $this->cache_folder.substr(crc32($cache_file), 0, 2);
			$cache_file	 = $folder.DIRECTORY_SEPARATOR.$cache_file;
			$cache		 = lc('cache')->getVersion();
			if(count($cache) > 0){
				$tmp	 = lc('cache')->get($cache_file, '');
				$tmps	 = explode(' ', $tmp, 2);
				if($tmps[0] != ll('files')->mtime(TPLPATH.$tplFile.EXT)){
					lc('cache')->delete($cache_file);
				}else{
					$return = $tmps[1];
				}
			}else{
				if(!ll('files')->exists($folder)){
					@ll('files')->mkdir($folder, 0766, true);
				}
				if($cache_timeout > 0 && ll('files')->exists($cache_file)){
					$file_last_mod = ll('files')->mtime($cache_file);
					if($cache_timeout + $file_last_mod < time()){
						@ll('files')->unlink($cache_file);
					}
				}
				if(ll('files')->exists($cache_file)){
					$tmp	 = file_get_contents($cache_file);
					$tmps	 = explode(' ', $tmp, 2);
					if($tmps[0] != ll('files')->mtime(TPLPATH.$tplFile.EXT)){
						@unlink($cache_file);
					}else{
						$return = $tmps[1];
					}
					unset($tmps, $tmp);
				}
			}
		}
		return $return;
	}
	private function _save_cache($cache_file, $tplFile, $cache_timeout, $content){
		if($cache_timeout === false){
			$cache_timeout = intval(self::$instance->config['cache_timeout']);
		}
		if($this->cache && $cache_timeout > 0 && $cache_file != ''){
			$folder		 = $this->cache_folder.substr(crc32($cache_file), 0, 2);
			$cache_file	 = $folder.DIRECTORY_SEPARATOR.$cache_file;
//			ll('output')->debug($cache_file);
			$cache		 = lc('cache')->getVersion();
			$content	 = ll('files')->mtime(TPLPATH.$tplFile.EXT).' '.$content;
			if(count($cache) > 0){
				lc('cache')->set($cache_file, $content, true, $cache_timeout);
			}else{
				if(!file_put_contents($cache_file, $content)){
					trigger_error('Cannot create cache file for template: '.$cache_file, E_USER_WARNING);
				}
			}
		}
	}
	/*
	 * This function will force the content of the page skipping the template system
	 * useful to send images of CSS files or content created on the fly
	 */
	public function force_content($content = ''){
		$this->content = $content;
	}
	public function render(){
		$output = '';
		if($this->use_start){
			$this->_start();
		}
		if($this->content !== false){
			echo $this->content;
		}elseif(is_array($this->tpl)){
			foreach($this->tpl as $tpl){
				$tplFile		 = $tpl['file'];
				$temp_var		 = $tpl['vars'];
				$cache_timeout	 = $tpl['cache_timeout'];
				$kk				 = md5(microtime().$tplFile);
				if(ll('files')->exists(TPLPATH.$tplFile.EXT)){
					$cache_file	 = '';
					$key_file	 = md5(serialize($this->output).serialize($temp_var).serialize($this->internal_temp_var).serialize($this->replace));
					$cache_file	 = preg_replace('/[^0-9a-z]/i', '_', $tplFile).'-'.ll('files')->mtime(TPLPATH.$tplFile.EXT).'-'.$key_file.'.tpl';

					$content = $this->_get_cache($cache_file, $tplFile, $cache_timeout);
					if($content === false){
						foreach($this->output as $__k=> $__v){
							$$__k = $__v;
						}
						if(is_array($temp_var) && !empty($temp_var)){
							$this->internal_temp_var[$kk] = $temp_var;
						}
						foreach($this->internal_temp_var as $k=> $temp_var){
							foreach($temp_var as $__k=> $__v){
								$$__k = $__v;
							}
						}
						ob_start();
						include TPLPATH.$tplFile.EXT;
						$content = ob_get_clean();
						if(isset($this->internal_temp_var[$kk])){
							unset($this->internal_temp_var[$kk]);
						}
						$this->_save_cache($cache_file, $tplFile, $cache_timeout, $content);
					}
					$output .= $content;
				}else{
					$output .= $tplFile.' NOT FOUND !!!';
				}
			}
		}
//		echo ll('format')->minify_html($output);
		echo $output;
		if($this->use_end){
			$this->_end();
		}
		return $this;
	}
	/*	 * *********************************************
	 * return the template as a string
	 * temp_var will override the global variables
	 * ********************************************* */
	public function grab($tplFile, $temp_var = array()){
		return $this->_grab($tplFile, $temp_var);
	}
	/*
	 * this is the function that will be called by the extensions
	 */
	public function _grab($tplFile, $temp_var = array(), $cache_timeout = false){
		$kk = md5(microtime().$tplFile);
		if(ll('files')->exists(TPLPATH.$tplFile.EXT)){
			$cache_file	 = '';
			$key_file	 = md5(serialize($this->output).serialize($temp_var).serialize($this->internal_temp_var));
			$cache_file	 = preg_replace('/[^0-9a-z]/i', '_', $tplFile).'-'.ll('files')->mtime(TPLPATH.$tplFile.EXT).'-'.$key_file.'.tpl';
			$content	 = $this->_get_cache($cache_file, $tplFile, $cache_timeout);
			if($content === false){
				foreach($this->output as $__k=> $__v){
					$$__k = $__v;
				}
				if(is_array($temp_var) && !empty($temp_var)){
					$this->internal_temp_var[$kk] = $temp_var;
				}
				foreach($this->internal_temp_var as $k=> $temp_var){
					foreach($temp_var as $__k=> $__v){
						$$__k = $__v;
					}
				}
				ob_start();
				include TPLPATH.$tplFile.EXT;
				$content = ob_get_contents();
				ob_end_clean();
				if(isset($this->internal_temp_var[$kk])){
					unset($this->internal_temp_var[$kk]);
				}
				$this->_save_cache($cache_file, $tplFile, $cache_timeout, $content);
			}
		}else{
			$content = $tplFile.' NOT FOUND !!!';
		}
		return $content;
	}
	public function get_config($var, $default = NULL){
		$return = $default;
		if(isset($this->config[$var])){
			$return = $this->config[$var];
		}
		return $return;
	}
	public function set_config($var, $value){
		if(isset($this->config[$var])){
			$this->config[$var] = $value;
		}
		return $this;
	}
}

?>