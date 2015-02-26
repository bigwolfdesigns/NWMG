<?php
if (!defined('BASEPATH'))
	exit ('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| AUTOLOAD SECTION
| -------------------------------------------------------------------------
|
| Autoload section, if the variables are set to TRUE, it will load those libraries (from lib folder)
| And they will be useable via $this->[class name]  where class name is the word following "use_"
|
| please remember, the classes fabric and config are the only ones always loaded by default
|
| when it is true, and it will load the corresponding class
| 	include	: load the class, if set to false the class won't be loaded nor the file included
| 	load	: if set to true, the file will be loaded and the class initialized, else it won't be loaded but the file included
*/
$autoload['class']['exceptions']		= array('include'=>true, 'load'=>true);
$autoload['lib']['table_prototype']		= array('include'=>true, 'load'=>true);
$autoload['lib']['client']		= array('include'=>true, 'load'=>true);