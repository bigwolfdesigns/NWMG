<?php

if(!defined('BASEPATH')) exit('No direct script access allowed');
/*
  | -------------------------------------------------------------------------
  | URI
  | -------------------------------------------------------------------------
 */

/*
  delimiter used to create URI
  default: /
 */
$uri['delimiter'] = '/';

/*
  this decice to show or not the $_SERVER['PHP_SELF'] variable in the URI
  it this is set to FALSE, remember to add something in that redirect
  the pages to the right URL
  example, if you use apache and index.php as main file, add this to the .htaccess file

  RewriteEngine On
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule ^(.*) index.php

  and add any other kind of extension to the RewriteCond part
  default: false
 */
$uri['hide_self'] = true;

/*
  the next parameters decide to show or hide the CLASS_KEY and/or the TASK_KEY.
  if they are hidde, the CLASS_KEY is always the first parameter, and the CLASS_KEY will always be the second parameter in the URL
  default: false
 */
$uri['hide_keys'] = true;

/*
  this decice to show or not the host server when referring to local pages.
  default: false

  remember, if you hide the host name you may lose some SEO optimization
  but if your site has a lot of internal links and your hostname is long and you pay for bandwith
  you may want to hide it.
 */
$uri['hide_host'] = false;

/*
  this is used in case you want to append an extension to the url created.
  This parameter will work only if "hide_self" is set to true
  for example:
  /bt/category
  will be:
  /bt/category.html

  default: .html

  if set to false it will not add an extension

 */
$uri['extension'] = '.html';


/*
  this is used to strip extensions from the URL

  for example:
  /bt/category.html
  will be:
  /bt/category

  default: array($uri['extension'])

  if set to false it will not add an extension

 */
$uri['accepted_extensions'] = array($uri['extension'], '.php', '.pdf', '.css', '.js', '.jpg', '.png', '.xml', '.gif', '.rss', '.svg');

/*
  the aliases allow to specify a different URL or to convert old URLs
  it works using the key and processing it as a regular expressions
  and only the found value will be substituted

  NOTE:
  - do not include the initial /
  - the php function used is preg_replace
  - the delimiter is *  (I know is not a common one, but I had to choose a char that is not commonly used in an URL)

  for example with the rule:
  ^lin	=> account/login
  if the customer hits:
  /lin?email=address@email.com
  it will be translated in
  /account/login?email=address@email.com
 */
$uri['alias']				 = array(
//	'^a/'							=>'account/',
//	'^c/'							=>'category/',
//	'^[oO][cC]/'					=>'offer/',
//	'^(A[0-9]{3}-[0-9]{3}-[0-9]{4})'=>'style//product/$1',
//	'^crossdomain'					=>'rss/crossdomain',
//	'^sitemap'						=>'rss/gglsmap',
//	'^lin/([A-Z]{6,12})/[LM]ID/(.+)'=>'account/login/KID/$1/NP/logo/$2.html',
//	'affiliates/images/aff_banner'	=>'images/aff_banner',
);
/*
 * time to set some default for when we create URI
 * if the values are set to NULL
 * the system will decide what to use
 * you can always set those parameters on the code at any time via
  set_default_uri_domain($value);
  set_default_uri_page($value);
  set_default_uri_secure_mode($value);
  set_default_uri_port($value);
 */
$uri['default_domain']		 = NULL;
$uri['default_page']		 = NULL;
$uri['default_secure_mode']	 = false;
$uri['default_port']		 = NULL;
