<?php

//THIS CAN BE DONE MUCH BETTER... but for now it works
class sessions_db {
	protected $lib	 = NULL;
	private $alive	 = true;
	public function __destruct(){
		if($this->alive){
			session_write_close();
			$this->alive = false;
		}
	}
	public function open($savePath, $sessionName){
		$this->lib = ll('table_prototype')->set_table_name('session');
		return true;
	}
	public function close(){
		//No need to close database connections
		//db will take care of that
//		return $this->lib->close();
	}
	public function read($id){
		$return		 = '';
		$filters	 = array();
		$filters[]	 = array('field' => 'id', 'operator' => '=', 'value' => $id);
		$tmp		 = $this->lib->get_info($filters, 'session');
		if(is_array($tmp) && isset($tmp['content'])){
			$return = (string)$tmp['content'];
		}
		return $return;
	}
	public function write($id, $data){
		if(trim($data) != ''){
			$return = $this->lib
					->replace()
					->into('session')
					->set('id', $id)
					->set('content', $data)
					->set('date_last_modified', date('Y-m-d H:i:s'))
					->run()
					->affected_rows();
		}else{
			$return = $this->destroy($id);
		}
		return $return;
	}
	public function destroy($id){
		$filters	 = array();
		$filters[]	 = array('field' => 'id', 'operator' => '=', 'value' => $id);
		$return		 = $this->lib
				->delete()
				->from('session')
				->where($filters)
				->run()
				->affected_rows();
		$_SESSION	 = array();
		return $return;
	}
	public function gc($maxlifetime){
		$filters	 = array();
		$filters[]	 = array('field' => 'date_last_modified', 'operator' => '<', 'value' => date('Y-m-d H:i:s', time() - $maxlifetime));
		$return		 = $this->lib
				->delete()
				->from('session')
				->where($filters)
				->run()
				->affected_rows();
		return $return;
	}
}