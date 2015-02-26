<?php

//THIS CAN BE DONE MUCH BETTER... but for now it works
class sessions_db{
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
		$this->lib	 = new MYSQLi($config->get('db_server'), $config->get('db_user'), $config->get('db_password'), $config->get('db_database')) OR die('Could not connect to database.');
		return true;
	}
	public function close(){
		return $this->lib->close();
	}
	public function read($id){
		$q	 = "SELECT `content` FROM `session` WHERE `id` = '".$this->lib->real_escape_string($id)."' LIMIT 1";
		$r	 = $this->lib->query($q);
		if($r->num_rows == 1){
			$fields = $r->fetch_assoc();
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
		  $tmp		 = $this->lib->get_info($filters, 'session');
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
			$q = "REPLACE INTO `session` (`id`, `content`) VALUES ('".$this->lib->real_escape_string($id)."', '".$this->lib->real_escape_string($data)."')";
		}else{
			$q = "DELETE FROM `session` WHERE `id` = '".$this->lib->real_escape_string($id)."'";
		}
		$this->lib->query($q);
		return $this->lib->affected_rows;
		/*
		  if($id != ''){
		  $this->lib->insert()
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
		$q			 = "DELETE FROM `session` WHERE `id` = '".$this->lib->real_escape_string($id)."'";
		$this->lib->query($q);
		$_SESSION	 = array();
		return $this->lib->affected_rows;
		/*
		  $filters	 = array();
		  $filters[]	 = array('field'=>'id', 'operator'=>'=', 'value'=>$id);
		  //		$filters[]	 = array('field'=>'active', 'operator'=>'=', 'value'=>'y');
		  //		$this->lib->update()
		  //				->table('session')
		  //				->set('active', 'n')
		  //				->where($filters)
		  //				->do_db()
		  //				;
		  $this->lib->delete()
		  ->from('session')
		  ->where($filters)
		  ->do_db()
		  ;
		  return true;
		 */
	}
	public function gc($maxlifetime){
		$q = "DELETE FROM `session` WHERE DATE_ADD(`date_last_modified`, INTERVAL ".(int)$maxlifetime." SECOND) < NOW()";
		$this->lib->query($q);

		return $this->lib->affected_rows;
		/*
		  $filters	 = array();
		  $filters[]	 = array('field'=>'date_last_modified', 'operator'=>'<', 'value'=>date('Y-m-d H:i:s', time() - $maxlifetime));
		  //		$filters[]	 = array('field'=>'active', 'operator'=>'=', 'value'=>'y');
		  //		$this->lib->update()
		  //				->table('session')
		  //				->set('active', 'n')
		  //				->where($filters)
		  //				->do_db()
		  //		;
		  $this->lib->delete()
		  ->from('session')
		  ->where($filters)
		  ->do_db()
		  ;
		  return true;
		 *
		 */
	}
}