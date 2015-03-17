<?php

if(!defined('BASEPATH')){
	exit('No direct script access allowed');
}

class products extends table_prototype {
	public function __construct(){
		parent::__construct();
		$this->set_table_name('product')->set_auto_lock_in_shared_mode(true);
	}
	public function get_info($where = NULL, $from = NULL){
		$_info	 = parent::get_info($where, $from);
		$return	 = array();
		if(is_array($_info) && !empty($_info)){
			$product_id			 = $_info['id'];
			$_info['image']		 = $this->get_image($product_id);
			$_info['components'] = $this->get_components($product_id);
			$_info['features']	 = $this->get_features($product_id);
			$return				 = $_info;
		}
		return $return;
	}
	public function get_image($product_id){
		return ll('images')->get_image($product_id,'product');
	}
	public function get_id_from_alias($alias){
		$filters	 = array();
		$filters[]	 = array('field' => 'alias', 'operator' => '=', 'value' => $alias);
		$ret		 = $this->get_info($filters);
		$return		 = false;
		if(is_array($ret) && isset($ret['id'])){
			$return = $ret['id'];
		}
		return $return;
	}
	public function get_products_for_category($cat_id){
		$filters	 = array();
		$filters[]	 = array('field' => 'category_id', 'operator' => '=', 'value' => $cat_id);
		$products	 = $this->get_raw($filters);
		$return		 = array();
		if(is_array($products) && !empty($products)){
			foreach($products as $k => $product){
				//get the main image
				//get the features for this product
				$product_id					 = $product['id'];
				$products[$k]['image']		 = $this->get_image($product_id);
				$products[$k]['components']	 = $this->get_components($product_id);
				$products[$k]['features']	 = $this->get_features($product_id);
				$products[$k]['url']		 = $this->get_url($product_id);
			}
			$return = $products;
		}
		return $return;
	}
	public function get_features($product_id){
		//get all the features for this product
		$filters	 = array();
		$filters[]	 = array('field' => 'product_feature.product_id', 'operator' => '=', 'value' => $product_id);
		$join		 = array();
		$join[]		 = array('table' => 'feature', 'how' => 'product_feature.feature_id = feature.id');
		$fields		 = array('feature.*');
		$features	 = $this->get_raw($filters, array(), array(), '', 'product_feature', $join, $fields);
		$return		 = array();
		if(is_array($features) && !empty($features)){
			foreach($features as $k => $feature){
				$features[$k]['value'] = ll('features')->parse_value($feature['value']);
			}
			$return = $features;
		}
		return $return;
	}
	public function get_components($product_id){
		//get all the components for this product
		$filters	 = array();
		$filters[]	 = array('field' => 'component_product.product_id', 'operator' => '=', 'value' => $product_id);
		$join		 = array();
		$join[]		 = array('table' => 'component', 'how' => 'component_product.component_id = component.id');
		$fields		 = array('component.*');
		$components	 = $this->get_raw($filters, array(), array(), '', 'component_product', $join, $fields);
		$return		 = array();
		if(is_array($components) && !empty($components)){
			foreach($components as $k => $component){
				$component_id			 = $component['id'];
				$component[$k]['image']	 = ll('components')->get_image($component_id);
				$component[$k]['parts']	 = ll('components')->get_parts($component_id);
			}
			$return = $components;
		}
		return $return;
	}
	public function get_pages($product_id){
		$pages = ll('pages')->get_pages($product_id, 'product');
		foreach($pages as $page){
			$page['content'] = ll('pages')->prep_content($page['content']);
		}
		return $pages;
	}
	public function get_breadcrumbs($product_id){
		//get parent category and continue up the line
		$product_info			 = $this->get_info($product_id);
		$cat_id					 = $product_info['category_id'];
		$product_bread_crumbs	 = array();
		$product_bread_crumbs[]	 = array('name' => $product_info['name'], 'url' => '/'.$product_info['alias'].'.html');
		$bread_crumbs			 = ll('categories')->get_breadcrumbs($cat_id, $product_bread_crumbs);
		return $bread_crumbs;
	}
	public function get_url($product_id){
		$breadcrumbs				 = $this->get_breadcrumbs($product_id);
		$product_bread_crumb_info	 = array_pop($breadcrumbs);
		return $product_bread_crumb_info['url'];
	}
	public function add($config = 'product'){
		$return = false;
		if(lc('uri')->is_post()){
			$return = parent::add($config);
		}
		return $return;
	}
	public function edit($id, $config = 'product'){
		$return = false;
		if(lc('uri')->is_post() && $id > 0){
			$return = parent::edit($id, $config);
		}
		return $return;
	}
}
