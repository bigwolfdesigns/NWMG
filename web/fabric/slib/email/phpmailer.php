<?php
class email_phpmailer extends email {
	public function __construct(){
		include_once dirname(__FILE__).'/phpmailer/class.phpmailer.php';
		$mail = new PHPMailer();
		$mail->PluginDir = dirname(__FILE__).'/phpmailer/';
		self::$instance = $mail;
	}
}