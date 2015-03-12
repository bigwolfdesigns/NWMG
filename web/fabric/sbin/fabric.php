<?php

if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This is where the magic starts
 */
class fabric {
	public $config;   //configuration class
	public $uri;   //hook class
	private $timer_start = 0;
	/**
	 * Initialize the fabric_core
	 *
	 * @access	public
	 * @param	none
	 * @return	void
	 */
	public function __construct(){
//		lc('exceptions');
//		$this->config = lc('config')->get_and_unload_config('fabric');
		$this->config = lc('config')->get_config('fabric'); //let's keep it in memoty in case this class is initialized multiple times
//		$_POST = lc('uri')->_clean_magic_quotes($_POST);

		date_default_timezone_set($this->config['TZ']);
		ini_set('arg_separator.output', $this->config['arg_separator']);
		ini_set('arg_separator.input', $this->config['arg_separator']);
		if(TMPPATH != ''){
			ini_set('upload_tmp_dir', TMPPATH);
		}else{
			$tmp_folder = $this->config['tmp_folder'];
			if($tmp_folder != ''){
				if(substr($tmp_folder, 0, 1) != DIRECTORY_SEPARATOR){
					$tmp_folder = FULLPATH.$tmp_folder;
				}
				ini_set('upload_tmp_dir', $tmp_folder);
			}
		}

		/*
		  //don't really need all of those....
		  //keep here in case I figure out that I need the later on
		  ini_set('session.use_trans_sid',0);
		  ini_set('url_rewriter.tags','a=href,area=href,frame=src,form=,fieldset=');
		  ini_set('file_uploads','1');
		  ini_set('allow_call_time_pass_reference','Off');
		  ini_set('allow_url_fopen','Off');
		  ini_set('max_input_time',120);
		  ini_set('assert.active','0');
		 */
	}
	static function redirect($location, $message = '', $timeout = 0, $force_html = false, $template_folder = ''){
		//if the header has already been sent redirect w/ javascript
		$args = '';
		if($template_folder != ''){
			$template_folder = $template_folder."/";
		}
		if(is_array($location)){
			$tmps		 = $location;
			$location	 = $tmps[0];
			$args		 = $tmps[1];
		}
		if(!fabric::cookie_check() || lc('uri')->get(session_name(), '') != ''){
			if(strpos($location, session_name().'='.session_id()) === false){
				$location .= ((strpos($location, '?') === false)?'?':'&').session_name().'='.session_id();
			}
		}
		if($args != ''){
			$location .= ((strpos($location, '?') === false)?'?':'&').$args;
		}
		if(headers_sent() || $force_html){
			if($message == ''){
				$message = 'redirecting to <a href="'.$location.'">'.$location.'</a><br /><br /><a href="'.$location.'">click here if you are not automatically redirected !</a>';
			}
			$message .='<script language="javascript" type="text/javascript"><!-- '."\n";
			if($timeout > 0) $message .='setTimeout("redirect_fabric();",'.($timeout * 1000).'); function redirect_fabric(){ ';
			$message .='window.location.replace="'.$location.'"; window.location.href="'.$location.'"; window.location="'.$location.'";';
			if($timeout > 0) $message .=' }';
			$message .= '// --></script>';
			ll('display')
					->assign('message', $message)
					->start(true, $template_folder)
					->show($template_folder.'message')
					->end(true, $template_folder);
			//this is to clean the rest of the page... will not output as it is in a different buffer
		} else{
			header('location:'.$location);
			exit(0);
		}
	}
	static function cookie_check(){
		if(!isset($_COOKIE[session_name()])){
			return false;
		}else{
			return true;
		}
	}
	//this function check if the script has been run from the web of from a script
	static function is_web(){
		return (!isset($_SERVER['argc']) || $_SERVER['argc'] == 0 || $_SERVER['argv'][0] != $_SERVER['PHP_SELF']);
	}
	//create a folder under HOMEPATH only
	static function mkdir($folder, $mode = 0777, $recursive = true){
		if(strpos($folder, HOMEPATH) !== 0){
			$folder = HOMEPATH.trim($folder);
		}
		if(!file_exists($folder)){
			mkdir($folder, $mode, $recursive);
		}
		if(!file_exists($folder)){
			$cmd = 'mkdir '.($recursive?'-p':'').' -m '.$mode.' "'.$folder.'"';
			$ret = `$cmd`;
		}
		if(!file_exists($folder)){
			return false;
		}
		return $folder;
	}
	/**
	 * to use to start a timer for the page
	 */
	public function start_timer(){
		if($this->timer_start == 0){
			$this->timer_start = microtime(true);
		}
	}
	/**
	 * to use to return the time since "time_start" was called
	 */
	public function stop_timer($round = 2){
		$return				 = round(microtime(true) - $this->timer_start, $round);
		$this->timer_start	 = 0;
		return $return;
	}
	static function UUID($lenght = 0){
		$uuid = sprintf('%04x%04x-%04x-%03x4-%04x-%04x%04x%04x', mt_rand(0, 65535), mt_rand(0, 65535), // 32 bits for "time_low"
																					  mt_rand(0, 65535), // 16 bits for "time_mid"
							   mt_rand(0, 4095), // 12 bits before the 0100 of (version) 4 for "time_hi_and_version"
				  bindec(substr_replace(sprintf('%016b', mt_rand(0, 65535)), '01', 6, 2)),
				// 8 bits, the last two of which (positions 6 and 7) are 01, for "clk_seq_hi_res"
				// (hence, the 2nd hex digit after the 3rd hyphen can only be 1, 5, 9 or d)
				// 8 bits for "clk_seq_low"
													 mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535) // 48 bits for "node"
		);
		if($lenght > 0 && $lenght < strlen($uuid)){
			$uuid = str_replace('-', '', $uuid);
			if($lenght < strlen($uuid)){
				$uuid = substr($uuid, 0, $lenght);
			}
		}
		return $uuid;
	}
	static function generate_uuid($lenght = 0){
		return fabric::UUID($lenght);
	}
	static function autoload(){
//		$config = lc('config')->get_and_unload_config('fabric');
		$config = lc('config')->get_config('fabric'); //let's keep it in memoty in case this class is initialized multiple times
		if(isset($config['use_autoload']) && $config['use_autoload']){
			$vars = lc('config')->get_and_unload_config('autoload');
			if(isset($vars['class']) && is_array($vars['class'])){
				foreach($vars['class'] as $key => $value){
					if($key != '' && $value['include']){
						lc($key, $value['load']);
					}
				}
			}
			if(isset($vars['lib']) && is_array($vars['lib'])){
				foreach($vars['lib'] as $key => $value){
					if($key != '' && $value['include']){
						ll($key, $value['load']);
					}
				}
			}
		}
	}
}

?>