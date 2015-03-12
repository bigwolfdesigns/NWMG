<?php

/*
  fabric SYSTEM
  Based on various other systems
  picking and choosing what Fabrizio Parrella, the creator, likes
 */
if(!defined('DIRECTORY_SEPARATOR')) define('DIRECTORY_SEPARATOR', strtoupper(substr(PHP_OS, 0, 3) == 'WIN')?'\\':'/');
define('APPNAME', 'Networks Marketing Group');
define('APPVERSION', '0.0.1');
define('IS_AJAX_REQUEST', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

/*
  |---------------------------------------------------------------
  | PHP ERROR REPORTING LEVEL
  |---------------------------------------------------------------
  |
  | By default BBV runs with error reporting set to ALL.  For security
  | reasons you are encouraged to change this when your site goes live.
  | For more info visit:  http://www.php.net/error_reporting
  |
 */
error_reporting(E_STRICT + E_ALL);

/*
  |---------------------------------------------------------------
  | SYSTEM FOLDER NAME
  |---------------------------------------------------------------
  |
  | This variable must contain the name of your "fabric" folder.
  | This will be the path where the whole system will be located
  | Include the path if the folder is not in the same  directory
  | as this file.
  |
  | NO TRAILING SLASH!
  |
 */
$fabric_main_folder = 'fabric';		//you can change this as you want

/*
  |===============================================================
  | END OF USER CONFIGURABLE SETTINGS
  |===============================================================
 */


/*
  |---------------------------------------------------------------
  | SET THE SERVER PATH
  |---------------------------------------------------------------
  |
  | Let's attempt to determine the full-server path to the "system"
  | folder in order to reduce the possibility of path problems.
  | Note: We only attempt this if the user hasn't specified a
  | full server path.
  |
 */
if(strpos($fabric_main_folder, DIRECTORY_SEPARATOR) === false){
	if(function_exists('realpath') && @realpath(dirname(__FILE__)) !== false){
		$fabric_folder = realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR.$fabric_main_folder;
	}
}else{
	// Swap directory separators to "DIRECTORY_SEPARATOR" style for consistency
	$fabric_folder = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $fabric_main_folder);
}

/*
  |---------------------------------------------------------------
  | DEFINE APPLICATION CONSTANTS
  |---------------------------------------------------------------
  |
  | EXT		- The file extension.  Typically ".php"
  | SELF		- The name of THIS file (typically "index.php")
  | FULLPATH	- The full server path to THIS file
  | WEBPATH	- The relative path that will be used for direct access to internal weblinks (index.php level)
  | BASEPATH	- The full server path to the "system" folder
  | ETCPATH	- The full server path to the "configuration and more" folder
  | SBINPATH	- The full server path to the "system binary" folder
  | BINPATH	- The full server path to the "binary" folder - your programs
  | SLIBPATH	- The full server path to the "system library" folder
  | LIBPATH	- The full server path to the "library" folder
  | TPLPATH	- The full server path to the "templates" folder
  | TPLWEBPATH	- The full server path to the "templates" folder accessible from the WEB
  |
  |
  |   the following needs to be writable by the webserver user
  |
  | TMPPATH	- The full server path to the "temporary" folder	- set this as temporary folder
  | USRPATH	- The full server path to the "user" folder
  | HOMEPATH	- The full server path to the "home" folder			- this will contain the site folders
  |
 */
define('EXT', '.'.pathinfo(__FILE__, PATHINFO_EXTENSION));
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
define('FULLPATH', pathinfo(__FILE__, PATHINFO_DIRNAME).DIRECTORY_SEPARATOR);
define('BASEPATH', $fabric_folder.DIRECTORY_SEPARATOR);
define('ETCPATH', BASEPATH.'etc'.DIRECTORY_SEPARATOR);
define('BINPATH', BASEPATH.'bin'.DIRECTORY_SEPARATOR);
define('SBINPATH', BASEPATH.'sbin'.DIRECTORY_SEPARATOR);
define('SLIBPATH', BASEPATH.'slib'.DIRECTORY_SEPARATOR);
define('LIBPATH', BASEPATH.'lib'.DIRECTORY_SEPARATOR);
define('LOGPATH', BASEPATH.'log'.DIRECTORY_SEPARATOR);
define('TPLPATH', BASEPATH.'tpl'.DIRECTORY_SEPARATOR);
//define('TMPPATH',	ini_get('upload_tmp_dir'));		//this is the default temporary folder for php
define('TMPPATH', BASEPATH.'tmp'.DIRECTORY_SEPARATOR); //or you can leave it empty to use a site_driven temporary folder
define('USRPATH', BASEPATH.'usr'.DIRECTORY_SEPARATOR);
define('HOMEPATH', USRPATH.'home'.DIRECTORY_SEPARATOR);
//web paths
define('WEBPATH', str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME).DIRECTORY_SEPARATOR));
define('TPLWEBPATH', WEBPATH.$fabric_main_folder.DIRECTORY_SEPARATOR.'tpl'.DIRECTORY_SEPARATOR);
define('USRWEBPATH', WEBPATH.$fabric_main_folder.DIRECTORY_SEPARATOR.'usr'.DIRECTORY_SEPARATOR);
define('HOMEWEBPATH', USRWEBPATH.'home'.DIRECTORY_SEPARATOR);

/*
  | This is the name that will be passed to the GET string to load the main class
  | if nothing is passed, a class called "default_fabric" will be loaded
 */
define('CLASS_KEY', '__m');

/*
  | This is the name that will be passed to the GET string to load the function in the main class
  | if nothing is passed, a class called DEFAULT will be loaded
 */
define('TASK_KEY', '__t');

/*
  | This is the name for the default class that will be loaded
 */
//define('STARTUP','default_fabric');
//define('STARTUP','pbi');
define('STARTUP', 'error');


/*
 * House Cleaning
 */
unset($fabric_folder);
unset($fabric_main_folder);
/*
  |---------------------------------------------------------------
  | AND HERE WE START....
  |---------------------------------------------------------------
 */
require_once BASEPATH.'include'.DIRECTORY_SEPARATOR.'_functions'.EXT;
require_once SBINPATH.'fabric'.EXT;
lc('fabric');
ll('sessions')->start();
fabric::autoload();
$class_key = str_replace('-','_',lc('uri')->get(CLASS_KEY, STARTUP));
if(!lc($class_key)){
	lc('uri')->set(CLASS_KEY, STARTUP);
	lc(STARTUP);
}
//TO-DO: unload everything but "display"
ll('display')->render();