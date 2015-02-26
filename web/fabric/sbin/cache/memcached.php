<?php

//thanks to php.net site

class cache_memcached extends cache{
	private $site_id	 = '';
	private $compress	 = false;
	private $expire		 = 0;
	private $connect	 = [];
	private $connected	 = false;
	public function __construct(){
		parent::$instance = &$this;
		// Attempt to establish/retrieve persistent connections to all servers.
		// If any of them fail, they just don't get put into our list of active
		// connections.
		$this->connections = [];
	}
	//set or return default site_id values
	public function set_site_id($site_id = NULL){
		if($site_id != NULL){
			$this->site_id = $site_id;
		}
		return $this->site_id;
	}
	//set or return default compress values
	public function default_compress($compress = NULL){
		if($compress != NULL){
			$this->compress = (bool)$compress;
		}
		return $this->compress;
	}
	//set or return default expire values
	public function default_expire($expire = NULL){
		if($expire != NULL){
			$this->expire = $expire + 0;
		}
		return $this->expire;
	}
	public function close(){
		$result = true;
		for($i = 0; $i < count($this->connections); $i++){
			if(!$this->connections[$i]->close()){
				$result = false;
			}
		}
		return $result;
	}
	private function _getConForKey($key){
		if(!$this->connected){
			$this->_connect();
		}
		if(($ns = count($this->connections)) > 1){
			$hashCode = 0;
			for($i = 0, $len = strlen($key); $i < $len; $i++){
				$hashCode = (int)(($hashCode * 33) + ord($key[$i])) & 0x7fffffff;
			}
			return $this->connections[$hashCode % $ns];
		}elseif(count($this->connections) == 1){
			return $this->connections[0];
		}
		return false;
	}
	private function _fixKey($key){
		$key = $this->site_id.trim($key);
		return strlen($key) > 100?md5($key):$key;
	}
	public function debug($on_off){
		if(!$this->connected){
			$this->_connect();
		}
		return memcache_debug($on_off);
//		$result = false;
//		for ($i = 0; $i < count($this->connections); $i++) {
//			if ($this->connections[$i]->debug($on_off)) $result = true;
//		}
//		return $result;
	}
	public function flush(){
		if(!$this->connected){
			$this->_connect();
		}
		$result = false;
		for($i = 0; $i < count($this->connections); $i++){
			if($this->connections[$i]->flush()) $result = true;
		}
		return $result;
	}
	public function getVersion(){
		if(!$this->connected){
			$this->_connect();
		}
		$result = [];
		for($i = 0; $i < count($this->connections); $i++){
			$result[] = $this->connections[$i]->getVersion();
		}
		return $result;
	}
	public function getStats(){
		if(!$this->connected){
			$this->_connect();
		}
		$result = [];
		for($i = 0; $i < count($this->connections); $i++){
			$result[] = $this->connections[$i]->getStats();
		}
		return $result;
	}
	public function getExtendedStats(){
		if(!$this->connected){
			$this->_connect();
		}
		$con = (isset($this->connections[0])?$this->connections[0]:false);
		if($con === false) return false;
		return $con->getExtendedStats();
	}
	public function get($key, $default = NULL){
		if(is_array($key)){
			$dest = [];
			foreach($key as $subkey){
				$dest[$subkey] = $this->get($subkey, $default);
			}
			return $dest;
		}else{
			$key = $this->_fixKey($key);
			$con = $this->_getConForKey($key);
			if($con === false){
				$return = $default;
			}else{
				$return = $con->get($key);
			}
			if($return === false && !is_null($default)){
				$return = $default;
			}
			return $return;
		}
	}
	public function set($key, $var, $compress = NULL, $expire = NULL){
		if($compress == NULL) $compress	 = $this->default_compress();
		if($expire == NULL) $expire		 = $this->default_expire();
		$key		 = $this->_fixKey($key);
		$con		 = $this->_getConForKey($key);
		if($con === false) return false;
		$return		 = $con->set($key, $var, $compress?MEMCACHE_COMPRESSED:false, $expire);
		return $return;
	}
	public function add($key, $var, $compress = NULL, $expire = NULL){
		if($compress == NULL) $compress	 = $this->default_compress();
		if($expire == NULL) $expire		 = $this->default_expire();
		$key		 = $this->_fixKey($key);
		$con		 = $this->_getConForKey($key);
		if($con === false) return false;
		$return		 = $con->add($key, $var, $compress, $expire);
		return $return;
	}
	public function replace($key, $var, $compress = NULL, $expire = NULL){
		if($compress == NULL) $compress	 = $this->default_compress();
		if($expire == NULL) $expire		 = $this->default_expire();
		$key		 = $this->_fixKey($key);
		$con		 = $this->_getConForKey($key);
		if($con === false) return false;
		$return		 = $con->replace($key, $var, $compress, $expire);
		return $return;
	}
	public function delete($key, $timeout = 0){
		$key	 = $this->_fixKey($key);
		$con	 = $this->_getConForKey($key);
		if($con === false) return false;
		$return	 = $con->delete($key, $timeout);
		return $return;
	}
	public function increment($key, $value = 1){
		$key	 = $this->_fixKey($key);
		$con	 = $this->_getConForKey($key);
		if($con === false) return false;
		$return	 = $con->increment($key, $value);
		return $return;
	}
	public function decrement($key, $value = 1){
		$key	 = $this->_fixKey($key);
		$con	 = $this->_getConForKey($key);
		if($con === false) return false;
		$return	 = $con->decrement($key, $value);
		return $return;
	}
	/*
	 * TO IMPLEMENT AT A LATER TIME
	  public function addServer($host, $port = '11211', $persistent = true, $weight = 1, $timeout = 1, $retry_interval = 15){
	  if(function_exists('memcache_add_server')){
	  if(count($this->connections) > 0){
	  foreach($this->connections as $con){
	  $con->addServer($host, $port, $persistent, $weight, $timeout, $retry_interval);
	  }
	  }else{
	  $con = @memcache_add_server($host, $port, $persistent, $weight, $timeout, $retry_interval);
	  if($con !== false){
	  $this->connections[] = $con;
	  }
	  }
	  }
	  }
	 */
	public function connect($host, $port = '11211', $timeout = 1){
		if(function_exists('memcache_connect')){
			$this->connect[] = ['host'=>$host, 'port'=>$port, 'timeout'=>$timeout, 'connected'=>false, 'persistant'=>false];
		}
		$this->connected = false;
	}
	public function pconnect($host, $port = '11211', $timeout = 1){
		if(function_exists('memcache_connect')){
			$this->connect[] = ['host'=>$host, 'port'=>$port, 'timeout'=>$timeout, 'connected'=>false, 'persistant'=>false];
		}
		$this->connected = false;
//		if(function_exists('memcache_pconnect')){
//			$con = @memcache_pconnect($host, $port, $timeout);
//			if($con !== false){
//				$this->connections[] = $con;
//			}
//		}
	}
	public function _connect(){
		if(function_exists('memcache_connect') && count($this->connect) > 0){
//			$this->connect = ['host'=>$host, 'port'=>$port, 'timeout'=>$timeout];
			foreach($this->connect as $k=> $connect){
				if($connect['connected'] == false){
					if(!$connect['persistant']){
						$con = @memcache_connect($connect['host'], $connect['port'], $connect['timeout']);
					}else{
						$con = @memcache_pconnect($connect['host'], $connect['port'], $connect['timeout']);
					}
					if($con !== false){
						$this->connections[] = $con;
					}
					$this->connect[$k]['connected'] = true;
				}
			}
		}
		$this->connected = true;
	}
}

?>