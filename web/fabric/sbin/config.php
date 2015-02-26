<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
This is the main class for the configuration
It will be used to get and set the configuration for a site

Using this class will be easy to add a generic configuration variable
without having to modify the database or any file manually
*/
class config{
	private $_cfg = array();
	private $loaded = array();
	private $override = array();
	public function __construct(){
	}
	public function get($key, $default=NULL){
		if(!isset($this->_cfg[$key])){
			if(strpos($key,'_')>0){
				list($key1, $key2) = explode('_', $key, 2);
				if(isset($this->_cfg[$key1][$key2])){
					$this->_cfg[$key] = $this->_cfg[$key1][$key2];
					return $this->_cfg[$key];
				}
			}
			return $default;
		}
		return $this->_cfg[$key];
	}
	public function load_config($conf){
		$ret	= debug_backtrace();
		$ret	= (isset($ret[1]))?$ret[1]:$ret[0];
		$file   = $ret['file'];
		$line   = $ret['line'];
		$object = $ret['object'];
		if (is_object($object)) { $object = get_class($object); }
		trigger_error('config->load_config() DEPRECATED.  Use config->load() called: line '.$line.' of '.$object.' (in '.$file.')', E_USER_DEPRECATED);
		return $this->load($conf);
	}
	public function load($conf){
		//Here I have to load the configuration
		//from memcache
		//from file
		//from DB
		//load the config only one time
		if($conf!='' && !isset($this->loaded[$conf])){
			//starting with a file, if exists
			//etc/fabric.conf.php
			if(file_exists(ETCPATH.$conf.'.conf'.EXT)){
				include ETCPATH.$conf.'.conf'.EXT;
				if(isset($$conf)){
					$this->_cfg[$conf] = $$conf;
				}
			}
		}
		foreach($this->override as $k=>$v){
			if(isset($this->_cfg[$k])){
				$this->_cfg[$k] = $v;
			}
		}
		$this->loaded[$conf] = true;
		return $this;
	}
	public function unload_config($conf = ''){
		$ret	= debug_backtrace();
		$ret	= (isset($ret[1]))?$ret[1]:$ret[0];
		$file   = $ret['file'];
		$line   = $ret['line'];
		$object = $ret['object'];
		if (is_object($object)) { $object = get_class($object); }
		trigger_error('config->unload_config() DEPRECATED.  Use config->unload() called: line '.$line.' of '.$object.' (in '.$file.')', E_USER_DEPRECATED);

		return $this->unload($conf);
	}
	public function unload($conf = ''){
		if($conf!=''){
			unset($this->loaded[$conf]);
			unset($this->_cfg[$conf]);
		}else{
			$this->loaded	= array();
			$this->_cfg		= array();
		}
		return $this;
	}
	public function get_config($conf){
		$this->load($conf);
		return isset($this->_cfg[$conf])?$this->_cfg[$conf]:NULL;
	}
	public function get_and_unload_config($conf){
		$ret = $this->get_config($conf);
		$this->unload($conf);
		return $ret;
	}
	public function set($key, $value){
		$this->_cfg[$key] = $value;
		return $this;
	}
	/**
	 * This function will force, each time that a configuration is loaded
	 * to check if the "key" exists, and if it does, to override it with the
	 * passed value
	 * for example you can call: set_override('cache_site_id',123)
	 * to force $this->_cfg['cache']['site_id'] = 123 every time that the file is loaded
	 * @param string $key
	 * @param mixed $value
	 */
	public function set_override($key, $value){
		$this->override[$key] = $value;
		return $this;
	}
	public function delete_override($key){
		if(isset($this->override[$key])){
			unset($this->override[$key]);
		}
		return $this;
	}
	public function get_override($key, $default){
		if(isset($this->override[$key])){
			$ret = $this->override[$key];
		} else {
			$ret = $value;
		}
		return $ret;
	}
}
?>