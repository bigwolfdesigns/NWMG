<?php

if(!defined('BASEPATH')){
	exit('No direct script access allowed');
}

class searches extends table_prototype {
	public function __construct(){
		parent::__construct();
	}
	public function search($query){
		//search through the products,categories,and parts
		$results	 = array();
		$tables		 = array();
		$tables[]	 = array('table' => 'product', 'fields' => array('name'));
		$tables[]	 = array('table' => 'category', 'fields' => array('name'));
		$tables[]	 = array('table' => 'part', 'fields' => array('name'));
		if($query!=''){
			foreach($tables as $params){
				$table			 = $params['table'];
				$results[$table] = isset($results[$table])?$results[$table]:array();
				$filters		 = $this->prep_search_filters($query, $params);
				$results[$table] = array_merge($results[$table], $this->select()->from($table)->where($filters)->get_records());
			}
			$empty = array();
			foreach($results as $table_name => $the_results){
				foreach($the_results as $k => $result){
					$id = $result['id'];
					switch($table_name){
						case'category':
							$results[$table_name][$k]['url']	 = ll('categories')->get_url($id);
							$results[$table_name][$k]['image']	 = ll('categories')->get_image($id);
							break;
						case'product':
							$results[$table_name][$k]['url']	 = ll('products')->get_url($id);
							$results[$table_name][$k]['image']	 = ll('products')->get_image($id);
							break;
						case'part':
//							$results[$table_name][$k]['url'] = ll('parts')->get_url($id);
							$results[$table_name][$k]['image']	 = ll('parts')->get_image($id);
							break;
					}
				}
			}
		}else{
			$results['error'] = "You must input a search parameter.";
		}
		return $results;
	}
	public function prep_search_filters($query_in, $params){
		$table	 = $params['table'];
		$fields	 = $params['fields'];
		$queries = explode(' ', $query_in);
		$filters = array();
		$i		 = 0;
		foreach($queries as $query){
			foreach($fields as $field){
				$filters[$i][] = array('field' => "$table.$field", 'operator' => 'LIKE', 'value' => "%$query%");
			}
			$i++;
		}
		return $filters;
	}
}
