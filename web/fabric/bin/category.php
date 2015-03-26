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
		$tasks_need_login	 = array('', 'add', 'manage', 'edit', 'delete');
		ll('client')->set_initial();
		$task				 = lc('uri')->get(TASK_KEY, 'view');
		if(ll('client')->is_privileged('CAT')){
			if(((!in_array($task, $tasks_need_login)) || ((in_array($task, $tasks_need_login) && ll('users')->is_logged())))){
				if(method_exists($this, 'web_'.$task) && is_callable(array($this, 'web_'.$task))){
					ll('display')->assign('task', $task);
					$this->{'web_'.$task}();
				}else{
					ll('display')->assign('task', 'view');
					$this->web_view();
				}
			}else{
				fabric::redirect('/control/login.html', "You must be logged in to view this page.", 5, true);
			}
		}else{
			fabric::redirect('/control.html', "Insufficient Privileges", 5, true);
		}
	}
	public function web_view(){
		$category_alias	 = lc('uri')->get('category', '');
		$ecom_content	 = '';
		if(trim($category_alias) !== ''){
			//get the id for this category
			//get any ecom pages for this category
			//get all the sub-categories for this category
			//if there are no sub-categories for this cat then get the products
			$cat_id = ll('categories')->get_id_from_alias($category_alias);
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
					$ecom_content .= $page['content'];
				}
				$sub_categories	 = ll('categories')->get_sub_categories($cat_id);
				//get the products
				$products		 = ll('products')->get_products_for_category($cat_id);
				$breadcrumbs	 = ll('categories')->get_breadcrumbs($cat_id);
			}
		}
		ll('display')
				->assign('title', $category_title)
				->assign('sub_categories', $sub_categories)
				->assign('products', $products)
				->assign('ecom_content', $ecom_content)
				->assign('category', $cat_info)
				->assign('breadcrumbs', $breadcrumbs)
				->show('category');
	}
	public function web_manage(){
		//get all categories
		//list them and click links to edit them
		$config			 = lc('config')->get_and_unload_config('category');
		$filters		 = ll('display')->get_filter_filters($config);
		$categories		 = ll('categories')->get_all($filters, array(), array(), '', 'category', array(), array());
		$category_count	 = count($categories);
		if($category_count == 1){
			fabric::redirect('/category/edit/id/'.$categories[0]['id']);
		}
		ll('display')
				->assign('_config', $config)
				->assign('display_table', 'Category')
				->assign('rows', $categories)
				->assign('row_count', $category_count)
				->show('list');
	}
	public function web_add(){
		$return	 = ll('categories')->add();
		$errors	 = array();
		if($return !== false){
			if(is_array($return)){
				//we have errors
				$errors = $return;
			}else{
				//we did it!
				fabric::redirect(lc('uri')->create_auto_uri(array(CLASS_KEY => 'category', TASK_KEY => 'edit', 'id' => $return)));
			}
		}
		$form_url	 = lc('uri')->create_auto_uri(array(CLASS_KEY => 'category', TASK_KEY => 'add'));
		$config		 = lc('config')->get_and_unload_config('category');
		ll('display')
				->assign('_config', $config)
				->assign('display_table', 'Category')
				->assign('action', 'add')
				->assign('errors', $errors)
				->assign('form_url', $form_url)
				->show('form');
	}
	public function web_edit(){
		$id = intval(lc('uri')->get('id', 0));
		if($id > 0){
			$return		 = ll('categories')->edit($id);
			$errors		 = array();
			$cat_info	 = ll('categories')->get_info($id);
			if($return !== false){
				if(is_array($return)){
					//we have errors
					$errors = $return;
				}else{
					//we did it!
					fabric::redirect(lc('uri')->create_auto_uri(array(CLASS_KEY => 'category', TASK_KEY => 'edit', 'id' => $id)));
				}
			}
			$form_url		 = lc('uri')->create_auto_uri(array(CLASS_KEY => 'category', TASK_KEY => 'edit', 'id' => $id));
			$config			 = lc('config')->get_and_unload_config('category');
			$images			 = ll('images')->get_all();
			$category_images = ll('categories')->get_all_images($id);
			$pages			 = ll('pages')->get_all();
			$category_pages	 = ll('categories')->get_all_pages($id);
			foreach($category_images as $k => $category_image){
				$image_info			 = ll('images')->get_info($category_image['image_id']);
				$category_images[$k] = array_merge($image_info, $category_images[$k]);
				foreach($images as $k => $image){
					if($image['id'] == $category_image['image_id']){
						unset($images[$k]);
					}
				}
			}
			foreach($category_pages as $k => $category_page){
				$page_info			 = ll('pages')->get_info($category_page['page_id']);
				$category_pages[$k]	 = array_merge($page_info, $category_pages[$k]);
				foreach($pages as $k => $page){
					if($page['id'] == $category_page['page_id']){
						unset($pages[$k]);
					}
				}
			}
			$related_tables = array(
				'image_id'	 => array(
					'base-image'	 => $images,
					'category_image' => $category_images,
				),
				'page_id'	 => array(
					'base-page'		 => $pages,
					'category_page'	 => $category_pages,
				),
			);
			ll('display')
					->assign('_config', $config)
					->assign('display_table', 'Category')
					->assign('action', 'edit')
					->assign('errors', $errors)
					->assign('info', $cat_info)
					->assign('id', $id)
					->assign('form_url', $form_url)
					->assign('related', $related_tables)
					->show('form');
		}
	}
	public function web_delete(){
		$id		 = intval(lc('uri')->get('id', 0));
		$return	 = false;
		if($id > 0){
			if(lc('uri')->post('delete', NULL) != ''){
				$return = ll('categories')->remove($id);
			}
		}
		ll('display')
				->assign('class_key', 'category')
				->assign('deleted', $return)
				->assign('id', $id)
				->show('delete');
	}
}
