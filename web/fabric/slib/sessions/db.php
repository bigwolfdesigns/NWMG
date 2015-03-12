<?php

//THIS CAN BE DONE MUCH BETTER... but for now it works
class sessions_db {
	private $savePath;
	protected $lib	 = NULL;
	private $alive	 = true;
	public function __destruct(){
		if($this->alive){
			session_write_close();
			$this->alive = false;
		}
	}
	public function open($savePath, $sessionName){
		$config		 = lc('config')->load('db');
		$this->lib	 = mysql_connect($config->get('db_server'), $config->get('db_user'), $config->get('db_password')) OR die('Could not connect to database.');
		mysql_select_db($config->get('db_database'));
		return true;
	}
	public function close(){
		return mysql_close($this->lib);
	}
	public function read($id){
		$q	 = "SELECT `content` FROM `session` WHERE `id` = '".mysql_real_escape_string($id)."' LIMIT 1";
		$r	 = mysql_query($q);
		if(mysql_num_rows($r) == 1){
			$fields = mysql_fetch_assoc($r);
			if(isset($fields['content'])){
				return $fields['content'];
			}else{
				return '';
			}
		}else{
			return '';
		}
		/*
		  $filters	 = array();
		  $filters[]	 = array('field'=>'id', 'operator'=>'=', 'value'=>$id);
		  $filters[]	 = array('field'=>'active', 'operator'=>'=', 'value'=>'y');
		  $tmp		 = ->get_info($filters, 'session');
		  if(is_array($tmp) && isset($tmp['content'])){
		  $return = (string)$tmp['content'];
		  }else{
		  $return = '';
		  }
		  return $return;
		 */
	}
	public function write($id, $data){
		if(trim($data) != ''){
			$q = "REPLACE INTO `session` (`id`, `content`) VALUES ('".mysql_real_escape_string($id)."', '".mysql_real_escape_string($data)."')";
		}else{
			$q = "DELETE FROM `session` WHERE `id` = '".mysql_real_escape_string($id)."'";
		}
		$r = mysql_query($q);
		return mysql_affected_rows($r);
		/*
		  if($id != ''){
		  ->insert()
		  ->into('session')
		  ->set('id', $id)
		  ->set('content', $data)
		  ->set('date_last_modified', 'NOW()', true)
		  ->set('active', 'y')
		  ->on_duplicate_key_update(array('content'=>'VALUES(content)', 'date_last_modified'=>'VALUES(date_last_modified)', 'active'=>'VALUES(active)'))
		  ->do_db()
		  ;
		  }
		  return true;
		 */
	}
	public function destroy($id){
		$q			 = "DELETE FROM `session` WHERE `id` = '".mysql_real_escape_string($id)."'";
		$r			 = mysql_query($q);
		$_SESSION	 = array();
		return mysql_affected_rows($r);
		/*
		  $filters	 = array();
		  $filters[]	 = array('field'=>'id', 'operator'=>'=', 'value'=>$id);
		  //		$filters[]	 = array('field'=>'active', 'operator'=>'=', 'value'=>'y');
		  //		->update()
		  //				->table('session')
		  //				->set('active', 'n')
		  //				->where($filters)
		  //				->do_db()
		  //				;
		  ->delete()
		  ->from('session')
		  ->where($filters)
		  ->do_db()
		  ;
		  return true;
		 */
	}
	public function gc($maxlifetime){
		$q	 = "DELETE FROM `session` WHERE DATE_ADD(`date_last_modified`, INTERVAL ".(int)$maxlifetime." SECOND) < NOW()";
		$r	 = mysql_query($q);

		return mysql_affected_rows($r);
		/*
		  $filters	 = array();
		  $filters[]	 = array('field'=>'date_last_modified', 'operator'=>'<', 'value'=>date('Y-m-d H:i:s', time() - $maxlifetime));
		  //		$filters[]	 = array('field'=>'active', 'operator'=>'=', 'value'=>'y');
		  //		->update()
		  //				->table('session')
		  //				->set('active', 'n')
		  //				->where($filters)
		  //				->do_db()
		  //		;
		  ->delete()
		  ->from('session')
		  ->where($filters)
		  ->do_db()
		  ;
		  return true;
		 *
		 */
	}
}
