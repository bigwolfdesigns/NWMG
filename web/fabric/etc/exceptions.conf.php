<?php

if(!defined('BASEPATH')) exit('No direct script access allowed');
/*
  | -------------------------------------------------------------------------
  | ERRORS SECTION
  | -------------------------------------------------------------------------
  |
  | Here you can setup how you want the errors to display
  |
 */
$exceptions['driver']		 = 'default';	 //in case you want to change this class, for now it hadles errors thanks to bib_errors by Fabrizio Parrella
$exceptions['code']			 = isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:'';
$exceptions['show_errors']	 = ini_get('display_errors'); //if show the errors on the page or not
$exceptions['log_errors']	 = ini_get('log_errors');  //if log the errors or not (log calling $f_error_log, see below)
/* * ******************************************************************* */
/* * ****** THE NEXT VALUE IS BETTER IF IT IS MANUALLY DEFINED ********* */
/* * ***** THIS WILL AVOID TO INTERCEPT ERRORS FROM OTHER SITES ******** */
/* * ******************************************************************* */
$exceptions['error_log']	 = ini_get('error_log');   //file log
$exceptions['parse_log']	 = false;	  //wheter or not to parse the log file - useful when the log is read only from the server
//if(!isset($_cfg['error_log']))		$_cfg['error_log']		= $_SERVER['DOCUMENT_ROOT'].'/error.log';		//file log
/* * ******************************************************************* */
$exceptions['email_errors']	 = false;	//send an email when an error accour
$exceptions['digest_errors'] = false;	//send a digest email with all the errors of the page
$exceptions['debug_errors']	 = false;	//When displaying the error on the page, it will show as many debug information, turn this off when releasing the App

$exceptions['var_no_log']	 = array();   //list of variables that will not show in the log.
$exceptions['f_error_log']	 = NULL;	//here you can add the function that wil handle the log for the errors
$exceptions['admin_name']	 = 'PHPErrors';	//Who will receive the error via email - name
$exceptions['admin_email']	 = '';	//Who will receive the error via email - email

$exceptions['email_from_name']	 = 'PHPErrors';   //Who will send the error via email - name
$exceptions['email_from_email']	 = '';   //Who will send the error via email - email
?>