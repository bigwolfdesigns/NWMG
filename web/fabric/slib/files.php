<?php

//simple operation on files
//mostly here to speed up disk access when it should not be necessary to access the file system multiple times
//to see if a file already exists
//as disk access are very "expensive" in time

class files {
	protected $cache;
	protected $cache_timeout	 = 90;
	protected $file_info_array	 = array();
	protected $mm_cache			 = 'files::info::';
	protected $conf				 = array();
	public function __construct(){
		$this->cache = lc('cache');
		$this->conf	 = lc('config')->get_and_unload_config('files');
		if(!isset($this->conf['new_owner_name'])){
			$this->conf['new_owner_name'] = '';
		}
		if(!isset($this->conf['reset_umask'])){
			$this->conf['reset_umask'] = false;
		}
	}
	private function _set_file_info($file, $key, $value){
		$mm									 = $this->mm_cache.$file;
		$this->file_info_array[$file][$key]	 = $value;
		//$this->cache->set($mm, $this->file_info_array[$file], false, $this->cache_timeout);
	}
	private function _get_file_info($file, $key){
		if(!isset($this->file_info_array[$file])){
			$mm								 = $this->mm_cache.$file;
			$tmp							 = array(); //$this->cache->get($mm, array());
			$this->file_info_array[$file]	 = $tmp;
		}
		return (isset($this->file_info_array[$file][$key])?$this->file_info_array[$file][$key]:NULL);
	}
	public function file_exists($file, $use_cache = true){
		return $this->exists($file, $use_cache);
	}
	public function exists($file, $use_cache = true){
		if($use_cache == false || is_null($this->_get_file_info($file, 'exists'))){
			$this->_set_file_info($file, 'exists', file_exists($file));
		}
		return (bool)$this->_get_file_info($file, 'exists');
	}
	public function rename($oldname, $newname, $context = NULL){
		if($this->conf['reset_umask']){
			$oldumask = umask(0);
		}
		if(is_resource($context)){
			$ret = rename($oldname, $newname, $context);
		}else{
			$ret = rename($oldname, $newname);
		}
		if($ret){
			$this->chown($newname);
			$this->_set_file_info($oldname, 'exists', false);
			$this->_set_file_info($newname, 'exists', true);
		}
		if($this->conf['reset_umask']){
			umask($oldumask);
		}
		return $ret;
	}
	public function copy($oldname, $newname, $context = NULL){
		if($this->conf['reset_umask']){
			$oldumask = umask(0);
		}
		if(is_resource($context)){
			$ret = copy($oldname, $newname, $context);
		}else{
			$ret = copy($oldname, $newname);
		}
		if($ret){
			$this->chown($newname);
			$this->_set_file_info($oldname, 'exists', true);
			$this->_set_file_info($newname, 'exists', true);
		}
		if($this->conf['reset_umask']){
			umask($oldumask);
		}
		return $ret;
	}
	public function mkdir($pathname, $mode = 0777, $recursive = false, $context = NULL){
		if($this->conf['reset_umask']){
			$oldumask = umask(0);
		}
		if(is_resource($context)){
			$ret = mkdir($pathname, $mode, $recursive, $context);
		}else{
			$ret = mkdir($pathname, $mode, $recursive);
		}
		if($ret){
			$this->chown($pathname);
			$this->_set_file_info($pathname, 'exists', true);
		}
		if($this->conf['reset_umask']){
			umask($oldumask);
		}
		return $ret;
	}
	public function unlink($filename, $context = NULL){
		if(is_resource($context)){
			$ret = unlink($filename, $context);
		}else{
			$ret = unlink($filename);
		}
		if($ret){
			$this->_set_file_info($filename, 'exists', false);
		}
		return $ret;
	}
	public function chown($filename, $user = NULL){
		if(is_null($user)){
			$user = $this->conf['new_owner_name'];
		}
		if($user != '' && $this->exists($filename)){
			return chown($filename, $user);
		}
		return false;
	}
	public function stat($filename, $use_cache = true){
		if($use_cache == false || is_null($this->_get_file_info($filename, 'stat'))){
			$this->_set_file_info($filename, 'stat', stat($filename));
		}
		return $this->_get_file_info($filename, 'stat');
	}
	public function atime($filename, $use_cache = true){
		$filestat = $use_cache?$this->_get_file_info($filename, 'stat'):array();
		if(!isset($filestat['atime'])){
			$filestat = $this->stat($filename, $use_cache);
		}
		return isset($filestat['atime'])?$filestat['atime']:NULL;
	}
	public function ctime($filename, $use_cache = true){
		$filestat = $use_cache?$this->_get_file_info($filename, 'stat'):array();
		if(!isset($filestat['ctime'])){
			$filestat = $this->stat($filename, $use_cache);
		}
		return isset($filestat['ctime'])?$filestat['ctime']:NULL;
	}
	public function mtime($filename, $use_cache = true){
		$filestat = $use_cache?$this->_get_file_info($filename, 'stat'):array();
		if(!isset($filestat['mtime'])){
			$filestat = $this->stat($filename, $use_cache);
		}
		return isset($filestat['mtime'])?$filestat['mtime']:NULL;
	}
}
