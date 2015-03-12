<?php

if(!defined('BASEPATH')){
	exit('No direct script access allowed');
}

class components extends table_prototype {
	public function __construct(){
		parent::__construct();
		$this->set_table_name('component')->set_auto_lock_in_shared_mode(true);
	}
	public function get_image($comp_id){
		return ll('images')->get_image($comp_id, 'component');
	}
	public function get_parts($comp_id){
		//get all the parts for this component
		$filters	 = array();
		$filters[]	 = array('field' => 'part_component.component_id', 'operator' => '=', 'value' => $comp_id);
		$join		 = array();
		$join[]		 = array('table' => 'part', 'how' => 'part_component.part_id = part.id');
		$fields		 = array('part.*');
		$parts		 = $this->get_raw($filters, array(), array(), '', 'part_component', $join, $fields);
		$return		 = array();
		if(is_array($parts) && !empty($parts)){
			foreach($parts as $k => $part){
				$part_id			 = $part['id'];
				$part[$k]['image']	 = ll('parts')->get_image($part_id);
			}
			$return = $parts;
		}
		return $return;
	}
}
