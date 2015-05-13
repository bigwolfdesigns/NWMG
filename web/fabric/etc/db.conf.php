<?php

if(!defined('BASEPATH')) exit('No direct script access allowed');
/*
  | -------------------------------------------------------------------------
  | database access
  | -------------------------------------------------------------------------
 */
$db['appname']	 = 'Networks Marketing Group';
$db['driver']	 = 'mysql';
$db['read']		 = array();
//$i							 = 0;
//$db['read'][$i]['name']		 = 'localhost';
//$db['read'][$i]['server']	 = 'localhost'; //127.0.0.1
//$db['read'][$i]['database']	 = 'networks_kirkcocorp';
//$db['read'][$i]['user']		 = 'networks_website';
//$db['read'][$i]['password']	 = '';

$db['persistent']	 = false;
$db['server']		 = 'localhost';
switch(strtolower(isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:'')){
	case'www.maxsondoors.com':
	case'maxsondoors.com':
	case'maxsondoors.networksmarketinggroup.com':
		$db['database']	 = 'networks_maxsondoors';
		break;
	case 'aviation':
	case 'aviation.networksmarketinggroup.com':
		$db['database']	 = 'networks_aviation';
		break;
	case'www.wlkco.com':
	case'wlkco.com':
	case'wlkco.networksmarketinggroup.com':
		$db['database']	 = 'networks_wlkco';
		break;
	case '': //scripts
	case 'kirkcocorp':
	case 'kirkcocorp.networksmarketinggroup.com':
	default:
		$db['database']	 = 'networks_kirkcocorp';
		break;
}
$db['user']		 = 'networks_website';
$db['password']	 = 'Q6Vh08E7buwd';

$db['show_error']			 = 0; //0 = show an error message, 1 = show the error in the page, 2 don't show anything in the page
$db['trigger_error']		 = 1;
$db['mail_error']			 = 'billy@bigwolfdesigns.com';   //set an email address if you want to receive an email with the error
$db['connect_first_use']	 = true; //connect only the first time that a database function is called
$db['default_result_type']	 = MYSQL_ASSOC;  //MySQL way to fetch rows

$db['SQL_CACHE'] = 0; //0 - do not touch selects
//1 - always use caching (add it to selects)
//2 - never use caching (remove it from selects)
$db['timezone']	 = ''; //if empty will not set the timezone