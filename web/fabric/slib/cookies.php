<?php

//this class is intended to help with the handling of the cookies

class cookies{
	private $config;
	public function __construct(){
		$this->config['prefix']		 = '';
		$this->config['domain']		 = '';
		$this->config['expire']		 = 0;
		$this->config['path']		 = '/';
		$this->config['secure']		 = false;
		$this->config['httponly']	 = false;
//		$config = lc('config')->get_and_unload_config('cookies');	//cleaning up memory
		$config						 = lc('config')->get_config('cookies'); //used in sessions as well, not a big deal to keep it in memory
		if(is_array($config)){
			foreach($config as $key=> $value){
				$this->config[$key] = $value;
			}
		}
	}
	/**
	 * Set cookie
	 *
	 * Accepts six parameter, or you can submit an associative
	 * array in the first parameter containing all the values.
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string	the value of the cookie
	 * @param	string	the number of seconds until expiration
	 * @param	string	the cookie domain.  Usually:  .yourdomain.com
	 * @param	string	the cookie path
	 * @param	string	the cookie set secure
	 * @return	void
	 */
	public function setcookie($name = '', $value = '', $expire = 0, $domain = NULL, $path = '/', $secure = false){
		//alias for set
		return $this->set($name, $value, $expire, $domain, $path, $secure);
	}
	public function set($name = '', $value = '', $expire = 0, $domain = NULL, $path = '/', $secure = false, $httponly = false){
		if(headers_sent()){
			return false;
		}else{
			$time = time(); //this needs to be constant in this function
			if(is_array($name)){
				foreach(array('name', 'value', 'expire', 'domain', 'path', 'secure') as $tmp){
					if(isset($name[$tmp])){
						$$tmp = $name[$tmp];
					}
				}
			}
			//cleaning cookes:
			if($name != '' && strpbrk($name, "=,; \t\r\n\013\014") != NULL){
				return false;
			}

			// Set the config file options
			$prefix = $this->config['prefix'];
			if(is_null($domain)){
				if($this->config['domain'] != ''){
					$domain = $this->config['domain'];
				}
				//RFC2109: The Domain attribute specifies the domain for which the cookie is valid. An explicitly specified domain must always start with a dot.
				if($domain != '' && substr($domain, 0, 1) != '.'){
					$domain = '.'.trim($domain);
				}
			}
			if($prefix == '' && $this->config['prefix'] != ''){
				$prefix = $this->config['prefix'];
			}
			if(!is_numeric($expire) && is_numeric($this->config['expire'])){
				$expire = $this->config['expire'];
			}
			if($httponly == '' && $this->config['httponly'] != ''){
				$httponly = $this->config['httponly'];
			}
			if($expire != 0){
				$expire = $time + $expire;
			}else{
				$expire = 0;
			}
			if($expire != 0 && $expire < $time){
				unset($_COOKIE[$prefix.$name]);
				$value = '';
			}else{
				$_COOKIE[$prefix.$name] = $value;
			}
			$return = setcookie($prefix.$name, $value, $expire, $path, $domain, $secure, $httponly);
			return $return?$this:$return;
		}
	}
	/**
	 * Fetch an item from the COOKIE array
	 *
	 * @access	public
	 * @param	string
	 * @param	bool
	 * @return	mixed
	 */
	public function getcookie($key = ''){
		//alias for get
		return $this->get($key);
	}
	public function get_all(){
		$return = array();
		if(is_array($_COOKIE)){
			foreach($_COOKIE as $k=> $v){
				$k			 = substr($k, -1 * (strlen($k) - strlen($this->config['prefix'])));
				$return[$k]	 = $v;
			}
		}
		return $return;
	}
	public function get($key = '', $default = NULL){
		if(isset($_COOKIE[$this->config['prefix'].$key])){
			return $_COOKIE[$this->config['prefix'].$key];
		}else{
			return $default;
		}
	}
	/**
	 * Delete a COOKIE
	 *
	 * @param	mixed
	 * @param	string	the cookie domain.  Usually:  .yourdomain.com
	 * @param	string	the cookie path
	 * @return	void
	 */
	public function deletecookie($name = '', $domain = '', $path = '/'){
		//alias for delete
		return $this->delete($name, $domain, $path);
	}
	public function delete($name = '', $domain = NULL, $path = '/'){
		return $this->set($name, '', -3600, $domain, $path);
	}
	/**
	 * Delete all the COOKIEs
	 * You manually have to remove the cookie associate with the session, this is done to avoid unwanted problems
	 *
	 * @return	void
	 */
	public function delete_all($domain = '', $path = '/'){
		if(is_array($_COOKIE)){
			foreach($_COOKIE as $key=> $value){
				if($key != session_name()){
					$this->delete($key, $domain, $path);
				}
			}
		}
		return $this;
	}
	public function set_config($var, $value){
		if(isset($this->config[$var])){
			$this->config[$var] = $value;
		}
		return $this;
	}
}

?>