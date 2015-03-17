<?php

//TO-DO:
//change the lc and ll system
// - instead than lc and ll that load system and use classes
// - move all the sbin and slib files into a folder called sys
// - create a ls function (might change the name .. ls stands for load system, but also list i unix.. blah)
// - create a new function to load the files from "lib"
// - create a new function to load the files from "bin"
// - BC:
//   - `ll` and `lc` will still search for the files, but once found the file in `sys` it will call the ls function
//     else the function other functions for lib and bin folders
//

/*
 * Class registry
 *
 * This function acts as a singleton.  If the requested class does not
 * exist it is instantiated and set to a static variable.  If it has
 * previously been instantiated the variable is returned.
 *
 * @access	public
 * @param	string	the class name being requested
 * @param	bool	optional flag that lets classes get loaded but not instantiated
 * @return	object	THis can be false if the class doesn't exists
 */
function &lc($class, $initialize = true){

	static $objects = array();

	// Does the class already exists?  If so, we're done...
	if(isset($objects[$class]) && (($initialize && is_object($objects[$class])) || !$initialize)){
		return $objects[$class];
	}
	if($class == ''){
		$return = false;
	}elseif(file_exists(BINPATH.$class.EXT)){
		require_once(BINPATH.$class.EXT);
		$return = true;
	}elseif(strpos($class, '_') !== false && file_exists(BINPATH.str_replace('_', DIRECTORY_SEPARATOR, $class).EXT)){
		require_once(BINPATH.str_replace('_', DIRECTORY_SEPARATOR, $class).EXT);
		$return = true;
	}elseif(file_exists(SBINPATH.$class.EXT)){
		require_once(SBINPATH.$class.EXT);
		$return = true;
	}elseif(strpos($class, '_') !== false && file_exists(SBINPATH.str_replace('_', DIRECTORY_SEPARATOR, $class).EXT)){
		require_once(SBINPATH.str_replace('_', DIRECTORY_SEPARATOR, $class).EXT);
		$return = true;
	}else{
		/*
		  $ret	= debug_backtrace();
		  $ret	= (isset($ret[1]))?$ret[1]:$ret[0];
		  $file   = $ret['file'];
		  $line   = $ret['line'];
		  $object = isset($ret['object'])?$ret['object']:NULL;
		  if (is_object($object)) { $object = ' by '. get_class($object); }
		  trigger_error('Class "'.$class.EXT.'" is not in the bin folders.  called'.$object.' in '.$file.' on line '.$line.' || ', E_USER_WARNING);
		 */
		$return = false;
	}
	if($initialize && $return){
		$name = str_replace(DIRECTORY_SEPARATOR, '_', $class);
		if(class_exists($name)){
			if(func_num_args() > 2){
				//there are extra parameters to pass to the class
				$args	 = func_get_args();
				array_shift($args); //$class name
				array_shift($args); //$initialize name
				$obj	 = new $name($args);
			}else{
				$obj = new $name();
			}
			if(method_exists($obj, 'get_instance') && is_callable(array($obj, 'get_instance'))){
				$objects[$class] = & $obj->get_instance();
			}else{
				$objects[$class] = & $obj;
			}
			if(method_exists($objects[$class], 'init') && is_callable(array($objects[$class], 'init'))){
				$objects[$class]->init();
			}
			$return = $objects[$class];
		}else{
			$ret	 = debug_backtrace();
			$ret	 = (isset($ret[1]))?$ret[1]:$ret[0];
			$file	 = $ret['file'];
			$line	 = $ret['line'];
			$object	 = isset($ret['object'])?$ret['object']:'';
			if(is_object($object)){
				$object = get_class($object);
			}
			trigger_error('ERROR!!!! Class "'.$name.'" doesn\'t exists into class '.$class.EXT."\n".'called: line '.$line.' of '.$object.' \n(in '.$file.')', E_USER_ERROR);
			debug_print_backtrace();
			exit(1);
		}
	}
	return $return;
}
function &ll($class, $initialize = true){
	static $objects = array();

	// Does the class already exists?  If so, we're done...
	if(isset($objects[$class]) && (($initialize && is_object($objects[$class])) || !$initialize)){
		return $objects[$class];
	}
	if($class == ''){
		$return = false;
	}elseif(file_exists(LIBPATH.$class.EXT)){
		require_once(LIBPATH.$class.EXT);
		$return = true;
	}elseif(strpos($class, '_') !== false && file_exists(LIBPATH.str_replace('_', DIRECTORY_SEPARATOR, $class).EXT)){
		require_once(LIBPATH.str_replace('_', DIRECTORY_SEPARATOR, $class).EXT);
		$return = true;
	}elseif(file_exists(SLIBPATH.$class.EXT)){
		require_once(SLIBPATH.$class.EXT);
		$return = true;
	}elseif(strpos($class, '_') !== false && file_exists(SLIBPATH.str_replace('_', DIRECTORY_SEPARATOR, $class).EXT)){
		require_once(SLIBPATH.str_replace('_', DIRECTORY_SEPARATOR, $class).EXT);
		$return = true;
	}else{
		/*
		  $ret	= debug_backtrace();
		  $ret	= (isset($ret[1]))?$ret[1]:$ret[0];
		  $file   = $ret['file'];
		  $line   = $ret['line'];
		  $object = isset($ret['object'])?$ret['object']:NULL;
		  if (is_object($object)) { $object = ' by '. get_class($object); }
		  trigger_error('Class "'.$class.EXT.'" is not in the lib folders.  called'.$object.' in '.$file.' on line '.$line.' || ', E_USER_WARNING);
		 */
		$return = false;
	}
	if($initialize && $return){
		$name = str_replace(DIRECTORY_SEPARATOR, '_', $class);
		if(class_exists($name)){
			if(func_num_args() > 2){
				//there are extra parameters to pass to the class
				$args	 = func_get_args();
				array_shift($args); //$class name
				array_shift($args); //$initialize name
				$obj	 = new $name($args);
			}else{
				$obj = new $name();
			}
			if(method_exists($obj, 'get_instance') && is_callable(array($obj, 'get_instance'))){
				$objects[$class] = & $obj->get_instance();
			}else{
				$objects[$class] = & $obj;
			}
			if(method_exists($objects[$class], 'init') && is_callable(array($objects[$class], 'init'))){
				$objects[$class]->init();
			}
			$return = $objects[$class];
		}else{
			$ret	 = debug_backtrace();
			$ret	 = (isset($ret[1]))?$ret[1]:$ret[0];
			$file	 = $ret['file'];
			$line	 = $ret['line'];
			$object	 = isset($ret['object'])?$ret['object']:'';
			if(is_object($object)){
				$object = get_class($object);
			}
			trigger_error('ERROR!!!! Class "'.$name.'" doesn\'t exists into library '.$class.EXT."\n".'called: line '.$line.' of '.$object.' \n(in '.$file.')', E_USER_ERROR);
			debug_print_backtrace();
			exit(1);
		}
	}
	return $return;
}
//human readable aliases
function &load_library($class, $initialize = true){
	$ret	 = debug_backtrace();
	$ret	 = (isset($ret[1]))?$ret[1]:$ret[0];
	$file	 = $ret['file'];
	$line	 = $ret['line'];
	$object	 = $ret['object'];
	if(is_object($object)){
		$object = get_class($object);
	}
	trigger_error('load_library() DEPRECATED.  Use ll()'."\n".'called: line '.$line.' of '.$object.' (in '.$file.')', E_USER_DEPRECATED);
	return ll($class, $initialize);
}
function &load_class($class, $initialize = true){
	$ret	 = debug_backtrace();
	$ret	 = (isset($ret[1]))?$ret[1]:$ret[0];
	$file	 = $ret['file'];
	$line	 = $ret['line'];
	$object	 = $ret['object'];
	if(is_object($object)){
		$object = get_class($object);
	}
	trigger_error('load_class() DEPRECATED.  Use lc()'."\n".'called: line '.$line.' of '.$object.' (in '.$file.')', E_USER_DEPRECATED);
	return lc($class, $initialize);
}
//I suggest not to use this to have a better control on the code
//and instead use spl autoload
/**
  function __autoload($class_name) {
  if(!lc($class_name,false)){
  ll($class_name,false);
  }
  }
 */
?>
