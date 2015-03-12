<?php

class display_template extends display {
	private $template			 = 'default';
	private $fail_over_template	 = 'default';
	private $hide_show			 = array(
		'head'	 => true,
		'foot'	 => true,
	);
	private $default_page_rows	 = 50;
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
	public function get_filter_operator($form_type){
		$return = '=';
		switch($form_type){
			case'textarea':
				$return	 = "LIKE";
				break;
			case'date':
				$return	 = ">=";
				break;
			default:
				break;
		}
		return $return;
	}
	public function create_select($key, $value, $form = array(), $extras = ''){
		$required	 = isset($form['required']) && $form['required'];
		$ret		 = "<select class='".($required?"required_field ":"")."$extras' id='id_$key' name='$key'>";
		$ret .= "<option value='' ".(($value == '')?'SELECTED':'').">Please Select</option>";
		$table_name	 = isset($form['table'])?$form['table']:false;
		if($table_name){
			//select all fields from db id and name in config file
			$id_col			 = 'id';
			$name_col		 = $form['select_show'];
			$extra_fields	 = array($id_col, $name_col);
			$values			 = ll('table_prototype')->get_all(array(), array(), array(), '', $table_name, array(), $extra_fields);
			if(is_array($values)){
				foreach($values as $val){
					$ret .= "<option value='$val[$id_col]'".(($value == $val[$id_col])?'SELECTED':'').">$val[$name_col]</option>";
				}
			}
		}else{
			$transform = isset($form['transform'])?$form['transform']:false;
			if(is_array($transform)){
				foreach($transform as $k => $v){
					$ret .= "<option value='$k'".(($value == $k)?'SELECTED':'').">$v</option>";
				}
			}
		}
		$ret .= "</select>";
		return $ret;
	}
	public function get_form_value($key, $default = ''){
		$ret = '';
		if(!is_null(lc('uri')->post($key, NULL))){
			$ret = lc('uri')->post($key);
		}elseif(!is_null(lc('uri')->get($key, NULL))){
			$ret = lc('uri')->get($key);
		}else{
			$ret = $default;
		}
		return $ret;
	}
	public function make_filter_field($key, $form = array(), $value = ''){
		$form_type	 = isset($form['type'])?$form['type']:'';
		$length		 = isset($form['length'])?$form['length']:8;
		$ret		 = "<input type='text' value='$value' name='$key' maxlength='$length' size='16'/>";
		switch($form_type){
			case'select':
				$ret	 = $this->create_select($key, $value, $form);
				break;
			case 'date':
				$lt_val	 = lc('uri')->get($key.'lt', '');
				$gt_val	 = lc('uri')->get($key.'gt', '');
				$ret	 = "<input type='text' value='$gt_val' name='$key"."gt' maxlength='$length' size='16'/>";
				$ret .= "<input type='text' value='$lt_val' name='$key"."lt' maxlength='$length' size='16'/>";
			default:
				break;
		}
		return $ret;
	}
	public function make_list_field($key, $value = '', $form = array()){
		$return		 = $value;
		$form_type	 = isset($form['type'])?$form['type']:'';
		switch($form_type){
			case'select':
				$table_name	 = isset($form['table'])?$form['table']:false;
				$transform	 = isset($form['transform'])?$form['transform']:false;
				if($table_name){
					$select_show = isset($form['select_show'])?$form['select_show']:'id';
					$prim_id_col = 'id';
					$filters	 = array();
					$filters[]	 = array('field' => $prim_id_col, 'operator' => '=', 'value' => $value);
					$ret		 = ll('table_prototype')->get_info($filters, $table_name);
					$return		 = isset($ret[$select_show])?$ret[$select_show]:$value;
				}elseif($transform){
					$return = $transform[$value];
				}
				break;
		}
		return $return;
	}
	public function make_form_field($key, $form = array(), $value = '', $action = 'add', $extras = ''){
		$length			 = isset($form['length'])?intval($form['length']):8;
		$required		 = isset($form['required'])?(is_array($form['required'])?(intval($form['required'][$action])):intval($form['required'])):false;
		$size_num		 = ceil($length / 10) * 10;
		$size			 = ($size_num >= 40)?40:$size_num;
		$length_display	 = $length > 0?"maxlength='$length'":"";
		$class			 = "class='".($required?"required_field ":'')."$extras'";
		$ret			 = "<input type='text' $class value='$value' name='$key' $length_display size='$size'/>";
		if(isset($form['type'])){
			switch($form['type']){
				case'select':
					$ret = $this->create_select($key, $value, $form, $extras);
					break;
				case'id':
					$ret = "<span>$value</span>";
					break;
				case'textarea':
					$ret = "<textarea $class name='$key' $length_display style='max-width:438px;max-height:100px; height:50px;width:215px'>$value</textarea>";
					break;
				case'image':
					$ret = "<input class='need_file_path'  type='text' $class value='$value' name='$key' $length_display size='$size'/>";
					break;
				case 'date':
				default:
					break;
			}
		}

		return $ret;
	}
	public function get_filter_filters($config){
		$filters = array();
		if(is_array(lc('uri')->get_GET())){
			foreach(lc('uri')->get_GET() as $k => $v){
				$key = $k;
				if(isset($config[substr($k, 0, -2)]) && $config[substr($k, 0, -2)]['form']['type'] == 'date'){
					$key = substr($k, 0, -2);
				}
				if(isset($config[$key]) && lc('uri')->get($k, '') != ''){
					if($config[$key]['form']['type'] != 'date'){
						$filters[] = array('field' => $key, 'operator' => $this->get_filter_operator($k), 'value' => $v);
					}else{
						$greater_or_less = substr($k, -2);
						if($greater_or_less == 'lt'){
							$filters[] = array('field' => $key, 'operator' => '<=', 'value' => date('Y-m-d H:i:s', strtotime($v)));
						}else{
							$filters[] = array('field' => $key, 'operator' => '>=', 'value' => date('Y-m-d H:i:s', strtotime($v)));
						}
					}
				}
			}
		}
		return $filters;
	}
	public function pagination($config, $row_count){
		$last				 = ceil($row_count / $this->default_page_rows);
		$pagination			 = '';
		$query_string_array	 = $this->parse_query_string_for_pagi($config);
//        $_SERVER['QUERY_STRING'];
		if($last > 0){
			$uri		 = array_merge($query_string_array, array(CLASS_KEY => lc('uri')->get(CLASS_KEY), TASK_KEY => 'manage'));
			$page_num	 = $this->get_page_num($row_count);
			$pagination	 = "<ul class='pagination' style='float:right'>";
			if($page_num == 1){
				$pagination.="<li class='disabled'><span>&lt;</span></li>";
			}else{
				$pagination.="<li><a href='".lc('uri')->create_auto_uri(array_merge($uri, array('page_num' => 1)))."'>&lt;</a></li>";
				$pagination.="<li><a href='".lc('uri')->create_auto_uri(array_merge($uri, array('page_num' => ($page_num - 1))))."'>Prev</a></li>";
			}
			$first_num	 = ($page_num - 2);
			$last_num	 = ($page_num + 2);
			$pagis		 = range($first_num, $last_num);
//		if(($page_num + 4) > $last){
			$add		 = 0;
			$sub		 = 0;
			foreach($pagis as $page){
				if($page < 1){
					$add++;
				}
				if($page > $last){
					$sub++;
				}
			}
			$pagis = range(($first_num + $add - $sub), ($last_num + $add - $sub));
			foreach($pagis as $page){
				if($page > 0 && $page <= $last){
					$class = '';
					if($page_num == $page){
						$class = "class='active'";
					}
					$pagination.="<li $class><a href='".lc('uri')->create_auto_uri(array_merge($uri, array('page_num' => $page)))."'>$page</a></li>";
				}
			}
			if($page_num == $last){
				$pagination.="<li class='disabled'><span>&gt;</span></li>";
			}else{
				$pagination.="<li><a href='".lc('uri')->create_auto_uri(array_merge($uri, array('page_num' => ($page_num + 1))))."'>Next</a></li>";
				$pagination.="<li><a href='".lc('uri')->create_auto_uri(array_merge($uri, array('page_num' => $last)))."'>&gt;</a></li>";
			}
		}
		return $pagination;
	}
	public function parse_query_string_for_pagi($config){
		$query_string	 = array();
		$output			 = lc('uri')->get_GET();
		if(is_array($output) && count($output) > 0){
			foreach($output as $key => $value){
				if(($value != '' && isset($config[$key])) || $key == 'filter_submit'){
					$query_string[$key] = $value;
				}
			}
		}
		return $query_string;
	}
	public function get_page_num($row_count){
		$page	 = lc('uri')->get('page_num', 0);
		$last	 = ceil($row_count / $this->default_page_rows);
		if($page < 1){
			$page = 1;
		}elseif($page > $last){
			$page = $last;
		}
		return $page;
	}
}
