<?php

if(!defined('BASEPATH')) exit('No direct script access allowed');

class failover {
	protected $code		 = '';
	protected $class_key = '';
	protected $task_key	 = '';
	public function __construct($customer_id = 0){
		$tmps	 = @parse_url(lc('uri')->get_uri());
		$path	 = isset($tmps['path'])?$tmps['path']:'/';
		$tmps	 = explode('/', $path);
		$tmp	 = array_pop($tmps);
		if(trim($tmp) != ''){
			$tmp		 = substr($tmp, 0, strpos($tmp, "."));
			$this->code	 = $tmp;
		}
		return $this;
	}
	public function check(){
		$return = $this->is_page() ||
				$this->is_category() ||
				$this->is_product();
		return $return;
	}
	public function get($key, $default = NULL){
		if(isset($this->{$key})){
			$return = $this->{$key};
		}else{
			$return = $default;
		}
		return $return;
	}
	public function get_code(){
		return $this->code;
	}
	private function is_product(){
		return false;
	}
	private function is_category(){
		$return		 = false;
		$filters	 = array();
		$filters[]	 = array('name' => 'alias', 'operator' => 'LIKE', 'value' => $this->code);
		$filters[]	 = array('name' => 'active', 'operator' => '=', 'value' => 'y');
		$tmps		 = ll('categories')->get_info($filters);
		if(is_array($tmps) && isset($tmps['id'])){
			$return			 = true;
			$this->class_key = 'category';
			$this->task_key	 = 'view';
			lc('uri')->set('category', $tmps['alias']);
		}
		return $return;
	}
	private function is_page(){
		$return		 = false;
		$filters	 = array();
		$filters[]	 = array('name' => 'alias', 'operator' => 'LIKE', 'value' => $this->code);
		$filters[]	 = array('name' => 'active', 'operator' => '=', 'value' => 'y');
		$tmps		 = ll('pages')->get_info($filters);
		if(is_array($tmps) && isset($tmps['id'])){
			$return			 = true;
			$this->class_key = 'page';
			$this->task_key	 = 'view';
			lc('uri')->set('page', $tmps['alias']);
		}
		return $return;
	}
}
