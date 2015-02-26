<?php

class lang{
	protected static $instance = NULL;
	public static function &get_instance(){
		if(is_null(self::$instance)){
			self::$instance = new db();
		}
		return self::$instance;
	}
	public function __construct(){
		self::$instance = $this;
		$driver	 = lc('config')->load('lang')->get('lang_driver', 'file');
		lc('config')->unload('lang');
		$tmp	 = & ll('lang'.DIRECTORY_SEPARATOR.$driver);
		return $tmp;
	}
}
?>