<?php

class uri {
	private $GET				 = array();
	private $POST				 = array();
	private $GETnum				 = array(NULL);
	private $extraGET			 = array();
	private $add_extraGET		 = false;
	private $delimiter			 = '/';
	private $hide_self			 = false;
	private $hide_host			 = false;
	private $found_extension	 = '';
	private $extension			 = '.html';
	private $accepted_extensions = array('.html');
	private $hide_keys			 = false;
	private $alias				 = array();
	private $uri_defaults		 = array();
	private $canonical_uri		 = false;
	/**
	 * Initialize the uri
	 *
	 * @access	private
	 * @param	config object
	 * @return	void
	 */
	public function __construct(){
		$config = lc('config')->load('uri');
		$this->set_delimiter($config->get('uri_delimiter', '/'));
		$this->set_hide_self($config->get('uri_hide_self', false));
		$this->set_hide_host($config->get('uri_hide_host', false));
		$this->set_extension($config->get('uri_extension', '.html'));
		$this->set_accepted_extensions($config->get('uri_accepted_extensions', false));
		$this->set_hide_keys($config->get('uri_hide_keys', false));

		$this->set_default_uri_domain($config->get('uri_default_domain', NULL));
		$this->set_default_uri_page($config->get('uri_default_page', NULL));
		$this->set_default_uri_secure_mode($config->get('uri_default_secure_mode', NULL));
		$this->set_default_uri_port($config->get('uri_default_port', NULL));

		$this->set_alias($config->get('uri_alias', array()));
		$config->unload('uri'); //cleaning up memory
		unset($config);

		$this->_fill_GET();
		$this->_fill_POST();
//		$host	= $_SERVER['HTTP_HOST'];
//		if (strrpos($host, ':') > 0){
//			$host = substr($host, 0, strrpos($host, ':'));
//		}
//		parent::$config->set('uri_host',$host);
	}
	public function set_default_uri_domain($value = NULL){
		$this->uri_defaults['domain'] = trim($value) != ''?trim($value):NULL;
	}
	public function set_default_uri_page($value = NULL){
		$this->uri_defaults['page'] = trim($value) != ''?trim($value):NULL;
	}
	public function set_default_uri_secure_mode($value = NULL){
		$this->uri_defaults['secure'] = ($value === true || $value === false)?$value:NULL;
	}
	public function set_default_uri_port($value = NULL){
		$this->uri_defaults['port'] = ((int)$value > 0)?(int)$value:NULL;
	}
	public function set_delimiter($delimiter = '/'){
		//only the first char
		$this->delimiter = substr($delimiter, 0, 1);
	}
	public function set_hide_self($hide_self = false){
		$this->hide_self = (bool)$hide_self;
	}
	public function set_hide_host($hide_host = false){
		$this->hide_host = (bool)$hide_host;
	}
	public function set_extension($extension = '.html'){
		$this->extension = $extension;
	}
	public function get_extension(){
		return $this->found_extension;
	}
	public function set_accepted_extensions($accepted_extensions = array()){
		if(!is_array($accepted_extensions)){
			$accepted_extensions = array($this->extension);
		}
		$this->accepted_extensions = $accepted_extensions;
	}
	public function set_hide_keys($hide_keys = false){
		$this->hide_keys = (bool)$hide_keys;
	}
	public function set_alias($alias = array()){
		$this->alias = $alias;
	}
	public function check_uri($uri){
		if(trim($uri) == '' || (
				!preg_match('/^((https?|ftp):\/\/|www\.)([^ \r\(\)\*\^\$!`"\'\|\[\]\{\};<>]*)/si', $uri) &&
				!preg_match('/^mailto:[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*$/i', $uri))) return false;
		return true;
	}
	public function format_uri($uri, $title = '', $maxwidth = 60, $width1 = 40, $width2 = -18){
		if(trim($title) == '') $title	 = $uri;
		if(strlen($title) > $maxwidth) $title	 = substr($title, 0, $width1).'...'.($width2 != 0?substr($title, $width2):'');

		return '<a href="'.$uri.'" target="_top" title="'.str_replace('"', '&quot;', $uri).'">'.$title.'</a>';
	}
	public function file($key = '', $default = NULL){
		$return = $default;
		if(isset($_FILES)){
			if($key == ''){
				$return = $_FILES;
			}elseif(isset($_FILES[$key])){
				$return = $_FILES[$key];
			}else{
				$return = $default;
			}
		}
		return $return;
	}
	public function is_post(){
		return !empty($this->POST);
	}
	public function post($key = '', $default = NULL){
		if($key == ''){
			return $this->get_POST();
		}elseif(isset($this->POST[$key])){
			return $this->POST[$key];
		}else{
			return $default;
		}
	}
	public function get($key = '', $default = NULL){
		if($key == ''){
			return $this->get_GET();
		}elseif(isset($this->GET[$key])){
			return $this->GET[$key];
		}elseif(is_numeric($key) && count($this->GETnum) > $key){
			return $this->GETnum[$key];
		}else{
			return $default;
		}
	}
	public function get_num($key = 0, $default = NULL){
		if($key <= 0){
			return $this->get_GETnum();
		}elseif(is_numeric($key) && count($this->GETnum) > $key){
			return $this->GETnum[$key];
		}else{
			return $default;
		}
	}
	/**
	 * Setting the variable passed into the GET array
	 *
	 * @access	public
	 * @param	$key string - the key to set/change
	 * @param	$value string - the value that will assigned to $key
	 * @return	void
	 */
	public function set($key, $value){
		$this->GET[$key] = $value;
		if($this->hide_keys == true && $key == CLASS_KEY && is_array($this->GETnum)){
			array_unshift($this->GETnum, $key, $value);
		}else{
			if(in_array($key, $this->GETnum)){
				$k						 = array_search($key, $this->GETnum);
				$this->GETnum[$k]		 = $key;
				$this->GETnum[$k + 1]	 = $value;
			}else{
				$this->GETnum[]	 = $key;
				$this->GETnum[]	 = $value;
			}
		}
	}
	/**
	 * This function will allow you to set a post variable
	 *
	 * @access	public
	 * @param	key string Variable name
	 * @param	key string Variable value
	 * @return	void
	 */
	public function set_post($key, $value){
		$this->POST[$key] = $value;
	}
	/**
	 * Deleting a GET variable
	 */
	public function delete($key){
		if($key != CLASS_KEY && is_array($this->GET) && isset($this->GET[$key])){
			unset($this->GET[$key]);
			if(in_array($key, $this->GETnum)){
				$k				 = array_search($key, $this->GETnum);
				unset($this->GETnum[$k]);
				unset($this->GETnum[$k + 1]);
				$this->GETnum	 = array_values($this->GETnum); //let's reindex
			}
		}
	}
	/**
	 * Deleting a POST variable
	 */
	public function delete_post($key){
		if($key != CLASS_KEY && is_array($this->POST) && isset($this->POST[$key])){
			unset($this->POST[$key]);
		}
	}
	/**
	 * This function returns the internal POST variable
	 *
	 * @access	public
	 * @param	void
	 * @return	array() the POST coming from the URL
	 */
	public function get_POST(){
		return $this->POST;
	}
	/**
	 * This function returns the internal GET variable
	 *
	 * @access	public
	 * @param	void
	 * @return	array() the GET coming from the URL
	 */
	public function get_GET(){
		return $this->GET;
	}
	/**
	 * This function returns the internal GETnum variable
	 *
	 * @access	public
	 * @param	void
	 * @return	array() the GETnum coming from the URL
	 */
	public function get_GETnum(){
		return $this->GETnum;
	}
	/**
	 * This function will fill the _POST global variable
	 *
	 * @access	private
	 * @param	void
	 * @return	void
	 */
	private function _fill_POST(){
		//this function just grab the POST variables
		$this->POST = $this->_clean_magic_quotes($_POST);  //This is needed in case there are some manually passed POST info
	}
	/**
	 * This function will grab the query string and subst the aliases as necessary
	 *
	 * @access    private
	 * @param    $qry
	 * @return    $qry
	 */
	private function _process_alias($qry){
		foreach($this->alias as $key => $value){
			$qry = preg_replace('*'.$key.'*', $value, $qry);
		}
		return $qry;
	}
	/**
	 * This function will return the current page URI
	 *
	 * @access	public
	 * @param boolean full return the path with the URL
	 * @param boolean secure force the returned URL to be secure
	 * @return	string The current URI without the domain
	 */
	public function get_uri($full = false, $secure = false){
		$request_uri = '';
		if($request_uri == '') $request_uri = isset($_SERVER['REQUEST_URI'])?str_replace('?'.$_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']):'';
		if($request_uri == '') $request_uri = isset($_SERVER['REDIRECT_URL'])?$_SERVER['REDIRECT_URL']:''; //.((isset($_SERVER['REDIRECT_QUERY_STRING']) && $_SERVER['REDIRECT_QUERY_STRING']!='')?'?'.$_SERVER['REDIRECT_QUERY_STRING']:''):'';
		if($request_uri == '') $request_uri = isset($_SERVER['PATH_INFO'])?$_SERVER['PATH_INFO']:'';
		if($full || $secure){
			$domain = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'';
			if($secure){
				$prefix = 'https';
			}else{
				$prefix = isset($_SERVER['HTTPS'])?'https':'http';
			}
			$request_uri = $prefix.'://'.$domain.$request_uri;
		}
		return $request_uri;
	}
	/**
	 * return current cacnonical URI
	 * with a https as protocol
	 */
	public function get_canonical_uri(){
		if($this->canonical_uri === false){
			$this->canonical_uri = $this->create_uri_from_uri(
					$this->get_uri(), array(
				CLASS_KEY	 => $this->get(CLASS_KEY),
				TASK_KEY	 => $this->get(TASK_KEY),
				'_EXT'		 => $this->found_extension == ''?$this->extension:$this->found_extension
					)
			);
		}
		return $this->canonical_uri;
	}
	/**
	 * set and override the current cacnonical URI
	 */
	public function set_canonical_uri($canonical_uri){
		$this->canonical_uri = $canonical_uri;
		return $this;
	}
	/**
	 * return current full URI
	 * with the domain included
	 */
	public function get_full_uri(){
		return $this->get_uri(true);
		/*
		  $uri	= $this->get_uri();
		  $domain	= isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'';
		  $prefix	= isset($_SERVER['HTTPS'])?'https':'http';
		  return $prefix.'://'.$domain.$uri;
		 */
	}
	/**
	 * return current full secure URI
	 * with a https as protocol
	 */
	public function get_full_secure_uri(){
		return $this->get_uri(true, true);
		/*
		  $uri	= $this->get_uri();
		  $domain	= isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'';
		  $prefix	= 'https';
		  return $prefix.'://'.$domain.$uri;
		 */
	}
	/**
	 * This function will return the referrer to this page
	 *
	 * @access	public
	 * @param	void
	 * @return	string The full referref
	 */
	public function get_referrer(){
		return isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'';
	}
	private function _parse_path($request_uri){
		$GET	 = array();
		$GETnum	 = array();
		if(trim($request_uri) != ''){
			if(is_array($this->accepted_extensions) && $this->hide_self){
				foreach($this->accepted_extensions as $tmp){
					if($tmp != false && $tmp != '' && substr($request_uri, -1 * strlen($tmp)) == $tmp){
						$this->found_extension	 = substr($tmp, 1);
						$request_uri			 = substr($request_uri, 0, -1 * strlen($tmp));
						break;
					}
				}
			}
			//remove index
			$query_string	 = preg_replace('/^'.preg_quote(WEBPATH, '/').'('.preg_quote(SELF, '/').'(\/)?)?/', '', $request_uri);
			//subst aliases
			$query_string	 = $this->_process_alias($query_string);

			$key2	 = '';
			$value	 = '';
			$kk		 = '';
			if($query_string != '' && $query_string != '/'){
				//Split it out.
				$tmp = explode($this->delimiter, $query_string);
				for($i = 0; $i <= count($tmp) - 1; $i+=2){
					if(strpos($tmp[$i], '?') !== false && strstr($tmp[$i], '?') != '?'){
						$tmp1	 = explode('?', $tmp[$i]);
						parse_str(isset($tmp1[1])?$tmp1[1]:'', $value);
						$i--;
						$key	 = $this->_clean_magic_quotes($tmp1[0]);
						$value	 = $this->_clean_magic_quotes($value);
						if(isset($value[session_name()])){
							unset($value[session_name()]);
							if($value == array()){
								$value = '';
							}
						}

						parse_str($key, $kk);
						unset($key2);
						if(is_array($kk)){
							if(empty($kk)){
								$key = '';
							}else{
								list($key) = array_keys($kk);
								if(is_array($kk[$key])){
									list($key2) = array_keys($kk[$key]);
								}
							}
						}
						$GETnum[]	 = $key;
						$GETnum[]	 = $value;
						if(isset($GET[$key])){
							if(!is_array($GET[$key])){
								if(isset($key2)){
									$GET[$key] = array($key2 => $GET[$key]);
								}else{
									$GET[$key] = array($GET[$key]);
								}
							}
							if(isset($key2)){
								$GET[$key][$key2] = $value;
							}else{
								$GET[$key] = array_merge($GET[$key], $value);
							}
						}else{
							if(isset($key2)){
								$GET[$key][$key2] = $value;
							}else{
								$GET[$key] = $value;
							}
						}
					}else{
						if($i == 0 && $this->hide_keys == true){
							$key		 = CLASS_KEY;
							$value		 = urldecode($tmp[$i]);
							$GETnum[]	 = $key;
							$GETnum[]	 = $value;
							$GET[$key]	 = $value;

							$key		 = TASK_KEY;
							$value		 = urldecode(isset($tmp[$i + 1])?$tmp[$i + 1]:'');
							$GETnum[]	 = $key;
							$GETnum[]	 = $value;
							$GET[$key]	 = $value;
						}else{
							$key	 = urldecode($tmp[$i]);
							$key2	 = false;
							$value	 = urldecode(isset($tmp[$i + 1])?$tmp[$i + 1]:'');
							if(strpos($value, '?') !== false){
								$value = substr($value, 0, strpos($value, '?'));
							}
							$GETnum[]	 = $key;
							$GETnum[]	 = $value;
							//is this something like $key[$k]
							$t			 = preg_match('/(.*)\[(.*)\]/', $key, $match);

							if($t > 0){
								$key	 = $match[1];
								$key2	 = $match[2];
							}
							if(trim($key2) != ''){
								if(isset($GET[$key])){
									if(!is_array($GET[$key])){
										$GET[$key] = array($GET[$key]);
									}
									$GET[$key][$key2] = $value;
								}else{
									$GET[$key][$key2] = $value;
								}
							}else{
								if(isset($GET[$key])){
									if(!is_array($GET[$key])){
										$GET[$key] = array($GET[$key]);
									}
									$GET[$key][] = $value;
								}else{
									$GET[$key] = $value;
								}
							}
						}
					}
				}
			}
		}
		return array('GET' => $GET, 'GETnum' => $GETnum);
	}
	/**
	 * This function will fill the _GET global variable
	 *
	 * @access	private
	 * @param	void
	 * @return	void
	 */
	private function _fill_GET($uri = NULL){
		/**
		 * This function converts info.php/a/1/b/2/c?d=4 TO
		 * array ( [d] => 4 [a] => 1 [b] => 2 [c] => array ( [d] => 4 ) )
		 * got this function from http://php.net/GLOBALS
		 * */
		if(isset($_GET[0])) unset($_GET[0]);
		$request_uri	 = is_null($uri)?$this->get_uri():$uri;
		$tmp			 = $this->_parse_path($request_uri);
		$this->GET		 = $tmp['GET'];
		$this->GETnum	 = $tmp['GETnum'];
		if(is_array($_GET)){
			$this->sanitize_get_array();
			foreach($_GET as $k => $v){
				$this->GET[$k] = str_replace('%3F', '?', $v);  //This is needed in case there are some manually passed GET info
			}
		}
		$_GET = $this->GET;
	}
	public function fill_get_from_uri($uri){
		$_GET		 = array();
		$this->GET	 = array();
		$parsed		 = @parse_url($uri);
		$path		 = '';
		if(is_array($parsed)){
			if(isset($parsed['path'])) $path .= $parsed['path'];
			if(isset($parsed['query'])) $path .= '?'.$parsed['query'];
			if(isset($parsed['fragment'])) $path .= '#'.$parsed['fragment'];
			$this->_fill_GET($path);
		}
		return $this;
	}
	/**
	 * This function will modify the $_GET variable
	 * by cleaning it from possible broken keys/values
	 * and try to sanitize it (? will be the char that this functions looks for)
	 * for example:
	 *  - array(1) {
	 * 				["QSCSID"]	=>  "si4als90014mbbheu30slma1u7?NP=/a/login.html"
	 * 		}
	 * will be converted into
	 *  - array(2) {
	 * 				["QSCSID"]	=>  "si4als90014mbbheu30slma1u7"
	 * 				["NP"]		=>  "/a/login.html"
	 * 		}
	 * but
	 *  - array(1) {
	 * 				["QSCSID"]	=>  "si4als90014mbbheu30slma1u7?12345"
	 * 		}
	 * won't change
	 */
	public function sanitize_get_array(){
		// TO-DO: It can be implemented also a sanitation meaning to accept only certain parameters or to exclude others
		if(is_array($_GET)){
			foreach($_GET as $k => $v){
				if(is_string($v) && strstr($v, '?') !== false){
					$ret = explode('?', $v);
					if(strstr($ret[1], '=') !== false){
						$_GET[$k] = $ret[0];
						if(count($ret) > 1){
							foreach($ret as $kk => $vv){
								if($kk > 0){
									$rett = array();
									parse_str($vv, $rett);
									if(is_array($rett)){
										foreach($rett as $key => $val){
											$_GET[$key] = $val;
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
	/**
	 * This function will simple strip out the slashes if needed
	 *
	 * @access  public
	 * @param   string/array  value to clean
	 * @return  string/array cleaned
	 */
	public function _clean_magic_quotes($string){
		if(get_magic_quotes_gpc()){
			if(is_array($string)){
				$nstring = array();
				foreach($string as $key => $value){
					$nstring[$this->_clean_magic_quotes($key)] = $this->_clean_magic_quotes($value);
				}
				$string = $nstring;
			}else{
				$string = stripslashes($string);
			}
		}
		return $string;
	}
	/**
	 * This function will parse an URI and will try to create the parameters to pass to create_uri
	 * Remember, you cannot pass a value that is only a single / in the first level of the array (key or value)
	 * It must be preceded by a ?
	 *
	 * @access	public
	 * @param	query array(): parameters that needs to be passed to the GET
	 * @param	page string: The page to use in the URL, if NULL will use the current page. if empty will return only the query string
	 * @param	domain string: The domain to use in the URL, if NULL will use the current domain
	 * @param	secure boolean: If the URI created should be a secure URL (https), if NULL will use the current status
	 * @param	port integer: The port to use in the URL, if NULL will use the current port
	 * @return	string
	 */
	public function create_uri_from_uri($uri, $query = NULL, $page = NULL, $domain = NULL, $secure = NULL, $port = NULL){
		$parsed = @parse_url($uri);
		if(is_array($parsed)){
			if(is_null($secure)){
				if(isset($parsed['scheme'])){
					switch(strtolower($parsed['scheme'])){
						case 'https':
							$secure	 = true;
							break;
						default:
							$secure	 = false;
					}
				}
			}
			if(isset($parsed['path'])){
				$tmp = $this->_parse_path($parsed['path']); //get the GET and GETnum
				if(!is_null($query) && is_array($query)){
					foreach($query as $k => $v){
						$tmp['GET'][$k] = $v;
					}
				}
				$query = $tmp['GET'];
			}
			if(isset($parsed['query'])){
				$qq = array();
				parse_str($parsed['query'], $qq);
				foreach($qq as $k => $v){
					if(!isset($query[$k])){
						$query[$k] = $v;
					}
				}
			}
			if(!isset($query['_EXT'])){
				$query['_EXT'] = $this->found_extension;
			}
			$domain	 = (is_null($domain) && isset($parsed['host']))?$parsed['host']:$domain;
			$port	 = (is_null($port) && isset($parsed['port']))?$parsed['port']:$port;
		}
		return $this->create_uri($query, $page, $domain, $secure, $port);
	}
	/**
	 * This function get as parameter a URI and a key
	 * and it returns suck key once that uri is parsed
	 * for example get_from_uri('http://my.site.com/account/login.html','account','def');
	 * will return "login"
	 * if $key is NULL or not passed, then an associated array will be returned
	 */
	public function get_from_uri($uri, $key = NULL, $default = NULL){
		$parsed	 = @parse_url($uri);
		$query	 = array();
		if(is_array($parsed)){
			if(isset($parsed['path'])){
				$tmp	 = $this->_parse_path($parsed['path']); //get the GET and GETnum
				$query	 = $tmp['GET'];
			}
			if(isset($parsed['query'])){
				$qq = array();
				parse_str($parsed['query'], $qq);
				foreach($qq as $k => $v){
					if(!isset($query[$k])){
						$query[$k] = $v;
					}
				}
			}
		}
		if(is_null($key)){
			$return = $query;
		}else{
			$return = isset($query[$key])?$query[$key]:$default;
		}
		return $return;
	}
	/**
	 * This function will create a uri from an array
	 * Remember, you cannot pass a value that is only a single / in the first level of the array (key or value)
	 * It must be preceded by a ?
	 *
	 * @access	public
	 * @param	query array(): parameters that needs to be passed to the GET
	 * @param	page string: The page to use in the URL, if NULL will use the current page. if empty will return only the query string
	 * @param	domain string: The domain to use in the URL, if NULL will use the current domain
	 * @param	secure boolean: If the URI created should be a secure URL (https), if NULL will use the current status
	 * @param	port integer: The port to use in the URL, if NULL will use the current port
	 * @return	string
	 */
	public function create_uri($query = NULL, $page = NULL, $domain = NULL, $secure = NULL, $port = NULL){
		if(is_null($page)) $page	 = $this->uri_defaults['page'];
		if(is_null($domain)) $domain	 = $this->uri_defaults['domain'];
		if(is_null($secure)) $secure	 = $this->uri_defaults['secure'];
		if(is_null($port)) $port	 = $this->uri_defaults['port'];
		$_EXT	 = $this->extension;
		if(is_array($query) && count($query) > 0){
			$v	 = end($query);
			$t	 = key($query);
			if($t === '_EXT'){
				$_EXT = $v;
				array_pop($query);
			}
		}
		$_EXT = trim($_EXT);
		if($_EXT != '' && substr($_EXT, 0, 1) != '.'){
			$_EXT = '.'.$_EXT;
		}

		if($domain == '' && $secure === true){
			$domain = $this->uri_defaults['domain'];
		}elseif($domain == 'auto'){
			$domain = NULL;
		}
		if(!isset($_SERVER['HTTP_HOST'])) $_SERVER['HTTP_HOST']	 = '';
		if($domain === NULL) $domain					 = (strrpos($_SERVER['HTTP_HOST'], ':') > 0)?substr($_SERVER['HTTP_HOST'], 0, strrpos($_SERVER['HTTP_HOST'], ':')):$_SERVER['HTTP_HOST'];
		if($page === NULL) $page					 = WEBPATH.SELF;
		if(!is_array($query)) $query					 = $this->GET;

		$newQry = array();
		if($this->add_extraGET && is_array($this->extraGET) && !empty($this->extraGET)){
//			$query = array_merge($this->extraGET, $query);		//this renumber the keys
			$query = $query + $this->extraGET;  //this adds only the new keys
		}
		if($this->hide_keys == true){
			if(isset($query[CLASS_KEY])){
				$newQry[] = urlencode($query[CLASS_KEY]);
			}else{
				$newQry[] = urlencode($this->get(CLASS_KEY));
			}
			if(isset($query[TASK_KEY]) && !(count($query) == 2 && isset($query[CLASS_KEY]) && $query[TASK_KEY] == '')){
				$newQry[] = urlencode($query[TASK_KEY]);
			}elseif(!isset($query[CLASS_KEY])){
				//only get the current if the CLASS KEY is not manually set
				$newQry[] = urlencode($this->get(TASK_KEY));
			}
			unset($query[CLASS_KEY]);
			unset($query[TASK_KEY]);
		}
		$add_session = false;
		$qry_added	 = false; //is there already a ? in the query
		foreach($query as $key => $item){
			if(is_array($item)){
				foreach($item as $kk => $vv){
					if(is_array($vv) && is_numeric($kk)){
						$newQry[]	 = urlencode($key).'?'.http_build_query($vv);
						$qry_added	 = true;
					}elseif(is_array($vv)){
						$newQry[]	 = urlencode($key.'['.$kk.']').'?'.http_build_query($vv);
						$qry_added	 = true;
					}elseif(!is_numeric($kk)){
						$newQry[]	 = urlencode($key.'['.$kk.']').$this->delimiter.urlencode($vv);
						$qry_added	 = true;
					}else{
						$newQry[] = urlencode($key).$this->delimiter.urlencode($vv);
					}
				}
			}else{
				//0 is not considered an usable keys
				if($key === 0){ //first 3 may be just a number
					$newQry[] = urlencode($item);
				}elseif($key == session_name()){
					$add_session = true;
				}else{
					$newQry[] = urlencode($key).$this->delimiter.urlencode($item);
				}
			}
		}

		$query = implode($this->delimiter, $newQry);
		if(substr($query, -1) == $this->delimiter){
			$query = substr($query, 0, -1);
		}

		if((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' && $secure !== false) || $secure === true){
			$host	 = 'https://';
			$secure	 = true;
		}else{
			$host	 = 'http://';
			$secure	 = false;
		}
		if($domain === NULL){
			if(strrpos($_SERVER['HTTP_HOST'], ':') > 0){
				$host .= substr($_SERVER['HTTP_HOST'], 0, strrpos($_SERVER['HTTP_HOST'], ':'));
			}else{
				$host .= $_SERVER['HTTP_HOST'];
			}
		}else{
			$host .= $domain;
//			if(strpos($domain.$page,$this->delimiter)===false)	$host .= $this->delimiter;
		}

		if($port == NULL){
			//check the current port used
			$port	 = isset($_SERVER['SERVER_PORT'])?$_SERVER['SERVER_PORT']:0;
			if($port <= 0) $port	 = 80;
		}
		if($port != 80 && $port != 443){
			$host .=':'.$port;
		}
		if($page === ''){
			$ret = $query;
		}else{
			if($this->hide_self) $page = '';
			//if host is the same as the current, do not show it
			if($this->hide_host && preg_match('/^http'.(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on').':\/\/('.$_SERVER['SERVER_NAME'].')/i', $host)){
				$ret = $page.($query != ''?'/'.$query:'');
			}else{
				$ret = $host.$page.($query != ''?'/'.$query:'');
			}
			if($this->hide_self && $_EXT != false && $_EXT != ''){
				$ret .= $_EXT;
			}
		}
		if($add_session === true && trim(session_id()) != ''){
			$ret .= ((strpos($ret, '?') === false)?'?':'&').session_name().'='.session_id();
		}
		return $ret;
	}
	public function create_secure_uri($query = NULL, $page = NULL, $domain = NULL){
		return $this->create_uri($query, $page, $domain, true, NULL);
	}
	public function create_auto_uri($query = NULL, $page = NULL, $domain = NULL){
		return $this->create_uri($query, $page, $domain, 'auto', NULL);
	}
	/**
	 * This function will be used to automatically add keys to the create_link function w/o the needs to pass them over and over
	 *
	 * @access	public
	 * @param	key: the key that needs to badded
	 * @param	value: The coresponding value
	 * @return	string
	 */
	public function auto_add($key, $value){
		$this->extraGET[$key] = $value;
		$this->set_auto_add(true);
		return $this;
	}
	public function set_auto_add($value = true){
		$this->add_extraGET = $value;
		return $this;
	}
	public function reset_auto_add(){
		$this->set_auto_add(false);
		$this->extraGET = array();
		return $this;
	}
	public function get_auto_add(){
		return $this->extraGET;
	}
	public function get_domain(){
		return $this->get_host();
	}
	public function get_host(){
		$host = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'';
		if(strrpos($host, ':') > 0){
			$host = substr($host, 0, strrpos($host, ':'));
		}
		return $host;
	}
}

?>