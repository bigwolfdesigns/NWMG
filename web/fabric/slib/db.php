<?php

class db{
	protected static $instance = NULL;
	public static function &get_instance(){
		if(is_null(self::$instance)){
			self::$instance = new db();
		}
		return self::$instance;
	}
	public function __construct(){
		self::$instance = $this;
		$config	 = lc('config');
		$driver	 = $config->load('db')->get('db_driver', '');
		self::$instance = & ll('db'.DIRECTORY_SEPARATOR.$driver);
		unset($this);
		$read	 = $config->get('db_read');
		if(!is_array($read)){
			$read = array();
		}
		$t = count($read);
		if($t > 0){
			shuffle($read);
		}
		$read[$t]['server']		 = $config->get('db_server');
		$read[$t]['database']	 = $config->get('db_database');
		$read[$t]['user']		 = $config->get('db_user');
		$read[$t]['password']	 = $config->get('db_password');
		$tmp					 = current($read);
		while(!isset($tmp['server'], $tmp['database'], $tmp['user'], $tmp['password'])){
			$tmp = next($read);
		}
		$read_server	 = $tmp['server'];
		$read_database	 = $tmp['database'];
		$read_user		 = $tmp['user'];
		$read_password	 = $tmp['password'];

		self::$instance->set_variables(array(
			'appname'				=>$config->get('db_appname', 'fabric DataBase Access'),
			'server'				=>$config->get('db_server'),
			'database'				=>$config->get('db_database'),
			'user'					=>$config->get('db_user'),
			'password'				=>$config->get('db_password'),
			'read_server'			=>$read_server,
			'read_database'			=>$read_database,
			'read_user'				=>$read_user,
			'read_password'			=>$read_password,
			'admin_email'			=>$config->get('db_mail_error'),
			'show_error'			=>$config->get('db_show_error'),
			'trigger_error'			=>$config->get('db_trigger_error'),
			'log'					=>0,
			'die_error'				=>1, //0 = the page will not die when there is an error, 1 = the page will die
			'connect_first_use'		=>$config->get('db_connect_first_use'),
			'default_result_type'	=>$config->get('db_default_result_type'),
			'SQL_CACHE'				=>$config->get('db_SQL_CACHE'),
			'timezone'				=>$config->get('db_timezone'),
		));
		lc('config')->unload('db'); //cleaning up memory
		unset($config);
		self::$instance->connect();
		register_shutdown_function(array(&self::$instance, 'close'));
//		unset($this);
//		return $tmp;
	}
}

?>