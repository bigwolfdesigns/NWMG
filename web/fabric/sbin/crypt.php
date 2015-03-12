<?php

//very dimple class to cript numbers and strings
class crypt{
	private $_crypt_map = array();
	public function __construct(){
		$_cfg = lc('config')->get_and_unload_config('crypt');
		if(isset($_cfg['salt'])){
			$this->set_crypt_map($_cfg['salt']);
		}
	}
	public function crypt_id($id, $key = 0){
		if($key == 0){
			$key = trim(microtime(true));
		}
		$key = intval($key);
		if(strlen($key) <= 3) $key +=999;
		if(substr($key, -1) == 0) $key +=3;
		$key = substr($key, -3);
		$kid = ((($id.substr($key, 0, 1)) + substr($key, 1, 1)) * substr($key, -1)).$key;
		return $this->_crypt_map_kid($kid);
	}
	public function decrypt_id($kid){
		if(trim($kid) == '') return false;
		$kid = $this->_crypt_map_kid($kid, true);
		$key = substr($kid, -3);
		$id	 = substr($kid, 0, -3);
		$id	 = @substr((($id / substr($key, -1)) - substr($key, 1, 1)), 0, -1);
		return $id;
	}
	public function crypt_str($str){
		$this->set_crypt_map();
		$key	 = implode('', $this->_crypt_map);
		$result	 = '';
		for($i = 0; $i < strlen($str); $i++){
			$char	 = substr($str, $i, 1);
			$keychar = substr($key, ($i % strlen($key)) - 1, 1);
			$char	 = chr(ord($char) + ord($keychar));
			$result .= $char;
		}
		return base64_encode($result);
	}
	public function decrypt_str($str){
		$this->set_crypt_map();
		$key	 = implode('', $this->_crypt_map);
		$str	 = base64_decode($str);
		$result	 = '';
		for($i = 0; $i < strlen($str); $i++){
			$char	 = substr($str, $i, 1);
			$keychar = substr($key, ($i % strlen($key)) - 1, 1);
			$char	 = chr(ord($char) - ord($keychar));
			$result .= $char;
		}
		return $result;
	}
	private function _crypt_map_kid($kid, $umap = false){
		$this->set_crypt_map();
		$ret = '';
		for($i = 0; $i < strlen($kid); $i++){
			if($umap){
				$ret .= array_search(substr($kid, $i, 1), $this->_crypt_map);
			}else{
				$ret .= $this->_crypt_map[substr($kid, $i, 1)];
			}
		}
		return $ret;
	}
	public function set_crypt_map($map = NULL){
		$lenght = 10;
		if(is_null($map) && isset($this->_crypt_map) && is_array($this->_crypt_map) && count($this->_crypt_map) == $lenght){

		}else{
			if(is_string($map)){
				$map = trim($map);
			}
			if(!is_string($map) && $map == ''){
				if(isset($_SERVER['HTTP_HOST'])){
					$host = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'';
					if(strrpos($host, ':') > 0){
						$host = substr($host, 0, strrpos($host, ':'));
					}
					if(substr_count($host, '.') > 1){
						$host = substr($host, strpos($host, '.'));
					}
					$map = preg_replace('/[^A-Z0-9]/', '', strtoupper($host));
				}else{
					$map = '';
				}
			}
			$map .= 'ABCDEFGHIJKLMNOPQRSTUVWYXZ'; //just in case is not long enough
			if(is_string($map) && strlen($map) >= $lenght){
				$tmap	 = $map;
				$map	 = array();
				$k		 = 0;
				for($i = 0; $i <= strlen($tmap) && $k < $lenght; $i++){
					$s = substr($tmap, $i, 1);
					if(!in_array($s, $map)){
						$map[$k] = $s;
						$k++;
					}
				}
			}
			//the next 3 lines should never happen
			if(!is_array($map) || count($map) != $lenght){
				$map = array('B', 'I', 'V', 'U', 'C', 'O', 'M', 'F', 'A', 'B');
			}
			$this->_crypt_map = $map;
		}
		return $this->_crypt_map;
	}
}