<?php

if(!defined('BASEPATH')) exit('No direct script access allowed');
/*
  | -------------------------------------------------------------------------
  | sessions settings
  | -------------------------------------------------------------------------
 */
$sessions['driver']	 = 'db';
$sessions['name']	 = 'NWMGID';
$sessions['prefix']	 = '';

//any of the followind parameters, if set to false will not be set
$sessions['lifetime']	 = 45 * 60;  //session_timeout in seconds
$sessions['savepath']	 = false;  //where to save the session files, make sure that apache can write in that folder
$sessions['linksid']	 = 1;   //use_trans_sid :  if cookies are disable, use the links directly on web pages (may be a security issue)