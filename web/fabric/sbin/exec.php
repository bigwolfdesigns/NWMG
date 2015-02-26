<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
class exec {
	protected static $instance;
	protected $cfg	= array();
	protected $cmd	= false;
	protected $cmds	= array();

	public function __construct(){
		self::$instance =&$this;
		$this->cfg = lc('config')->get_and_unload_config('exec');
	}
	public function is_running($cmd){
		$key = 'exec_'.md5($cmd);
		$ret = lc('cache')->get($key, array('concurrency'=>0, 'timeout'=>array()));
		return ($ret['concurrency']>=$this->cfg['concurrent']) && $this->cfg['concurrent']>0;
	}
	public function _start_exec($cmd){
		$cmd_found 	= false;
		$return		= true;
		if($this->cfg['timeout']>0){
			while($this->is_running($cmd)){
				// command was found running, so we'll not have another instance running for it.
				$cmd_found = true;
				// wait for the existing command to finish.
				usleep(12500);
			}

			// if command was not found, we can run new process now.
			if($cmd_found===false){
				$key	= 'exec_'.md5($cmd);
				$ret	= lc('cache')->get($key, array('concurrency'=>0, 'timeout'=>array()));
				$ret['concurrency']++;
				$ret['timeout'][] = time();
				lc('cache')->set($key, $ret, false, $this->cfg['timeout']);

				// we are ready for executing new process for the command.
				$return = true;
			}else{
				// If command was found and finished, we should lookup for the result
				$result_key	= 'exec_'.md5($cmd).'_result';
				$tmps		= lc('cache')->get($result_key, '');
				if(isset($tmps['result'])){
					$return = $tmps['result'];
				}
			}
		}

		// if timeout is not set, we have to run the command anyway.
		return $return;
	}
	public function _end_exec($cmd, $result = ''){
		if($this->cfg['timeout']>0){
			//assume that the old one ended
			$key	= 'exec_'.md5($cmd);
			// store results in cache for any waiting command to fetch
			if($result!=''){
				$store		= array('cmd'=>$cmd, 'result'=>$result);
				$result_key	= $key . '_result';
				lc('cache')->set($result_key, $store, false, $this->cfg['timeout_result']);
			}

			// Now reduce concurrency value stored in cache
			$ret	= lc('cache')->get($key, array('concurrency'=>0, 'timeout'=>array()));
			if($ret['concurrency']>1){
				$ret['concurrency']--;
				array_shift($ret['timeout']);
				lc('cache')->set($key, $ret, false, $this->cfg['timeout']);
			}elseif($ret['concurrency']>0){
				lc('cache')->delete($key);
			}
		}
	}
	public function exec($cmd, $background = false){
		$cmd_status = $this->_start_exec($cmd);
		$return	= '';
		if($cmd_status!==true){
			// no need to run the command. Output should already be available.
			$return = $cmd_status;
		}else{
			$from	= array();
			$to		= array();
			foreach($this->cfg['bin'] as $k=>$v){
				$from[]	= '#'.$k.'#';
				$to[]	= $v;
			}
			// storing it for later use to syncronize and ending for running command
			$orig_cmd	= $cmd;
			$cmd		= $this->cfg['prepend'].str_replace($from, $to, $cmd).$this->cfg['append'];

//			$return	= `$cmd`;
			if (substr(PHP_OS, 0, 3) == 'WIN'){
				$cmd	= 'start '.($background?'/b':'').' "' . $cmd.'"';
				$proc	= popen($cmd, 'r');
			}else{
				foreach($this->cfg['ulimit']['limits'] as $limit=>$value){
					$cmd = $this->cfg['ulimit']['bin'].' -'.$limit.' '.$value.' ; '.$cmd;
				}
				$cmd	= '( '.$cmd . ' ) '.($background?'&':'');
				$proc	= popen($cmd, 'r');
			}
			$this->cmd		= $cmd;
			$this->cmds[]	= $cmd;
			if(!$background){
				while(!feof($proc)){
					$return .= fread($proc, 1024);
					usleep(1000);
				}
			}
			pclose($proc);	//I don't care about the output
			$this->_end_exec($orig_cmd, $return);
		}
   		return $return;
	}
	public function cmd($all=false){
		if($all){
			return $this->cmds;
		} else {
			return $this->cmd;
		}
	}
}
?>