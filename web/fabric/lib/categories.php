<?php

if(!defined('BASEPATH')){
	exit('No direct script access allowed');
}

class categories extends table_prototype {
	protected $related = array('category_image', 'category_page');
	public function __construct(){
		parent::__construct();
		$this->set_table_name('category')->set_auto_lock_in_shared_mode(true);
	}
	public function get_nav_categories(){
		//get all categories where nav == 'y'
		$filters	 = array();
		$filters[]	 = array('field' => 'nav', 'operator' => '=', 'value' => 'y');
		$order_by	 = array('sort_order ASC');
		$categories	 = $this->get_raw($filters, $order_by);
		if(!is_array($categories)){
			$categories = array();
		}
		foreach($categories as $k => $category){
			$categories[$k]['sub_categories'] = $this->get_sub_categories($category['id']);
		}
		return $categories;
	}
	public function get_sub_categories($parent_id){
		$filters		 = array();
		$filters[]		 = array('field' => 'parent_id', 'operator' => '=', 'value' => $parent_id);
		$order_by		 = array('sort_order ASC');
		$sub_categories	 = $this->get_raw($filters, $order_by);
		if(!is_array($sub_categories)){
			$sub_categories = array();
		}
		foreach($sub_categories as $k => $sub_category){
			//get the main image
			$sub_cat_id							 = $sub_category['id'];
			$sub_categories[$k]['url']			 = $this->get_url($sub_cat_id);
			$sub_categories[$k]['description']	 = ll('pages')->prep_content($sub_category['description']);
			$sub_categories[$k]['image']		 = ll('categories')->get_image($sub_cat_id);
		}
		return $sub_categories;
	}
	public function get_id_from_alias($alias){
		$filters	 = array();
		$filters[]	 = array('field' => 'alias', 'operator' => '=', 'value' => $alias);
		$ret		 = $this->get_info($filters);
		$return		 = false;
		if(is_array($ret)&&isset($ret['id'])){
			$return = $ret['id'];
		}
		return $return;
	}
	public function get_image($cat_id){
		return ll('limages')->get_image($cat_id, 'category');
	}
	public function get_all_images($cat_id){
		return ll('limages')->get_images($cat_id, 'category');
	}
	public function get_all_pages($cat_id){
		return ll('pages')->get_all_pages($cat_id, 'category');
	}
	public function get_pages($cat_id){
		$pages = ll('pages')->get_pages($cat_id, 'category');
		foreach($pages as $k => $page){
			$pages[$k]['content'] = ll('pages')->prep_content($page['content']);
		}
		return $pages;
	}
	public function get_breadcrumbs($cat_id, $bread_crumbs = array(), $show_home = true){
		if($cat_id>0){
			$category_info = ll('categories')->get_info($cat_id);
			//we have to form the array backwards and then reverse it
			if(!empty($category_info)){
				if(empty($bread_crumbs)){
					$bread_crumbs[] = array('name' => $category_info['name'], 'url' => '/'.$category_info['alias'].'.html');
				}else{
					$parent_alias = $category_info['alias'];
					foreach($bread_crumbs as $k => $bread_crumb){
						$bread_crumbs[$k]['url'] = '/'.$parent_alias.$bread_crumb['url'];
					}
					$bread_crumbs[] = array('name' => $category_info['name'], 'url' => '/'.$category_info['alias'].'.html');
				}
				while(isset($category_info['parent_id'])&&$category_info['parent_id']>0){
					$parent_id		 = $category_info['parent_id'];
					$category_info	 = ll('categories')->get_info($category_info['parent_id']);
					if($category_info['parent_id']==$parent_id){
						$category_info = array();
					}
					if(!empty($category_info)){
						$parent_alias = $category_info['alias'];
						foreach($bread_crumbs as $k => $bread_crumb){
							$bread_crumbs[$k]['url'] = '/'.$parent_alias.$bread_crumb['url'];
						}
						$bread_crumbs[] = array('name' => $category_info['name'], 'url' => '/'.$category_info['alias'].'.html');
					}
				}
			}
		}
		if($show_home){
			$bread_crumbs[] = array('name' => 'Home', 'url' => '/home.html');
		}
		krsort($bread_crumbs);
		return $bread_crumbs;
	}
	public function get_home_categories(){
		$filters	 = array();
		$filters[]	 = array('field' => 'home', 'operator' => '=', 'value' => 'y');
		$order_by	 = array('sort_order ASC');
		$categories	 = $this->get_raw($filters, $order_by);
		if(!is_array($categories)){
			$categories = array();
		}
		foreach($categories as $k => $category){
			$cat_id								 = $category['id'];
			$categories[$k]['url']				 = $this->get_url($cat_id);
			$categories[$k]['sub_categories']	 = ll('categories')->get_sub_categories($cat_id);
			$categories[$k]['image']			 = ll('categories')->get_image($cat_id);
		}
		return $categories;
	}
	public function get_url($cat_id){
		$breadcrumbs				 = $this->get_breadcrumbs($cat_id);
		$product_bread_crumb_info	 = array_pop($breadcrumbs);
		return $product_bread_crumb_info['url'];
	}
	public function add($config = 'category'){
		$return = false;
		if(lc('uri')->is_post()){
			$return = parent::add($config);
		}
		return $return;
	}
	public function edit($id, $config = 'category'){
		$return = false;
		if(lc('uri')->is_post()&&$id>0){
			$return = parent::edit($id, $config);
		}
		return $return;
	}
	public function get_select_list(){
		$fields		 = array('id', 'name');
		$filters	 = array();
		$filters[]	 = array('field' => 'active', 'operator' => '=', 'value' => 'y');
		$categories	 = $this->get_raw($filters, array(), array(), '', '', array(), $fields);
		$return		 = array();
		foreach($categories as $category){
			$return[$category['id']] = $category['name'];
		}
		return $return;
	}
}
