<?php

class display_template extends display{
	private $template			 = 'default';
	private $fail_over_template	 = 'default';
	private $hide_show			 = array(
		'head'	=>true,
		'foot'	=>true,
	);
	public function __construct(){
		parent::$instance = &$this;
	}
	public function set_template($template){
		$this->template = $template;
		return $this;
	}
	public function get_template(){
		return $this->template;
	}
	public function set_fail_over_template($template){
		$this->fail_over_template = $template;
		return $this;
	}
	public function set_hide_show($what, $show = true){
		if($what != ''){
			$this->hide_show[$what] = $show;
		}
		return $this;
	}
	public function show($tplFile, $temp_var = array(), $folder = ''){
		static $firsttime = true;
		if($folder == ''){
			$folder = $this->template;
		}
		$this->start();
		if($firsttime && $this->hide_show['head']){
			$this->_show($this->_tplFile('header', $folder), $temp_var);
		}
		$this->_show($this->_tplFile($tplFile, $folder), $temp_var);
		if($firsttime && $this->hide_show['foot']){
			$this->_show($this->_tplFile('footer', $folder), $temp_var);
		}
		$this->end();
		$firsttime = false;
		return $this;
	}
	public function grab($tplFile, $temp_var = array(), $folder = ''){
		if($folder == ''){
			$folder = $this->template;
		}
		return $this->_grab($this->_tplFile($tplFile, $folder), $temp_var);
	}
	private function _tplFile($tplFile, $folder){
		if(!ll('files')->file_exists(TPLPATH.$folder.'/'.$tplFile.EXT) && ll('files')->file_exists(TPLPATH.$this->fail_over_template.'/'.$tplFile.EXT)){
			$return = $this->fail_over_template.'/'.$tplFile;
		}else{
			$return = $folder.'/'.$tplFile;
		}
		return $return;
	}
}

?>