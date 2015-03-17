<?php
class output{
	protected static $instance	 = NULL;
	private $display			 = NULL;
	private $download			 = false;
	private $is_file			 = false;
	public static function &get_instance(){
		if(is_null(self::$instance)){
			self::$instance = new output();
		}
		return self::$instance;
	}
	public function __construct(){
		self::$instance	 = &$this;
		$this->config	 = lc('config')->get_and_unload_config('output');
	}
	public function set_display($display = NULL){
		$this->display = $display;
		return $this;
	}
	public function get_display(){
		if(is_null($this->display)){
			$display = ll('display');
		}else{
			$display = $this->display;
		}
		return $display;
	}
	public function set_download($download){
		$this->download = (bool)$download;
		return $this;
	}
	public function set_is_file($is_file){
		$this->is_file = (bool)$is_file;
		return $this;
	}
	public function xml($xml = array()){
		if(!is_array($xml)&&count($xml)>0&&!$this->is_file){
			$xml = array('error' => true);
		}
		if(count($xml)>1){
			$xml = array('doc' => $xml);
		}
		$this->send_header('text/xml', 'xml');
		$this->get_display()->assign('xml', $xml);
		$this->send_file('output/xml', $xml);
		return $this;
	}
	public function csv($csv = array()){
		if(!is_array($csv)&&count($csv)>0&&!$this->is_file){
			$csv = array('error' => true);
		}
		$this->send_header('application/csv', 'csv');
		$this->get_display()->assign('report_data', $csv);
		$this->send_file('output/csv', $csv);
		return $this;
	}
	public function json($json = array()){
		if(!is_array($json)&&count($json)>0&&!$this->is_file){
			$json = array('error' => true);
		}
		$this->send_header('application/json', 'json');
		$this->get_display()->assign('json', $json);
		$this->send_file('output/json', $json);
		return $this;
	}
	public function jsonp($callback, $json = array()){
		if(!is_array($json)&&count($json)>0&&!$this->is_file){
			$json = array('error' => true);
		}
		$this->send_header('application/x-javascript', 'json');
		$this->get_display()->assign('json', $json)->assign('callback', $callback);
		$this->send_file('output/jsonp', $json);
		return $this;
	}
	public function css($css = array()){
		if(!is_array($css)&&count($css)>0&&!$this->is_file){
			$css = array('error' => true);
		}
		$this->send_header('text/css', 'css');
		$this->get_display()->assign('css', $css);
		$this->send_file('output/css', $css);
		return $this;
	}
	public function debug(){
		if(isset($_SERVER['REMOTE_ADDR'])){
			if(in_array(trim($_SERVER['REMOTE_ADDR']), $this->config['debug_ips'])){
				$args	 = func_get_args();
				$die	 = false;
				$run	 = true;
				foreach($args as $var){
					//any special commands ?
					if(is_string($var)){
						if(substr($var, 0, 3)==='IF:') $run = (bool)substr($var, 3);
						if($var==='_DIE') $die = true;
					}
				}
				if($run){
					echo '<pre style="margin:0;">';
					echo 'Time: '.date('Y-m-d H:i:s'), ' ('.time().")\n";
					foreach($args as $var){
						var_dump($var);
					}
					echo '</pre>';
					if($die){
						exit();
					}
				}
			}
		}
		return $this;
	}
	private function send_header($type, $ext = ''){
		if($this->download){
			$this->get_display()->add_header('content-description', 'File Transfer');
			$this->get_display()->add_header('content-disposition', 'attachment; filename=report_'.$this->get_display()->get('report').'.'.$ext);
		}
		$this->get_display()->add_header('content-type', $type);
	}
	private function send_file($template, $files){
		$this->get_display()->set_hide_show('head', false);
		$this->get_display()->set_hide_show('foot', false);
		if($this->is_file){
			if(!is_array($files)){
				$files = array($files);
			}
			foreach($files as $file){
				if(file_exists($file)){
					$this->get_display()->_process_headers();
					readfile($file);
				}else{
					//quietly continue
//					echo 'Impossible to read '.$file;
				}
			}
		}else{
			$this->get_display()->show($template);
		}
	}
}