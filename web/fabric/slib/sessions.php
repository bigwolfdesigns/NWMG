<?php

//this class is intended to help with the handling of the cookies

class sessions{
	const SESSION_STARTED			 = TRUE;
	const SESSION_NOT_STARTED		 = FALSE;
	private $config;
	// The state of the session
	private $sessionState	 = self::SESSION_NOT_STARTED;
	// THE only instance of the class
	private static $instance;
	/**
	 *    Returns THE instance of 'Session'.
	 *    The session is automatically initialized if it wasn't.
	 *
	 *    @return    object
	 * */
	public static function getInstance(){
		if(!isset(self::$instance)){
			self::$instance = new self;
		}
		self::$instance->startSession();
		return self::$instance;
	}
	public function __construct(){
		$this->config['name']		 = 'bibSid';
		$this->config['prefix']		 = '';
		$this->config['lifetime']	 = false;
		$this->config['savepath']	 = false;
		$this->config['linksid']	 = false;
		$this->config['driver']		 = 'files';
		$config						 = lc('config')->get_and_unload_config('sessions');
		if(is_array($config)){
			foreach($config as $key=> $value){
				$this->config[$key] = $value;
			}
		}
		//loading the cookies settings for the session from the cookie class
		$this->config['cookie_prefix']	 = '';
		$this->config['cookie_domain']	 = '';
		$this->config['cookie_expire']	 = 0;
		$this->config['cookie_path']	 = '/';
		$this->config['cookie_secure']	 = false;
		$this->config['cookie_httponly'] = false;
//		$config = lc('config')->get_and_unload_config('cookies');	//cleaning up memory
		$config							 = lc('config')->get_config('cookies'); //used in cookies as well, not a big deal to keep it in memory
		if(is_array($config)){
			foreach($config as $key=> $value){
				$this->config['cookie_'.$key] = $value;
			}
		}
		if($this->config['driver'] == 'db'){
			$handler = ll('sessions'.DIRECTORY_SEPARATOR.$this->config['driver']);
			if(is_object($handler)){
				session_set_save_handler(
						array($handler, 'open'), array($handler, 'close'), array($handler, 'read'), array($handler, 'write'), array($handler, 'destroy'), array($handler, 'gc')
				);
				// the following prevents unexpected effects when using objects as save handlers
				register_shutdown_function('session_write_close');
			}
		}
	}
	public function start(){
		if(headers_sent()){
			return false;
		}elseif($this->sessionState == self::SESSION_NOT_STARTED){
			if($this->config['lifetime'] !== false) ini_set('session.gc_maxlifetime', $this->config['lifetime']);
			if($this->config['savepath'] !== false) ini_set('session.save_path', $this->config['savepath']);
			if($this->config['linksid'] !== false) ini_set('session.use_trans_sid', 1);
			session_set_cookie_params($this->config['cookie_expire'], $this->config['cookie_path'], $this->config['cookie_domain'], $this->config['cookie_secure'], $this->config['cookie_httponly']);
			session_name($this->config['name']);
			//this needs to be done a bit better to avoid session injections
			//but it may work for now - GET first then POST
			if(isset($_GET[session_name()]) && trim($_GET[session_name()]) != ''){
				@session_id($this->_sanitize_session_id($_GET[session_name()]));
			}elseif(isset($_POST[session_name()]) && trim($_POST[session_name()]) != ''){
				@session_id($this->_sanitize_session_id($_POST[session_name()]));
			}
			$this->sessionState = session_start();
		}
		return $this->sessionState;
	}
	private function _sanitize_session_id($session_id){
		return preg_replace('/[^a-z0-9-,]/i', '', $session_id);
	}
	public function get_id(){
		$sid = session_id();
		if($sid == ''){
			//no session started??? let's start one
			session_start();
		}
		return session_id();
	}
	/**
	 * Set session
	 *
	 * Accepts six parameter, or you can submit an associative
	 * array in the first parameter containing all the values.
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string	the value of the session
	 * @return	void
	 */
	public function set($name = '', $value = ''){
		return $_SESSION[$this->config['prefix'].$name] = $value;
	}
	/**
	 * Fetch an item from the SESSION array
	 *
	 * @access	public
	 * @param	string
	 * @return	mixed
	 */
	public function get($key = '', $default = NULL){
		if(isset($_SESSION[$this->config['prefix'].$key])){
			return $_SESSION[$this->config['prefix'].$key];
		}else{
			return $default;
		}
	}
	/**
	 * Delete a SESSION
	 *
	 * @param	mixed
	 * @return	void
	 */
	public function delete($key = ''){
		if(isset($_SESSION[$this->config['prefix'].$key])){
			unset($_SESSION[$this->config['prefix'].$key]);
		}
	}
	/**
	 * Delete all the SESSIONs
	 *
	 * @return	void
	 */
	public function delete_all(){
		$_SESSION = array();
	}
	/**
	 * Get all the SESSIONs
	 *
	 * @return	mixed
	 */
	public function get_all(){
		if(is_array($_SESSION)){
			return $_SESSION;
		}else{
			return array();
		}
	}
	/**
	 * RESET a session, close the current session
	 *
	 * @return	void
	 */
	public function reset(){
		if($this->sessionState == self::SESSION_STARTED){
			$this->delete_all();
			if(ini_get('session.use_cookies')){
				$params = session_get_cookie_params();
				setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']
				);
				unset($_COOKIE[session_name()]);
			}
			$this->sessionState = !session_destroy();
			session_regenerate_id();
		}
		return $this->sessionState;
	}
	/**
	 * RESET a session, close the current session and creates a new one
	 *
	 * @return	void
	 */
	public function restart(){
		unset($_GET[session_name()]);
		unset($_POST[session_name()]);
		$this->reset();
		$this->start();
	}
	/**
	 *    Destroys the current session.
	 *
	 *    @return    bool    TRUE is session has been deleted, else FALSE.
	 * */
	public function destroy(){
		return $this->reset();
	}
}
?>