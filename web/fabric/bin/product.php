<?php

if(!defined('BASEPATH')){
	exit('No direct script access allowed');
}

class product {
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
		$product_alias = lc('uri')->get('product', '');
		if(trim($product_alias) !== ''){
			//get the id for this product
			//get any ecom pages for this product
			$product_id		 = ll('products')->get_id_from_alias($product_alias);
			$product_title	 = "Product Not Found";
			$ecom_content	 = '';
			if($product_id > 0){
				//good we have one
				$product_info = ll('products')->get_info($product_id);

				$product_title = $product_info['name'];
				if(isset($product_info['title']) && trim($product_info['title']) != ''){
					$product_title = $product_info['title'];
				}
				//content_pages
				$pages = ll('products')->get_pages($product_id);
				foreach($pages as $page){
					//prep ecom page to be displayed
					$ecom_content .= $page['content'];
				}
				$breadcrumbs = ll('products')->get_breadcrumbs($product_id);
			}else{
				die('no product found');
			}
		}else{
			die('no product??');
		}
		ll('display')
				->assign('title', $product_title)
				->assign('breadcrumbs', $breadcrumbs)
				->assign('ecom_content', $ecom_content)
				->assign('product', $product_info)
				->show('product');
	}
	public function web_manage(){
		//get all products
		//list them and click links to edit them
		$config			 = lc('config')->get_and_unload_config('product');
		$filters		 = ll('display')->get_filter_filters($config);
		$limit			 = ll('display')->get_limit();
		$products		 = ll('products')->get_all($filters, array(), array(), $limit, 'product', array(), array());
		$product_count	 = count($products);
		if($product_count == 1){
			fabric::redirect('/product/edit/id/'.$products[0]['id']);
		}
//		var_dump($products);
		ll('display')
				->assign('_config', $config)
				->assign('display_table', 'Product')
				->assign('rows', $products)
				->assign('row_count', $product_count)
				->show('list');
	}
	public function web_add(){
		$return	 = ll('products')->add();
		$errors	 = array();
		if($return !== false){
			if(is_array($return)){
				//we have errors
				$errors = $return;
			}else{
				//we did it!
				fabric::redirect(lc('uri')->create_auto_uri(array(CLASS_KEY => 'product', TASK_KEY => 'edit', 'id' => $return)));
			}
		}
		$form_url	 = lc('uri')->create_auto_uri(array(CLASS_KEY => 'product', TASK_KEY => 'add'));
		$config		 = lc('config')->get_and_unload_config('product');
		ll('display')
				->assign('_config', $config)
				->assign('display_table', 'Product')
				->assign('action', 'add')
				->assign('errors', $errors)
				->assign('form_url', $form_url)
				->show('form');
	}
	public function web_edit(){
		$id = intval(lc('uri')->get('id', 0));
		if($id > 0){
			$return			 = ll('products')->edit($id);
			$errors			 = array();
			$product_info	 = ll('products')->get_info($id);
			if($return !== false){
				if(is_array($return)){
					//we have errors
					$errors = $return;
				}else{
					//we did it!
					fabric::redirect(lc('uri')->create_auto_uri(array(CLASS_KEY => 'product', TASK_KEY => 'edit', 'id' => $id)));
				}
			}
			$form_url	 = lc('uri')->create_auto_uri(array(CLASS_KEY => 'product', TASK_KEY => 'edit', 'id' => $id));
			$config		 = lc('config')->get_and_unload_config('product');
			ll('display')
					->assign('_config', $config)
					->assign('display_table', 'Product')
					->assign('action', 'edit')
					->assign('errors', $errors)
					->assign('info', $product_info)
					->assign('id', $id)
					->assign('form_url', $form_url)
					->show('form');
		}
	}
	public function web_delete(){
		$id		 = intval(lc('uri')->get('id', 0));
		$return	 = false;
		if($id > 0){
			if(lc('uri')->post('delete', NULL) != ''){
				$return = ll('products')->remove($id);
			}
		}
		ll('display')
				->assign('class_key', 'product')
				->assign('deleted', $return)
				->assign('id', $id)
				->show('delete');
	}
}
