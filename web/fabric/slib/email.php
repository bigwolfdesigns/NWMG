<?php

class email {
	protected static $instance = NULL;
	public static function &get_instance(){
		if(is_null(self::$instance)){
			self::$instance = new db();
		}
		return self::$instance;
	}
	public function __construct(){
		self::$instance	 = $this;
		$driver			 = lc('config')->load('email')->get('email_driver', '');
		lc('config')->unload('email');
		$tmp			 = & ll('email'.DIRECTORY_SEPARATOR.$driver);
		$this->_configure($tmp);
		return $tmp;
	}
	private function _configure(&$mail){
		$smtp_user		 = ll('client')->get('smtp_user', '');
		$smtp_password	 = ll('client')->get('smtp_password', '');
		$smtp_server	 = ll('client')->get('smtp_server', '');
		$smtp_port		 = ll('client')->get('smtp_port', 25);
		$site_name		 = ll('client')->get('name', 'Your Site');
		$mail->IsSMTP();
		$mail->SMTPAuth	 = true;
		$mail->Host		 = $smtp_server;
		$mail->Port		 = $smtp_port;
		$mail->Username	 = $smtp_user;
		$mail->Password	 = $smtp_password;
		$mail->SMTPDebug = 1;
		$mail->AddReplyTo($smtp_user, $site_name);
		$mail->SetFrom($smtp_user, $site_name);
//		$mail->AddAddress($address, "John Doe");
	}
}
