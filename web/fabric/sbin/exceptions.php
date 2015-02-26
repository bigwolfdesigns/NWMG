<?php

class exceptions{
	protected static $instance;
	protected $_cfg			 = array();
	protected $_errors		 = array();
	protected $f_error_log	 = '';  //this is the function that is used to LOG the errors
	public function __construct(){
		self::$instance = &$this;
		$_cfg						 = lc('config')->get_and_unload_config('exceptions');
		if(!isset($_cfg['admin_email']) || $_cfg['admin_email'] == '') $_cfg['admin_email']		 = lc('config')->get('fabric_admin_email', '');
		if(!isset($_cfg['admin_name']) || $_cfg['admin_name'] == '') $_cfg['admin_name']			 = lc('config')->get('fabric_admin_name', '');
		if(!isset($_cfg['email_from_email']) || $_cfg['email_from_email'] == '') $_cfg['email_from_email']	 = $_cfg['admin_email'];
		if(!isset($_cfg['email_from_name']) || $_cfg['email_from_name'] == '') $_cfg['email_from_name']	 = $_cfg['admin_name'];
		ini_set('display_errors', 1);  //do not need, I use my personalize error report
		ini_set('html_errors', 1);   //do not need, I use my personalize error report
		$this->_cfg					 = array();
		$this->_errors				 = array();

		if($_cfg['digest_errors'] == true && $_cfg['email_errors'] == true){
			register_shutdown_function(array(&self::$instance, '_send_all_errors'));
		}
		$old_error_handler = set_error_handler(array(&self::$instance, 'error_handler'));

		ini_set('log_errors', $_cfg['log_errors']);
		@ini_set('error_log', $_cfg['error_log']);

		$this->_cfg['admin_name']		 = $_cfg['admin_name'];
		$this->_cfg['admin_email']		 = $_cfg['admin_email'];
		$this->_cfg['email_from_name']	 = $_cfg['email_from_name'];
		$this->_cfg['email_from_email']	 = $_cfg['email_from_email'];
		$this->_cfg['code']				 = $_cfg['code'];

		$this->_cfg['show_errors']	 = $_cfg['show_errors']; //if show the errors on the page or not
		$this->_cfg['log_errors']	 = $_cfg['log_errors']; //if log the errors in a file or not
		$this->_cfg['error_log']	 = $_cfg['error_log']; //file log
		$this->_cfg['email_errors']	 = $_cfg['email_errors'];
		$this->_cfg['digest_errors'] = $_cfg['digest_errors']; //send only one email with all the errors
		$this->_cfg['var_no_log']	 = $_cfg['var_no_log']; //this is an array. Those viarbles will not be logged
		$this->f_error_log			 = $_cfg['f_error_log'];
		$this->_parse_error_log();
	}
	private function _parse_error_log(){
		if($this->_cfg['error_log'] != ''){
			if(@file_exists($this->_cfg['error_log']) && @filesize($this->_cfg['error_log']) > 1){
				$errors = file($this->_cfg['error_log']);
				//in the meantime the file could have been deleted by another process
				if(!file_exists($this->_cfg['error_log'])){
					//if has been deleted by another process I do not have to send the report,
					//cause the other process probably already did
				}else{
					unlink($this->_cfg['error_log']);
					foreach($errors as $key=> $error){
						if($error != ''){
							$tmp	 = substr($error, 27);
							$level	 = substr($tmp, 0, strpos($tmp, ':'));
							$message = $error;
							$line	 = trim(strrchr($error, ' '));
							$tmp	 = substr($error, 0, -1 * (strlen(' on line '.$line) + 1));
							$file	 = strrchr($tmp, ' in ');
							$context = array();

							$date = substr($error, 1, 20);
							$this->error_handler($level, $message, $file, $line, $context, true);
						}
					}
				}
			}
		}
	}
	public function error_handler($level, $message, $file, $line, $context, $from_file = false){
		if((error_reporting() & $level) == 0) return;  //the error should not be reported






//error_level is set to don't return this kind of errors
		if($this->_cfg['show_errors'] == true || $this->_cfg['log_errors'] == true || $this->_cfg['email_errors'] == true){
			$type	 = '';
			$ertype	 = 'warning';
			switch($level){
				case 1:
					$type	 = 'E_ERROR';
					$ertype	 = 'error';
					break;
				case 2:
					$type	 = 'E_WARNING';
					$ertype	 = 'warning';
					break;
				case 4:
					$type	 = 'E_PARSE';
					$ertype	 = 'error';
					break;
				case 8:
					$type	 = 'E_NOTICE';
					$ertype	 = 'warning';
					break;
				case 16:
					$type	 = 'E_CORE_ERROR';
					$ertype	 = 'error';
					break;
				case 32:
					$type	 = 'E_CORE_WARNING';
					$ertype	 = 'error';
					break;
				case 64:
					$type	 = 'E_COMPILE_ERROR';
					$ertype	 = 'error';
					break;
				case 128:
					$type	 = 'E_COMPILE_WARNING';
					$ertype	 = 'error';
					break;
				case 256:
					$type	 = 'E_USER_ERROR';
					$ertype	 = 'error';
					break;
				case 512:
					$type	 = 'E_USER_WARNING';
					$ertype	 = 'warning';
					break;
				case 1024:
					$type	 = 'E_USER_NOTICE';
					$ertype	 = 'notice';
					break;
				case 2047:
					$type	 = 'E_ALL';
					$ertype	 = 'error';
					break;
				case 2048:
					$type	 = 'E_STRICT';
					$ertype	 = 'warning';
					break;
				case 4096:
					$type	 = 'E_RECOVERABLE_ERROR';
					$ertype	 = 'recoverable_error';
					break;
				case 8192:
					$type	 = 'E_DEPRECATED';
					$ertype	 = 'deprecated';
					break;
				case 16384:
					$type	 = 'E_USER_DEPRECATED';
					$ertype	 = 'deprecated';
					break;
				default:
					$type	 = $level;
					$ertype	 = 'error';
					break;
			}
			$msg = '';
			$msg .= 'An error of level '.$level.'('.$type.') was generated in file '.$file.' on line '.$line.".\n";
			$msg .= 'The error message was: '.$message."\n\n";
			$msg .= 'Date                : '.date("d.m.Y @ H:i", time())."\n";
			if($this->_cfg['show_errors'] == true && !$from_file){
				echo "\n".'<br />'.$msg.'<br />'."\n";
			}
			$msg .= 'Page                : http'.(isset($_SERVER['HTTPS'])?'s':'').'://'.(isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:'').(isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:'')."\n";
			$msg .= 'Script Filename     : '.(isset($_SERVER['SCRIPT_FILENAME'])?$_SERVER['SCRIPT_FILENAME']:'')."\n";
			$msg .= 'Query String        : '.(isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:'')."\n";
			$msg .= 'Referrer            : '.(isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'')."\n";
			$msg .= 'User IP             : '.(isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'undefinied')."\n";
			$msg .= 'Server IP           : '.(isset($_SERVER['SERVER_ADDR'])?$_SERVER['SERVER_ADDR']:getHostByName(getHostName()))."\n";
			$msg .= 'Server Name         : '.(isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:getHostName())."\n";

			if($this->_cfg['digest_errors'] == false && $this->_cfg['email_errors'] == true){
				$msg .= 'email sent to: '.$this->_cfg['admin_name'].'<'.$this->_cfg['admin_email'].'>';
				mail($this->_cfg['admin_name'].'<'.$this->_cfg['admin_email'].'>', '['.$this->_cfg['code'].']->site error', $msg, 'FROM: '.$this->_cfg['email_from_name'].'<'.$this->_cfg['email_from_email'].'>');
				$msg .= "\n\n";
			}elseif($this->_cfg['email_errors'] == true){
				$this->_errors[] = $msg;
			}
			if($this->_cfg['log_errors'] == true && $this->f_error_log != NULL && is_callable($this->f_error_log)){
				//now I log the error
				if(!$from_file){
					$msg .= 'The following variables were set in the scope that the error occurred in:'."\n\n";
					//cleaning some variables to reduce the LOG file
					$clean	 = $this->_cfg['var_no_log'];
					if(!is_array($clean)) $clean	 = array();
					$clean[] = 'HTTP_SERVER_VARS';
					$clean[] = 'HTTP_COOKIE_VARS';
					$clean[] = 'HTTP_GET_VARS';
					$clean[] = 'HTTP_POST_VARS';
					$clean[] = 'HTTP_POST_FILES';
					$clean[] = 'HTTP_ENV_VARS';
					$clean[] = 'HTTP_SESSION_VARS';
					$clean[] = '_SERVER';
					$clean[] = '_COOKIE';
					$clean[] = '_GET';
					$clean[] = '_POST';
					$clean[] = '_FILES';
					$clean[] = '_SESSION';
					$clean[] = 'GLOBALS';
					$clean[] = '_ENV';

					$msg_cont = array();
					foreach($context as $key=> $value){
						if(!in_array($key, $clean)){
							$msg_cont[$key] = $value;
						}
					}
					$content = var_export($msg_cont, true);
					$msg .= $content;
				}
				call_user_func($this->f_error_log, $msg, $ertype);
			}
			if($ertype == 'error' && !$from_file){
				//I kill the script and show a message
				echo "<center><h1>ERROR !!</h1><br />an e-mail has been sent to the staff<br />we are sorry for this problem</center>";
				//echo "\n<!-- ".str_replace(array('<','>'),array('&lt;','&gt;'),$msg)." -->\n";	//bad security problem here !!! do not show errors unless wanting to
				exit(1);
			}
		}
	}
	public function _send_all_errors(){
		if($this->_cfg['digest_errors'] == true && $this->_cfg['email_errors'] == true && !empty($this->_errors) && is_array($this->_errors)){
			$msg = 'email sent to: '.$this->_cfg['admin_email']."\n";
			$msg .= 'errors in this email: '.count($this->_errors)."\n\n";
			foreach($this->_errors as $msgs){
				$msg .= '------------------------------------'."\n\n".$msgs."\n\n";
			}
			mail(
					$this->_cfg['admin_name'].'<'.$this->_cfg['admin_email'].'>', '['.$this->_cfg['code'].']->site error', $msg, 'FROM: '.$this->_cfg['email_from_name'].'<'.$this->_cfg['email_from_email'].'>'
			);
		}
	}
}

?>