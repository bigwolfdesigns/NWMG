<?php

if(!defined('BASEPATH')) exit('No direct script access allowed');

// ------------------------------------------------------------------------

class table_prototype {
	protected $last_result				 = false;
	protected $current					 = array();
	protected $dodb						 = array();
	protected $output					 = array();
	protected $fields					 = array();
	protected $related					 = array();
	protected $bulk_actions				 = array();
	protected $last_query				 = false;
	protected $level_debug				 = false;
	protected $query_delimiter			 = ';';
	protected $auto_lock_in_shared_mode	 = false;
	protected $db						 = NULL;
	protected $read						 = false;
	public function __clone(){
		$this->_reset_dodb();
	}
	public function close($read = NULL){
		return $this->db->close($read);
	}
	public function __construct(){
		$this->_reset_dodb();
		$this->dodb['table'] = '';
		$this->db			 = ll('db');
	}
	public function reset(){
		$this->_reset_dodb();
	}
	private function _reset_dodb(){
		$this->dodb['what']			 = '';
		$this->dodb['prepare']		 = '';
		$this->dodb['using']		 = '';
		$this->dodb['fields']		 = array();
		$this->dodb['set']			 = array();
		$this->dodb['join']			 = array();
//		$this->dodb['table']			= get_class($this);
		$this->dodb['ttable']		 = '';
		$this->dodb['table_alias']	 = '';
		$this->dodb['where']		 = array();
		$this->dodb['group']		 = array();
		$this->dodb['having']		 = array();
		$this->dodb['order']		 = array();
		$this->dodb['limit']		 = '';
		$this->dodb['postfix']		 = '';
		$this->dodb['on_dup_update'] = array();
//		$this->dodb['read_db']		 = false;
//		$this->dodb['sql_cache']	 = false;
	}
	public function set_read($read){
		$this->dodb['read_db'] = $read;
		return $this;
	}
	public function get_read(){
		return isset($this->dodb['read_db'])?$this->dodb['read_db']:false;
	}
	public function set_sql_cache($sql_cache){
		$this->dodb['sql_cache'] = $sql_cache;
		return $this;
	}
	public function last_result(){
		return $this->last_result;
	}
	public function set_table_name($table){
		$this->dodb['table'] = $table;
		return $this;
	}
	public function execute($prepare, $using){
		$this->dodb['what']		 = 'EXECUTE';
		$this->dodb['prepare']	 = $prepare;
		$this->dodb['using']	 = $using;
		return $this;
	}
	public function prepare($prepare){
		$this->dodb['prepare'] = $prepare;
		return $this;
	}
	public function insert($extra_insert = ''){
		$this->dodb['what'] = 'INSERT '.$extra_insert;
		return $this;
	}
	public function replace($extra_replace = ''){
		$this->dodb['what'] = 'REPLACE '.$extra_replace;
		return $this;
	}
	public function select($extra_select = ''){
		$this->dodb['what'] = 'SELECT '.$extra_select;
		return $this;
	}
	public function update(){
		$this->dodb['what'] = 'UPDATE';
		return $this;
	}
	public function delete(){
		$this->dodb['what'] = 'DELETE';
		return $this;
	}
	public function from($table){
		$this->dodb['ttable'] = $table;
		return $this;
	}
	public function table($table){
		$this->dodb['ttable'] = $table;
		return $this;
	}
	public function into($table){
		$this->dodb['ttable'] = $table;
		return $this;
	}
	public function alias($alias){
		$this->dodb['table_alias'] = $alias;
		return $this;
	}
	public function qjoin($join){
		return $this->join($join);
	}
	public function join($join){
		if(is_array($join)){
			if(isset($join['on'])&&!isset($join['how'])){
				$join['how'] = $join['on'];
			}
			if(isset($join['join'])&&!isset($join['direction'])){
				$join['direction'] = $join['join'];
			}
			if(isset($join['table'])&&isset($join['how'])){
				if(!isset($join['direction'])) $join['direction']		 = 'left';
				$this->dodb['join'][]	 = array('table' => $join['table'], 'how' => $join['how'], 'direction' => $join['direction']);
			} else{
				foreach($join as $v){
					$this->qjoin($v);
				}
			}
		}
		return $this;
	}
	public function field($field){
		return $this->fields($field);
	}
	public function fields($fields = array()){
		if(is_array($fields)){
			$this->dodb['fields'] = $fields;
		}else{
			$this->dodb['fields'][] = $fields;
		}
		return $this;
	}
	public function set($field, $value = NULL, $SQL_value = false){
		//use $SQL_value=true if this is a SQL function
		if(is_array($field)){
			foreach($field as $k => $v){
				$this->set($k, $v);
			}
		}else{
			$this->dodb['set'][$field] = array($value, $SQL_value);
		}
		return $this;
	}
	public function where($where = array()){
		if(isset($where['field'])||isset($where['name'])){
			$this->dodb['where'][] = $where;
		}else{
			$this->dodb['where'] = $where;
		}
		return $this;
	}
	public function postfix($postfix){
		$this->dodb['postfix'] = ''.$postfix;
		return $this;
	}
	public function on_duplicate_key_update($on_dup_update){
//		ON DUPLICATE KEY UPDATE
		if(is_array($on_dup_update)&&count($on_dup_update)>0){
			$this->dodb['on_dup_update'] = $on_dup_update;
		}
		return $this;
	}
	public function group($group = array()){
		if(is_array($group)){
			if(!empty($group)&&$group!=array('')){
				$this->dodb['group'] = $group;
			}
		}elseif($group!=''){
			$this->dodb['group'][] = $group;
		}
		return $this;
	}
	public function having($having = array()){
		if(isset($having['field'])||isset($having['name'])){
			$this->dodb['having'][] = $having;
		}else{
			$this->dodb['having'] = $having;
		}
		return $this;
	}
	public function order($order = array()){
		if(is_array($order)){
			if(!empty($order)&&$order!=array('')){
				$this->dodb['order'] = $order;
			}
		}elseif($order!=''){
			$this->dodb['order'][] = $order;
		}
		return $this;
	}
	public function limit($limit = ''){
		if($limit==''){
			$this->dodb['limit'] = '';
		}else{
			$this->dodb['limit'] = 'LIMIT '.$limit;
		}
		return $this;
	}
	public function set_auto_lock_in_shared_mode($auto_lock_in_shared_mode){
		$this->auto_lock_in_shared_mode = (bool)$auto_lock_in_shared_mode;
		return $this;
	}
	private function _create_query(){
		$qry = '';
		$nl	 = "\n";
		$tab = "\t";
		if($this->dodb['what']=='EXECUTE'){
			$qry .= $nl.'EXECUTE '.$this->dodb['prepare'].' USING ';
			if(is_array($this->dodb['using'])){
				$qry .= implode(',', $this->dodb['using']);
			}else{
				$qry .= $this->dodb['using'];
			}
			$qry .= ';';
		}else{
			if($this->dodb['prepare']!=''){
				$qry .= $nl.'PREPARE '.$this->dodb['prepare'].' FROM ';
			}
			$qry .= $nl.$this->dodb['what'];
			if(substr($this->dodb['what'], 0, 6)=='INSERT'||substr($this->dodb['what'], 0, 7)=='REPLACE'){
				$qry .= $nl.'INTO ';
			}elseif(substr($this->dodb['what'], 0, 6)=='SELECT'){
				$sql_cache = isset($this->dodb['sql_cache'])?$this->dodb['sql_cache']:false; //true, false, 'once', 'no'
				if($sql_cache==='no'){
					$qry .= ' SQL_NO_CACHE';
				}elseif($sql_cache!==false){
					$qry .= ' SQL_CACHE';
				}
				if(is_array($this->dodb['fields'])&&!empty($this->dodb['fields'])){
					$qry .= ' '.implode(','.$nl.$tab, $this->dodb['fields']);
				}else{
					$qry .= ' * ';
				}
				$qry .= $nl.'FROM ';
			}elseif($this->dodb['what']=='DELETE'){
				$qry .= $nl.'FROM ';
			}elseif($this->dodb['what']=='UPDATE'){
				
			}
			if(is_array($this->dodb['ttable'])){
				$qry .= ' `'.implode('`,`', $this->dodb['ttable']).'` ';
			}elseif($this->dodb['ttable']!=''){
				$qry .= ' `'.$this->dodb['ttable'].'` ';
			}else{
				$qry .= ' `'.$this->dodb['table'].'` ';
			}
			if($this->dodb['table_alias']!=''){
				$qry .= ' AS `'.$this->dodb['table_alias'].'` ';
			}
			if($this->dodb['join']!=array()){
				if(substr($this->dodb['what'], 0, 6)=='SELECT'||$this->dodb['what']=='DELETE'){
					foreach($this->dodb['join'] as $join){
						switch($join['direction']){
							case 'right':
							case 'left':
							case 'inner':
							case 'outer':
								$qry .= $nl.$tab.strtoupper($join['direction']);
								break;
							default:
								$qry .= $nl.$tab.'INNER';
						}
						$qry .= ' JOIN '.$join['table'].' ON '.$join['how'];
					}
				}
			}
			if($this->dodb['what']=='UPDATE'||substr($this->dodb['what'], 0, 6)=='INSERT'||substr($this->dodb['what'], 0, 7)=='REPLACE'){
				$aqry = array();
				foreach($this->dodb['set'] as $field => $value){
//					if($value[1] || ((string)(float)$value[0] === (string)$value[0])){	//MySQL function or number
					if($value[1]||(is_numeric($value[0])&&!is_string($value[0]))){ //MySQL function or number
						$valueDb = $value[0];
					}else{
						$valueDb = '"'.$this->db->real_escape_string($value[0]).'"';
					}
					$aqry[]					 = '`'.$field.'` = '.$valueDb.' ';
					$this->current[$field]	 = $value;
				}
				$qry .= $nl.'SET '.implode(','.$nl.$tab, $aqry);
			}
			if($this->dodb['what']=='UPDATE'||substr($this->dodb['what'], 0, 6)=='SELECT'||$this->dodb['what']=='DELETE'){
				if($this->dodb['where']==array()&&($this->dodb['what']=='UPDATE'||$this->dodb['what']=='DELETE')){
					$this->where(array('field' => 'id', 'operator' => '=', 'value' => $this->get('id')));
				}

				if($this->dodb['where']!=array()){
					$qry .= $nl.'WHERE '.$this->_process_where($this->dodb['where']);
				}
			}
			if(substr($this->dodb['what'], 0, 6)!='INSERT'||substr($this->dodb['what'], 0, 7)!='REPLACE'){
				if(isset($this->dodb['group'])&&is_array($this->dodb['group'])&&!empty($this->dodb['group'])){
					$qry .= $nl.'GROUP BY '.implode(','.$nl.$tab, $this->dodb['group']);
				}
				if($this->dodb['having']!=array()){
					$qry .= $nl.'HAVING '.$this->_process_where($this->dodb['having']);
				}
				if(isset($this->dodb['order'])&&is_array($this->dodb['order'])&&!empty($this->dodb['order'])){
					$qry .= $nl.'ORDER BY '.implode(','.$nl.$tab, $this->dodb['order']);
				}
			}
			$qry .= $this->dodb['postfix'];
			if(substr($this->dodb['what'], 0, 6)=='INSERT'&&is_array($this->dodb['on_dup_update'])&&count($this->dodb['on_dup_update'])>0){
				$qry .= $nl.'ON DUPLICATE KEY UPDATE ';
				foreach($this->dodb['on_dup_update'] as $k => $v){
					$qry .= ' `'.$k.'`='.$v.', ';
				}
				$qry = substr(trim($qry), 0, -1);
			}

			if(substr($this->dodb['what'], 0, 6)!='INSERT'&&isset($this->dodb['limit'])){
				$qry .= $nl.$this->dodb['limit'];
			}
			if($this->auto_lock_in_shared_mode){
				if(substr($this->dodb['what'], 0, 6)=='SELECT'){
					$qry .= ' LOCK IN SHARE MODE';
				}
			}
			$qry .= $this->query_delimiter;
		}
		return $qry;
	}
	public function run(){
		$qry				 = $this->_create_query();
		$this->debug($qry);
		//mail('fabrizio@fabric.com','qry',$qry);
//		var_dump($qry);echo '<br />';
		$read				 = isset($this->dodb['read_db'])?$this->dodb['read_db']:false; //true, false, 'once'
		$sql_cache			 = isset($this->dodb['sql_cache'])?$this->dodb['sql_cache']:false; //true, false, 'once'
		$this->last_query	 = $qry;
		$this->last_result	 = $this->db->query($qry, $read!==false);
		if($read=='once'){
			$this->set_read(false);
		}
		if($sql_cache=='once'||$sql_cache=='no'){
			$this->set_sql_cache(false);
		}

		//bad practice ....
//		if(substr($this->dodb['what'],0,6)=='INSERT' || $this->dodb['what'] =='REPLACE'){
//			$this->_get_info($this->db->insert_id());
//		}
		//cache removal for updates
//		if(($this->dodb['what'] == 'UPDATE' || $this->dodb['what'] == 'DELETE' || $this->dodb['what'] == 'REPLACE') && $this->get('id')>0){
//			$ckey = md5($this->get('id').($this->dodb['ttable']!=''?$this->dodb['ttable']:$this->dodb['table']));
//			lc('cache')->delete($ckey);
//		}
		$this->_reset_dodb();
		return $this;
	}
	public function do_db(){
		return $this->run();
	}
	private function _get_info($id, $from = '', $prepare = false){
//		$ckey = md5(serialize($id).($from==''?($this->dodb['ttable']!=''?$this->dodb['ttable']:$this->dodb['table']):$from));
//		$ret = lc('cache')->get($ckey);
//		if($ret===false){
		if(is_array($id)){
			$this->select()->from($from)->fields()->where($id)->limit(1)->do_db();
			$ret = $this->db->fetch_array($this->last_result);
		}else{
			$this->select()->from($from)->fields()->where(array(array('field' => 'id', 'operator' => '=', 'value' => $id)))->limit(1)->do_db();
			$ret = $this->db->fetch_array($this->last_result);
		}
//			$timeout = 1;
//			lc('cache')->set($ckey, $ret, true, $timeout);	//uses the default compression and timeout
//		}
		$this->current = $ret;
		return $this;
	}
	public function get_record($where = NULL, $from = ''){
		if(is_array($where)){
//			$this->current = $this->select()->from($from)->fields()->where($where)->limit(1)->do_db()->db->fetch_array($this->last_result);
			$this->_get_info($where, $from);
		}elseif($from!=''){
			$this->_get_info($where, $from);
		}elseif($where!=NULL){
			$this->_get_info($where, $from);
		}elseif(!isset($this->current['id'])||$this->current['id']<=0){
			
		}
		return $this;
	}
	public function get($field = '', $default = NULL){
		$return = false;
		if($field==''||is_array($field)){
			$return = $this->get_info();
		}elseif(!isset($this->current[$field])){
			$this->get_info(); //to do in case the user override some functions
		}
		if($return===false){
			$return = isset($this->current[$field])?$this->current[$field]:$default;
		}
		return $return;
	}
	public function get_info($where = NULL, $from = NULL){
		if(!is_null($where)) $this->get_record($where, $from);
		return isset($this->current)&&is_array($this->current)?$this->current:array();
	}
	/**
	 * this function returns all the records
	 * using the current set select,table,order,etc...
	 * in other words a `do_db` and `get_raw` combined
	 */
	public function get_records(){
		$records = array();
		$this->do_db();
		$tmps	 = $this->last_result;
		$db		 = $this->db;
		while($record	 = $db->fetch_array($tmps)){
			$records[] = $record;
		}
		return $records;
	}
	public function affected_rows($read = false){
		return $this->db->affected_rows($read);
	}
	/**
	 * this function returns every records
	 * and pass it throu the get_info function
	 */
	public function get_all($where = array(), $orders = array(), $group = array(), $limit = '', $from = '', $join = array(), $xtrfields = array(), $extra_select = '', $having = array()){
		$records = array();
		$fields	 = (is_array($xtrfields)&&!empty($xtrfields))?$xtrfields:'`'.($from!=''?$from:$this->dodb['table']).'`.`id`';
		$this->select($extra_select)->fields($fields)->from($from)->qjoin($join)->where($where)->group($group)->having($having)->order($orders)->limit($limit)->do_db();
		$tmps	 = $this->last_result;
		$db		 = $this->db;
		while($record	 = $db->fetch_array($tmps)){
			if(is_array($fields)){
				$retrieve_fields = array();
				foreach($fields as $field){
					if(strrpos($field, '.')>0){
						$value = $record[substr($field, strrpos($field, '.')+1)];
					}else{
						$value = $record[$field];
					}
					$retrieve_fields[] = array('name' => $field, 'operator' => '=', 'value' => $value);
				}
			}else{
				$retrieve_fields = $record['id'];
			}
			$records[] = $this->get_record($retrieve_fields, $from)->get_info();
		}
		return $records;
	}
	/**
	 * this function returns every records
	 * and pass it throu the get_info function
	 * @param array where Where (Filter)
	 * @param array group Group By
	 * @param string from Table
	 * @param array join Join Array
	 * @param array xtrfield Extra Field To Select on
	 * @param string extra_select Extra Text To Append to the SELECT
	 * @param string extra_count Extra Text To Append to the COUNT(
	 */
	public function get_count($where = array(), $group = array(), $from = '', $join = array(), $xtrfields = array(), $extra_select = '', $extra_count = ''){
		if(strtoupper($extra_select)=='DISTINCT'){
			$extra_count = $extra_select;
		}
		if(is_array($xtrfields)&&!empty($xtrfields)){
			if(count($xtrfields)==1&&isset($xtrfields[0])){
				$field = 'COUNT('.$extra_count.' '.$xtrfields[0].') tot';
			}else{
				$field = 'COUNT('.$extra_count.' '.'CONCAT('.implode(',', $xtrfields).')) tot';
			}
		}elseif(is_string($xtrfields)&&$xtrfields!=''){
			$field = 'COUNT('.$extra_count.' '.$xtrfields.') tot';
		}else{
			$field = 'COUNT(*) tot';
		}
		$this->select()->fields($field)->from($from)->qjoin($join)->where($where)->group($group)->limit(1)->do_db();
		$tmps	 = $this->last_result;
		$record	 = $this->db->fetch_array($tmps);
		return intval($record['tot']);
	}
	/**
	 * this function returns ONLY what is in the records
	 * without pass the records from get_info
	 * @param array where Where (Filter)
	 * @param array order Order By
	 * @param array group Group By
	 * @param string limit Limit
	 * @param string from Table
	 * @param array join Join Array
	 * @param array xtrfield Extra Field To Select on
	 * @param string extra_select Extra Text To Append to the SELECT
	 */
	public function get_raw($where = array(), $orders = array(), $group = array(), $limit = '', $from = '', $join = array(), $xtrfields = array(), $extra_select = '', $having = array()){
		$records = array();
		$this->select($extra_select)->fields($xtrfields)->from($from)->qjoin($join)->where($where)->group($group)->having($having)->order($orders)->limit($limit)->do_db();
		$tmps	 = $this->last_result;
		$db		 = $this->db;
		while($record	 = $db->fetch_array($tmps)){
			$records[] = $record;
		}
		return $records;
	}
	/**
	 * this function returns ONLY the ID field
	 * @param array where Where (Filter)
	 * @param array order Order By
	 * @param array group Group By
	 * @param string limit Limit
	 * @param string from Table
	 * @param array join Join Array
	 * @param array xtrfield Extra Field To Select on
	 * @param string extra_select Extra Text To Append to the SELECT
	 */
	public function get_ids($where = array(), $orders = array(), $group = array(), $limit = '', $from = '', $join = array(), $xtrfields = array(), $extra_select = '', $having = array()){
		$records = array();
		$this->select($extra_select)->fields(array_merge($xtrfields, array('`'.($from!=''?$from:$this->dodb['table']).'`.`id`')))->from($from)->qjoin($join)->where($where)->group($group)->having($having)->order($orders)->limit($limit)->do_db();
		$tmps	 = $this->last_result;
		$db		 = $this->db;
		while($record	 = $db->fetch_array($tmps)){
			$records[] = $record;
		}
		return $records;
	}
	/**
	 * Returns the last Inserted ID for a query
	 */
	public function get_last_inserted_id(){
		return $this->db->insert_id();
	}
	private function _process_where($where){
		static $recur	 = 0;
		$return			 = false;
		$nl				 = "\n";
		$tab			 = "\t";
		if(!is_array($where)){
			$where = array(array('field' => 'id', 'operator' => '=', 'value' => $where));
		}elseif(count($where)==1&&isset($where[0])&&!is_array($where[0])){
			$where = array(array('field' => 'id', 'operator' => '=', 'value' => $where[0]));
		}elseif(!is_array($where)){
			$return = 1;
		}
		if($return===false){
			$recur++;
			$appends = array();
			foreach($where as $fil){
				if(!isset($fil['field'])&&!isset($fil['name'])){
					$appends[] = $nl.str_repeat($tab, $recur).$this->_process_where($fil);
				}else{
					$appends[] = $nl.str_repeat($tab, $recur).$this->_process_where_single($fil);
				}
			}
			$append	 = $nl.str_repeat($tab, $recur-1).'('.implode($recur==1?' AND ':' OR ', $appends).$nl.str_repeat($tab, $recur-1).')';
			$recur--;
			$return	 = $append;
		}
		return $return;
	}
	private function _process_where_single($where){
		//need to allow the field name to be called also "name" and not only "field"
		if((!isset($where['field'])&&!isset($where['name']))||!isset($where['operator'])){
			$return = '';
		}else{
			if(!isset($where['value'])){
				$where['value'] = '';
			}
			$field		 = isset($where['field'])?$where['field']:$where['name'];
			$operator	 = strtoupper($where['operator']);
			$value		 = $where['value'];
			$extra		 = isset($where['extra'])?$where['extra']:'';
			$escape		 = isset($where['escape'])?$where['escape']:'auto'; //this can be : auto (filter/value chooses if escape or not), true (always escapes), false (never escapes)
			$return		 = '';

			//let's create some backwards compatibility
			switch($operator){
				case 'F=':
				case 'F>':
				case 'F<':
				case 'F!=':
				case 'F>=':
				case 'F<=':
					//field base
					$operator	 = substr($operator, 1);
					$escape		 = false;
					break;
			}

			//fixing some non standard operators
			switch($operator){
				case '!=':
					$operator = '<>';
					break;
			}

			//let's prepare the $where['value']
			switch($operator){
				case 'AGAINST':
					$value = '(';
					if(is_array($where['value'])){
						$value .= $this->_escape_field($where['value'][0], $escape);
						$value .= ' '.$where['value'][1];
					}else{
						$value = $this->_escape_field($where['value'], $escape);
					}
					$value .= ')';
					break;
				case 'BETWEEN':
					if(!is_array($where['value'])){
						$where['value'] = array(0, 0);
					}else{
						if(!isset($where['value'][0])) $where['value'][0]	 = 0;
						if(!isset($where['value'][1])) $where['value'][1]	 = 0;
					}
					$value	 = $this->_escape_field($where['value'][0], $escape).' AND '.$this->_escape_field($where['value'][1], $escape);
					break;
				case 'NOT IN':
				case 'IN':
					$value	 = '(';
					if(is_array($where['value'])){
						foreach($where['value'] as $k => $v){
							$where['value'][$k] = $this->_escape_field($where['value'][$k], $escape);
						}
						$value .= implode(',', $where['value']);
					}else{
						$value .= $this->_escape_field($where['value'], $escape);
					}
					$value .= ')';
					break;
				case 'IS':
				case 'IS NOT':
					//exception to _escape_field because the "NULL" will not need to be escaped
					//so the default behavior is to NOT escape
					if($escape===false||$escape==='auto'){
						$value = $where['value'];
					}else{
						$value = '"'.$this->db->real_escape_string($where['value']).'"';
					}
					break;
				case 'FIND_IN_SET':
				case 'NOT FIND_IN_SET':
					$value	 = $this->_escape_field($where['value'], $escape);
//				if($escape === false){
//					$value = $where['value'];
//				}else{
//					$value = '"'.$this->db->real_escape_string($where['value']).'"';
//				}
					$return	 = $operator.'('.$value.','.$field.')';
					break;
				//all of those are falling into the default
				/*
				  case 'REGEXP':
				  case 'LIKE':
				  case '=':
				  case '!=':
				  case '>':
				  case '<':
				  case '=>':
				  case '=<':
				 */
				default:
					$value	 = $this->_escape_field($where['value'], $escape);
					break;
			}
			if($return==''){
				$return = $field.' '.$operator.' '.$value.' '.$extra;
			}
		}
		return $return;
	}
	private function _escape_field($value, $escape){
		if($escape===false||($escape==='auto'&&is_numeric($value)&&!is_string($value))){
			//nothing to do
		}else{
			$value = '"'.$this->db->real_escape_string($value).'"';
		}
		return $value;
	}
	/*	 * *******************************************
	 *  MANAGEMENT CONNECTION AND DISPLAY PART
	 * ****************************************** */
	public function get_related_tables(){
		return $this->related;
	}
	public function get_bulk_actions(){
		$this->bulk_actions = array();
		return $this->bulk_actions;
	}
	public function get_fields(){
//		$this->fields = array();
		return $this->fields;
	}
	private function _get_data($action, $config = NULL){
		$return				 = array();
		$return['errors']	 = array();
		$return['data']		 = array();
		if(!is_null($config)){
			$_config = lc('config')->get_and_unload_config($config);
			foreach($_config as $k => $conf){
				if($k!=='id'){
					$required	 = isset($conf['form']['required'])?(is_array($conf['form']['required'])?(intval($conf['form']['required'][$action])):intval($conf['form']['required'])):false;
					$use_default = isset($conf['form']['default'])?$conf['form']['default']:false;
					$default	 = '';
					if($use_default!==true&&$use_default!==false){
						$default = $use_default;
					}
					$value = trim(lc('uri')->post($k, $default));
					if($required&&($value=='')){
						$return['errors'][] = "The ".$conf['display']." field is required.";
					}else{
						if($use_default!==true){
							$return['data'][$k] = $this->db->real_escape_string($value);
						}
					}
				}
			}
		}
		return $return;
	}
	public function add($config = NULL){
		$data_errors = $this->_get_data('add', $config);
		$data		 = $data_errors['data'];
		$return		 = $data_errors['errors'];
		if(empty($return)){
			$return = false;
			if(is_array($data)&&!empty($data)){
				foreach($data as $key => $value){
					$this->set($key, $value);
				}
				$this->insert()->do_db();
				$return = $this->get_last_inserted_id();
			}
		}
		return $return;
	}
	public function edit($id, $config = NULL){
		$return = false;
		if($id>0){
			$data_errors = $this->_get_data('edit', $config);
			$data		 = $data_errors['data'];
			$return		 = $data_errors['errors'];
			if(empty($return)){
				$return	 = false;
				$return	 = $this->edit_related($config, $id);
				if(is_array($data)&&!empty($data)){
					foreach($data as $key => $value){
						if($key=='content'&&$config=='page'){
							$value = str_ireplace('\r\n', '', $value);
						}
						$this->set($key, $value);
					}
					$filters[]	 = array('field' => 'id', 'operator' => '=', 'value' => $id);
					$return		 = $this->update()->where($filters)->do_db();
				}
			}
		}
		return $return;
	}
	public function edit_related($table, $id){
		$related_table = $this->get_related_tables();
		foreach($related_table as $related){
			$serialized_table_data	 = lc('uri')->post('related_table_'.$related, '');
			$filters				 = array();
			$filters[]				 = array('field' => $table.'_id', 'operator' => '=', 'value' => $id);
			$existing_data			 = $this->get_all($filters, array(), array(), '', $related, array());
			$data					 = array();
			parse_str($serialized_table_data, $data);
			if(!is_array($data)){
				$data = array();
			}
			$doit = true;
			if(empty($data)&&!empty($existing_data)){
				$filters	 = array();
				$filters[]	 = array('field' => $table.'_id', 'operator' => '=', 'value' => $id);
				$this->delete()->from($related)->where($filters)->run();
				//delete all data for this table
				$doit		 = false;
			}elseif(empty($data)&&!empty($existing_data)){
				$doit = false;
			}
			if($doit){
				$new_data	 = array();
				$field_name	 = '';
				foreach($data as $k => $values){
					$field_name = $k;
					foreach($values as $value){
						$new_data[$k][] = $value;
					}
				}
				$delete_rows = array();
				foreach($existing_data as $existing){
					$row_id = $existing['id'];
					if(!in_array($existing[$field_name], $new_data[$field_name])){
						$delete_rows[] = $row_id;
					}
				}
				foreach($new_data as $data){
					foreach($data as $k => $value){
						foreach($existing_data as $existing){
							if($existing[$field_name]==$value){
								unset($new_data[$field_name][$k]);
							}
						}
					}
				}
				foreach($new_data as $field_name => $data){
					foreach($data as $value){
						$this->set($field_name, $value);
						$this->set($table.'_id', $id);
						$this->insert('IGNORE')->into($related)->run();
					}
				}
				foreach($delete_rows as $delete_row){
					$filters	 = array();
					$filters[]	 = array('field' => 'id', 'operator' => '=', 'value' => $delete_row);
					$this->delete()->from($related)->where($filters)->run();
				}
			}
		}
	}
	public function edit_bulk($tt_post){
		$ret = false;
		if(is_array($tt_post)){
			$fields = $this->get_fields();
			foreach($tt_post as $key => $value){
				if(isset($fields[$key])&&is_array($value)&&$fields[$key]['post']==true){
					foreach($value as $id => $vv){
						$filters = array(array('field' => 'id', 'operator' => '=', 'value' => $id));
						$ret	 = $this->update()->set($key, $vv)->where($filters)->do_db();
					}
				}
			}
		}else{
			$ret = false;
		}
		return $ret;
	}
	public function remove($id){
		$filters	 = array();
		$filters[]	 = array('field' => 'id', 'operator' => '=', 'value' => $id);
		return $this->delete()->where($filters)->do_db();
	}
	public function change($field, $value = NULL, $id = NULL, $from = ''){
		if(is_null($id)){
			$id = $this->get('id', 0);
		}
		if(!empty($id)&&!empty($field)){
			if(!is_array($id)){
				$filters	 = array();
				$filters[]	 = array('field' => 'id', 'operator' => '=', 'value' => $id);
			}else{
				$filters = $id;
			}
			$ret = $this->update()->from($from)->set($field, $value)->where($filters)->do_db();
		}else{
			$ret = false;
		}
		return $ret;
	}
	public function preview_query(){
		return $this->_create_query();
	}
	public function get_query(){
		return $this->last_query;
	}
	private function debug($msg){
		if($this->level_debug!==false){
			echo $msg.'::'.$this->db->get_total_time()."<br />\n";
		}
	}
	public function __get($name){
		if($name=='db'){
			$return = $this->db;
		}else{
			$return = NULL;
		}
		return $return;
	}
}
