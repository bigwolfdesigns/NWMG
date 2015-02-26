<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| CORE
| -------------------------------------------------------------------------
*/

/*
default server timezone.
This is used to avoid any strict errors that PHP would cause.
Use a valid timezone
*/
$fabric['TZ']			= 'America/New_York';
//$fabric['TZ']			= 'America/Los_Angeles';

/*
leave it blank to have the default php upload_tmp_folder selected
The tml_folder parameter is used ONLY if the main TMPPATH is set to empty.
*/
$fabric['tmp_folder']	= 'tmp';

/*
use_autoload is a boolean. default = true
if true, will load the autoload config and will try to load
each single class that is specified in the config file.
*/
$fabric['use_autoload']	= true;

/*
This is the contact information that will
receive important messages from the system
*/
$fabric['admin_name']	= 'Billy Stalnaker';
$fabric['admin_email']	= 'billy@bigwolfdesigns.com';

/*
This is the character that will separate the
parameters in the query string
*/
$fabric['arg_separator']	= '&amp;';

?>