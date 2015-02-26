<?php

if(!defined('BASEPATH')){
	exit('No direct script access allowed');
}

class category {
	/**
	 * Initialize the default class
	 *
	 * @access	private
	 * @return	void
	 */
	public function __construct(){
		ll('client')->set_initial();
		$task = lc('uri')->get(TASK_KEY, 'view');
		if(method_exists($this, 'web_'.$task) && is_callable(array($this, 'web_'.$task))){
			ll('display')->assign('task', $task);
			$this->{'web_'.$task}();
		}else{
			ll('display')->assign('task', 'view');
			$this->web_view();
		}
	}
	public function web_view(){
		$category_alias = lc('uri')->get('category', '');
		if(trim($category_alias) !== ''){
			//get the id for this category
			//get any ecom pages for this category
			//get all the sub-categories for this category
			//if there are no sub-categories for this cat then get the products
			$cat_id			 = ll('categories')->get_id_from_alias($category_alias);
			$category_title	 = "Category Not Found";
			$ecom_content	 = '';
			if($cat_id > 0){
				//good we have one
				$cat_info		 = ll('categories')->get_info($cat_id);
				$category_title	 = $cat_info['name'];
				if(trim($cat_info['title']) != ''){
					$category_title = $cat_info['title'];
				}
				//content_pages
				$pages = ll('categories')->get_pages($cat_id);
				foreach($pages as $page){
					//prep ecom page to be displayed
					$content = $page['content'];
					$ecom_content .= ll('pages')->prep_content($content);
				}
				$sub_categories = ll('categories')->get_sub_categories($cat_id);
				if(!empty($sub_categories)){
					foreach($sub_categories as $k => $sub_category){
						//get the main image
						$sub_cat_id					 = $sub_category['id'];
						$sub_categories[$k]['image'] = ll('categories')->get_image($sub_cat_id);
					}
				}else{
					//get the products
					$products = ll('products')->get_products_for_category($cat_id);
					foreach($products as $k => $product){
						//get the main image
						$product_id				 = $product['id'];
						$products[$k]['image']	 = ll('products')->get_image($product_id);
					}
				}
			}
		}
		ll('display')
				->assign('title', $category_title)
				->assign('sub_categories', $sub_categories)
				->assign('products', $products)
				->assign('ecom_content', $ecom_content)
				->show('category');
	}
}
