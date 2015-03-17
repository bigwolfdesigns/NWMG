<?php

if(!defined('BASEPATH')){
	exit('No direct script access allowed');
}

class pages extends table_prototype {
	public function __construct(){
		parent::__construct();
		$this->set_table_name('page')->set_auto_lock_in_shared_mode(true);
	}
	public function get_info($where = NULL, $from = NULL){
		if(!is_array($where) && !is_numeric($where) && !is_null($where)){
			//we're passing in the alias
			$page_where		 = array();
			$page_where[]	 = array('field' => 'alias', 'operator' => '=', 'value' => $where);
			$where			 = $page_where;
		}
		$_info = parent::get_info($where, $from);
		if(is_array($_info) && !empty($_info)){
			$_info['content'] = $this->prep_content($_info['content']);
		}
		return $_info;
	}
	public function get_pages($id, $type = 'category'){
		$lib = ll('table_prototype');
		switch($type){
			case'product':
				$lib->set_table_name($type.'_page');
				$field	 = $type.'_id';
				break;
			case'category':
			default:
				$field	 = 'category_id';
				$lib->set_table_name('category_page');
		}
		$filters	 = array();
		$filters[]	 = array('field' => $field, 'operator' => '=', 'value' => $id);
		$t_pages	 = $lib->get_raw($filters);
		$return		 = array();
		if(is_array($t_pages) && !empty($t_pages)){
			$page_ids = array();
			foreach($t_pages as $page){
				$page_ids[] = $page['page_id'];
			}
			$filters	 = array();
			$filters[]	 = array('field' => 'id', 'operator' => 'IN', 'value' => $page_ids);
			$return		 = $this->get_raw($filters);
			if(!is_array($return) || empty($return)){
				$return = array();
			}
		}
		return $return;
	}
	public function get_all_pages($id, $type = 'page'){
		//returns the info
		$lib				 = ll('table_prototype');
		$field				 = 'id';
		$skip_initial_query	 = false;
		switch($type){
			case'category':
			case'product':
			case'component':
			case'part':
				$lib->set_table_name($type.'_page');
				$field				 = $type.'_id';
				break;
			case'image':
			default:
				$type				 = 'page';
				$skip_initial_query	 = true;
				break;
		}
		$filters	 = array();
		$filters[]	 = array('field' => $field, 'operator' => '=', 'value' => $id);
		$return		 = $lib->get_all($filters);
		if(!is_array($return) || empty($return)){
			$return = array();
		}
		return $return;
	}
	public function prep_content($content){
		// Can look like this : [[[image:122{class:my-class,width:107}]]]
		// Or like this : [[[image:122]]]
		$matches = array();
		preg_match_all('/\[\[\[(.+?):(.+?)\]\]\]/', $content, $matches);
		$from	 = array();
		$to		 = array();
		if(isset($matches[1])){
			foreach($matches[1] as $k => $match){
				$from[] = $matches[0][$k];
				switch($match){
					case'image':
					case'product':
					case'catgory':
					case'component':
					case'part':
						$id					 = $matches[2][$k];
						$attribute_string	 = "";
						if(is_numeric($id)){
							//we have an id, no attributes
							$src = ll('images')->get_image($id, $match);
						}else{
							$attr_matches = array();
							preg_match('/(.+?){(.+?)}/', $id, $attr_matches);
							if(isset($attr_matches[1]) && !empty($attr_matches[1])){
								$id			 = $attr_matches[1];
								$src		 = ll('images')->get_image($id, $match);
								$attributes	 = explode(',', $attr_matches[2]);
								if(is_array($attributes) && !empty($attributes)){
									foreach($attributes as $attribute){
										$attrs = explode(':', $attribute);
										$attribute_string .=$attrs[0]."='".$attrs[1]."' ";
									}
								}
							}else{
								//uh oh somehting went wrong...
								$src = "/images/no-image.png";
							}
						}
						$converted	 = "<img src='$src' $attribute_string />";
						break;
					default:
						$converted	 = '';
						break;
				}
				$to[] = $converted;
			}
		}
		$prepped = stripslashes(nl2br(str_replace($from, $to, $content)));
		return $prepped;
	}
	public function add($config = 'page'){
		$return = false;
		if(lc('uri')->is_post()){
			$return = parent::add($config);
		}
		return $return;
	}
	public function edit($id, $config = 'page'){
		$return = false;
		if(lc('uri')->is_post() && $id > 0){
			$return = parent::edit($id, $config);
		}
		return $return;
	}
}
