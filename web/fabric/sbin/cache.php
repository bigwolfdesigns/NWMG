<?php
class cache{
	protected static $instance;
	public static function &get_instance(){
		return self::$instance;
	}

	public function __construct(){
		self::$instance =&$this;
		$config		= lc('config')->load('cache');
		$driver		= $config->get('cache_driver','');
		$tmp		=& lc('cache'.DIRECTORY_SEPARATOR.$driver);
		if($driver!='' && $tmp!==false){
			$tmp->set_site_id($config->get('cache_site_id',''));
			$tmp->default_compress($config->get('cache_compress',''));
			$tmp->default_expire($config->get('cache_expire',''));
			switch($driver){
				case 'memcached':
					$tmp->connect($config->get('cache_memcached_server', 'localhost'),
								  $config->get('cache_memcached_port','11211'),
								  $config->get('cache_memcached_timeout','1')
						);
					break;
			}
		}
		lc('config')->unload('cache');
		self::$instance = $tmp;
	}
}
?>