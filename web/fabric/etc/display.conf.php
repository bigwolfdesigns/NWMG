<?php

if(!defined('BASEPATH')) exit('No direct script access allowed');
/*
  | -------------------------------------------------------------------------
  | Display output configuration parameters
  | -------------------------------------------------------------------------
 */
$uri						 = lc('uri');
$display['driver']			 = 'template';
$display['cache_timeout']	 = 0;  //in seconds.  0 = DISABLE
//GROUPPING should not be a DISPLAY functionality
//$display['group']['css']							 = false;
//$display['group']['js']								 = false;
$display['gzip']			 = false; //let apache do the dirty work

$display['headers']['Date']							 = gmdate('D, d M Y H:i:s').' GMT';
$display['headers']['Expires']						 = 'Mon, 13 Jan 1977 21:33:00 GMT';
$display['headers']['Last-Modified']				 = gmdate('D, d M Y H:i:s').' GMT';
$display['headers']['Cache-Control']				 = 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0';
$display['headers']['Access-Control-Allow-Origin']	 = '*';
$display['headers']['Connection']					 = 'keep-alive';
$display['headers']['Content-Type']					 = 'text/html; charset=utf-8';   //text/plain = only text file
$display['headers']['P3P']							 = 'CP="Not Provided"';
$display['headers']['X-Content-Type-Options']		 = 'nosniff';
//$display['headers']['X-Frame-Options']				 = 'SAMEORIGIN';
$display['headers']['X-XSS-Protection']				 = '1; mode=block';
//2012-12-11
$display['headers']['Pragma']						 = 'public';
$display['headers']['Proxy-Connection']				 = 'keep-alive';
$display['headers']['Vary']							 = 'Accept-Encoding,User-Agent';
//2014-08-05
$display['headers']['Access-Control-Allow-Origin']	 = '*';
$display['headers']['Access-Control-Allow-Method']	 = 'POST, GET, OPTIONS';
$display['headers']['Access-Control-Allow-Headers']	 = 'x-requested-with';

$i = 0;
$display['meta'][$i]['http-equiv']	 = 'Content-type';
//$display['meta'][$i]['content']		= 'text/html; charset=iso-8859-1';
$display['meta'][$i]['content']		 = 'application/xhtml+xml; charset=ISO-8859-1';
$i++;
//$display['meta'][$i]['name']		 = 'description';
//$display['meta'][$i]['content']		 = 'Kirkco Corporation is a distributor of adhesive, sealant and lubricant metering, mixing, and dispensing equipment. Kirkco offers the following product lines: single component systems, two component systems, spraying and coating, hot melt adhesive and cold glue adhesive systems.';
//$i++;
//$display['meta'][$i]['http-equiv']	 = 'keywords';
//$display['meta'][$i]['content']		 = 'adhesive applicator,adhesive applicators,adhesive dispensing equipment,adhesive epoxy,dispensing equipment,hot melt adhesive,hot melt adhesives,hot melt equipment,hot melt glue gun,industrial adhesives,industrial coating,industrial coatings';
//$i++;
$display['meta'][$i]['name']		 = 'Identifier-URL';
$display['meta'][$i]['content']		 = '//'.$uri->get_domain();
$i++;
$display['meta'][$i]['name']		 = 'Revisit-after';
$display['meta'][$i]['content']		 = '1 day';
$i++;
$display['meta'][$i]['name']		 = 'Robots';
$display['meta'][$i]['content']		 = 'index, follow';
$i++;
$display['meta'][$i]['http-equiv']	 = 'imagetoolbar';
$display['meta'][$i]['content']		 = 'no';
$i++;
$display['meta'][$i]['http-equiv']	 = 'Content-Style-Type';
$display['meta'][$i]['content']		 = 'text/css';
$i++;
$display['meta'][$i]['http-equiv']	 = 'Content-Script-Type';
$display['meta'][$i]['content']		 = 'text/javascript';
$i++;


$i							 = 0;
$display['link'][$i]['rel']	 = 'canonical';
$display['link'][$i]['href'] = $uri->get_canonical_uri();
$i++;
$display['link'][$i]['rel']	 = 'shortcut icon';
$display['link'][$i]['href'] = $uri->create_auto_uri(array(CLASS_KEY => 'images', TASK_KEY => 'favicon', '_EXT' => 'ico'));
$i++;
$display['link'][$i]['rel']	 = 'stylesheet';
$display['link'][$i]['href'] = '/css/jquery-ui/jquery-ui.min.css';
$i++;
$display['link'][$i]['rel']	 = 'stylesheet';
$display['link'][$i]['href'] = '/css/jquery-ui/jquery-ui.structure.min.css';
$i++;
$display['link'][$i]['rel']	 = 'stylesheet';
$display['link'][$i]['href'] = '/css/jquery-ui/jquery-ui.theme.min.css';
$i++;

$i									 = 0;
$display['script'][$i]['type']		 = 'text/javascript';
$display['script'][$i]['language']	 = 'javascript';
$display['script'][$i]['src']		 = '/js/functions.js';
$i++;
$display['script'][$i]['type']		 = 'text/javascript';
$display['script'][$i]['language']	 = 'javascript';
$display['script'][$i]['src']		 = '/js/jquery/jquery-1.11.2.min.js';
$i++;
$display['script'][$i]['type']		 = 'text/javascript';
$display['script'][$i]['language']	 = 'javascript';
$display['script'][$i]['src']		 = '/js/jquery-ui/jquery-ui.min.js';
$i++;
