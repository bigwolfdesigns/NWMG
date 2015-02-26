<?php
/***********************************\
Fabrizio Parrella
updated on: 06/23/2008 - v.1.0		- initial release


this class is one of the many classes to access the Postgress database.

this class will send an email everytime that there is an error with some query, in this way
will be simpler to monitor the site and to see if there is any kind of problem
with POSTRGRESS/query

\***********************************/
class db_pgsql extends db{
	public $database = '';

	protected $link_id  = 0;
	protected $query_id = 0;
	protected $record   = array();

	public $errdesc		= '';
	public $errno		= 0;
	public $show_error	= 0;
	public $die_error	= 1;
	public $trigger_error = 1;
	public $strip_comment = 1;

	public $server		= '';
	public $user		= '';
	public $password	= '';

	public $log			= 0;	//abilita il file log
	public $filelog		= '';	//nome del file log

	public $appname		= 'DataBase Managment';
	public $admin_email	= '';

	public $debug		= 0;
	protected $total_time = 0;
	public $db_accesses	= 0;

	protected $substitute = array();
	protected $default_result_type;

	public $SQL_CACHE	= 1;				//if = 1, will add SQL_CACHE to all the select queries
	public $connect_first_use = false;		//if = true, will connect only when the first query is executed

	protected $in_trans = false;	//if I am into a transaction
	private $timezone = '';

	public function set_variables($vars = array()){
		//this function can be used set all the variables for this class at once
		foreach($vars as $key=>$value){
			if(isset($this->$key)){
				$this->$key = $value;
			}
		}
	}

	function __construct(){
		parent::$instance =&$this;
		$this->substitute['what']	= '';
		$this->substitute['with']	= '';
//		$this->default_result_type	= PGSQL_ASSOC;
//		$this->default_result_type	= PGSQL_NUM;
		$this->default_result_type	= PGSQL_BOTH;		//pgsql default
	}

	function set_default_result_type($result_type){
		if(
			$result_type == PGSQL_ASSOC ||
			$result_type == PGSQL_NUM ||
			$result_type == PGSQL_BOTH
		   ){
			$this->default_result_type	= $result_type;
		}
	}

	function substitute($what, $with){
		$this->substitute['what']	= $what;
		$this->substitute['with']	= $with;
	}
	function connect($persistant = false, $first_use = false) {
		$this->persistant = $persistant;
		if(!$this->connect_first_use || $first_use){
			$time1=$this->getmicrotime();
			if ( 0 == $this->link_id ) {
				if($persistant){
					$this->link_id=@pg_pconnect('host='.$this->server.' dbname='.$this->database.' user='.$this->user.' password='.$this->password);
				} else {
					$this->link_id=@pg_connect('host='.$this->server.' dbname='.$this->database.' user='.$this->user.' password='.$this->password);
				}
				if (!$this->link_id) {
					$this->print_error('Link-ID == false, connect failed');
				}
			}
//			if($this->timezone!=''){
//				pgsql_query('SET time_zone = "'.$this->timezone.'";', $this->link_id) or die('Error While Setting PgSQL Timezone '.$this->timezone.' !<br />'.pgsql_error() );
//			}
			$time2=$this->getmicrotime();
			unset($this->server,$this->user,$this->password);
			$this->total_time += $time2-$time1;
		}
  	}
	function pconnect() {
		$this->connect(true);
	}

	function is_connected(){
		return (bool)$this->link_id;
	}

	function geterrdesc() {
		$this->error=pgsql_error();
		return $this->error;
  	}

  	function geterrno() {
		$this->errno=0;
		return $this->errno;
  	}

  	function select_db($database='') {
		//in postgress each connection has its own db
		return true;
  	}

  	function query($query_string) {
		if(!$this->is_connected() && $this->connect_first_use){
			//first connection, I try to connect
			$this->connect($this->persistant,true);
		}
		if($this->link_id){
			$time1=$this->getmicrotime();
			$this->db_accesses++;
			$query_string = trim($query_string);

			if($this->substitute['what'] != '' && $this->substitute['with'] != ''){
				$query_string = str_replace($this->substitute['what'],$this->substitute['with'],$query_string);
			}

			if($this->SQL_CACHE>0 && strtoupper(substr($query_string,0,6)) == 'SELECT'){
				if($this->SQL_CACHE==1 && !preg_match('/^SELECT[\s+]SQL_NO_CACHE/i', $query_string)){
					//add cache
					$query_string = preg_replace('/^SELECT[\s+]SQL_CACHE/i','SELECT ',$query_string);
					$query_string = preg_replace('/^SELECT[\s+]/i','SELECT SQL_CACHE ',$query_string);
				} elseif($this->SQL_CACHE==2){
					//remove cache
					$query_string = preg_replace('/^SELECT[\s+]SQL_CACHE/i','SELECT ',$query_string);
					$query_string = preg_replace('/^SELECT[\s+]SQL_NO_CACHE/i','SELECT ',$query_string);
				}
			}
			if($this->strip_comment==1){
				//remove comments
				$query_string = preg_replace('@/\*[^\*].*?\*/@s','',$query_string);
			}

			$this->dbug('<font size="1">'.$this->db_accesses.' - DATABASE ACCESS<br/>'.$query_string);

			$this->query_id = pg_query($this->link_id, $query_string);
			$this->write_log($query_string);	//save the query log
			$time2=$this->getmicrotime();
			$this->dbug('('.($time2-$time1).')</font><br />');
			$this->total_time += $time2-$time1;
			if (!$this->query_id || pg_last_error($this->link_id)!='') {
				$this->print_error('QUERY SQL not valid: '.$query_string);
				if($this->in_trans){
					$this->rollback_transaction();
				}
			}
			return $this->query_id;
		} else {
			return false;
		}
  	}

  	function query_first($query_string, $result_type=NULL) {
		if($this->query($query_string)!=false){			//call the query function
			$returnarray=$this->fetch_array($this->query_id, 0, $result_type);
			$this->free_result($this->query_id);
			return $returnarray;
		} else {
			return false;
		}
  	}

	function data_seek($query_id=-1,$pos=-1){
		$time1=$this->getmicrotime();
		if ($query_id!=-1) {
			$this->query_id=$query_id;
		}
		if($this->query_id){
			if($this->num_rows($this->query_id)>0){
				if($pos==-1){
					//a random position
					$pos = rand(1,$this->num_rows($this->query_id))-1;
				}
				$return = pg_result_seek($this->query_id,$pos);
			} else {
				$return = false;
			}
		}else{
			$return = false;
		}

		$time2=$this->getmicrotime();
		$this->total_time += $time2-$time1;
		return $return;
	}

  	function fetch_array($query_id=-1, $result_type=NULL) {
		$time1=$this->getmicrotime();
		if ($query_id!=-1) {
			$this->query_id=$query_id;
		}
		if($result_type==NULL)	$result_type = $this->default_result_type;
		if($this->query_id){
			$this->record = pg_fetch_array($this->query_id, NULL, $result_type);
		}else{
			$this->record = false;
		}

		$time2=$this->getmicrotime();
		$this->total_time += $time2-$time1;
		return $this->record;
  	}

	function fetch_object($query_id=-1) {
		$time1=$this->getmicrotime();
		/*
		thanks to:
		Thomas Bruhin - mailto:thomas@mediasonics.ch
		Media Sonics - http://www.mediasonics.ch
		*/
		if ($query_id!=-1) {
			$this->query_id=$query_id;
		}

		if($this->query_id){
			$this->record = pg_fetch_object($this->query_id);
		}else{
			$this->record = false;
		}

		$time2=$this->getmicrotime();
		$this->total_time += $time2-$time1;
		return $this->record;
	}
	function fetch_row($query_id=-1) {
		$time1=$this->getmicrotime();
		if ($query_id!=-1) {
			$this->query_id=$query_id;
		}

		if($this->query_id){
			$this->record = pg_fetch_row($this->query_id);
		}else{
			$this->record = false;
		}

		$time2=$this->getmicrotime();
		$this->total_time += $time2-$time1;
		return $this->record;
	}

	function fetch_field($query_id=-1, $field_offset=0) {
		return false;
	}
	function num_fields($query_id=-1) {
		$time1=$this->getmicrotime();
		if ($query_id!=-1) {
			$this->query_id=$query_id;
		}

		if($this->query_id){
			$this->record = pg_num_fields($this->query_id);
		}else{
			$this->record = 0;
		}

		$time2=$this->getmicrotime();
		$this->total_time += $time2-$time1;
		return $this->record;
	}

	function start_transaction($query = 'START TRANSACTION;'){
		$this->query('SET AUTOCOMMIT=0;');
		$this->query($query);
		$this->in_trans = true;
	}

	function rollback_transaction($query = 'ROLLBACK;'){
		if($this->in_trans){
			$this->query($query);
			$this->query('SET AUTOCOMMIT=1;');
			$this->in_trans = false;
		}
	}

	function commit_transaction($query = 'COMMIT;'){
		if($this->in_trans){
			$this->query($query);
			$this->query('SET AUTOCOMMIT=1;');
			$this->in_trans = false;
		}
	}

  	function free_result($query_id=-1) {
		$time1=$this->getmicrotime();
		if ($query_id!=-1) {
			$this->query_id=$query_id;
		}
		$return = pg_free_result($this->query_id);
		$time2=$this->getmicrotime();
		$this->total_time += $time2-$time1;
		return $return;

  	}

  	function num_rows($query_id=-1) {
		$time1=$this->getmicrotime();
		if ($query_id!=-1) {
			$this->query_id=$query_id;
		}
		$return = pg_num_rows($this->query_id);
		$time2=$this->getmicrotime();
		$this->total_time += $time2-$time1;
		return $return;
  	}

	function affected_rows() {
		$time1=$this->getmicrotime();
		if($this->link_id){
			$return = pg_affected_rows($this->link_id);
		}
		$time2=$this->getmicrotime();
		$this->total_time += $time2-$time1;
		return $return;
	}

  	function insert_id() {
		return false;
		/*
		not a clue how to get this in pgsql

		$time1=$this->getmicrotime();
		if($this->link_id){
			$return = pg_insert_id($this->link_id);
		}
		$time2=$this->getmicrotime();
		$this->total_time += $time2-$time1;
		return $return;
		*/
  	}

	function real_escape_string($string) {
		$time1=$this->getmicrotime();
		if(!$this->is_connected() && $this->connect_first_use){
			//first connection, I try to connect
			$this->connect($this->persistant,true);
		}
		if($string!='' && $this->link_id){
			$return = pg_escape_string($this->link_id,$string);
		} else {
			$return =$string;
		}
//		$return = pgsql_escape_string($string);
		$time2=$this->getmicrotime();
		$this->total_time += $time2-$time1;
		return $return;
	}
	function escape_string($string) {
		//alias for escape_string($string)
		return $this->real_escape_string($string);
	}

  	function print_error($msg) {
		// need to add a session variable to send the errr message to the email
		// only once per session or every 15 minutes

		if($this->link_id){
			$this->errdesc=pg_result_error($this->link_id);
			$this->errno=0;
		} else {
			//no connection, just gettin the last error from Pgsql
			$this->errdesc='no connection';
			$this->errno=0;
		}
		if(!isset($_SESSION[$this->appname][$this->errno]) || ($_SESSION[$this->appname][$this->errno]+(60*15))>time()){
			$_SESSION[$this->appname][$this->errno] = time();

			$messagehtml ='Error in the DB in '.$this->appname.': '.$msg.'<br />';
			$messagehtml.='error PgSQL         : '.$this->errdesc.'<br />';
			$messagehtml.='Date                : '.date('d.m.Y @ H:i').'<br />';
			$messagehtml.='Page                : http'.(isset($_SERVER['HTTPS'])?'s':'').'://'.(isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:'').(isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:'').'<br />';
			$messagehtml.='Referrer            : '.(isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'').'<br />';
			$messagehtml.='USER IP             : '.(isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'').'<br />';

			$message	='Error in the DB in '.$this->appname.': '.$msg."\n";
			$message	.='error PgSQL         : '.$this->errdesc."\n";
			$message	.='Date                : '.date('d.m.Y @ H:i')."\n";
			$message 	.='Page                : http'.(isset($_SERVER['HTTPS'])?'s':'').'://'.(isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:'').(isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:'')."\n";
			$message 	.='Referrer            : '.(isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'')."\n";
			$message	.='USER IP             : '.(isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'')."\n";

			if($this->show_error==1){
				echo $messagehtml;
			}elseif($this->show_error==0){
				echo '<center><h1>ERROR !!</h1><br />an e-mail has been sent to the staff<br />we are sorry for this problem</center>';
			}
			$admin_email = $this->admin_email;
			$headers = '';
			$headers .= 'From: "web Site" <'.$admin_email.'>'."\n";
			$headers .= 'Reply-To: "database" <'.$admin_email.'>'."\n";
			$headers .= 'X-Sender: <$admin_email>'."\n";
			$headers .= 'X-Mailer: PHP/'.phpversion()."\n"; //mailer
			$headers .= 'X-Priority: 3'."\n"; //1 UrgentMessage, 3 Normal

			if($admin_email!=''){
				mail($admin_email, '['.$this->appname.']->Error DB',$message,$headers);
			}
			if($this->trigger_error==1){
				trigger_error('SQL ERROR!!('.$this->errno.')'.$this->errdesc, E_USER_ERROR);
				$this->link_id = false;
			}
			if($this->die_error==1){
				die();
			}
		}
   	}

	function write_log($testo){
		//salvo il log delle query che vengono eseguite su un file
		if ($this->log==1){
			if ($this->filelog=='')		$this->filelog=date('Y-m-d').'.txt';
			$testo = trim($testo);
			if ((substr($testo,0,6)=='INSERT' || substr($testo,0,6)=='UPDATE' || substr($testo,0,6)=='DELETE' || substr($testo,0,7)=='REPLACE')){
				$fp = fopen($this->filelog, 'a');
				if ($fp){
					$testo=str_replace("\n","\\n",$testo);
					$testo=str_replace("\r","\\r",$testo);
					fputs ($fp, $testo."\n");
					fclose($fp);
				}
			}
		}
	}

	function dbug($text){
		if($this->debug==1){
			echo $text;
		}
		$this->write_log('# dbug:  '.$text);
	}

	function getmicrotime(){
		static $var_controllo = 0;
		static $tempo1;

		if ($var_controllo == 0) {
			list($usec, $sec) = explode(' ', microtime());
			$tempo1 = (float)$usec + (float)$sec;
			$var_controllo = 1;
		} elseif ($var_controllo == 1) {
			list($usec, $sec) = explode(' ', microtime());
			$tempo2 = (float)$usec + (float)$sec;
			$tempo_esecuzione = $tempo2 - $tempo1;
			return $tempo_esecuzione;
		}
	}

	function close(){
		if($this->link_id){
			return pg_close($this->link_id);
		}
	}
	function get_total_time(){
		return $this->total_time;
	}
	// this will be called automatically at the end of scope
	public function __destruct() {
   		$this->close();
	}

}
?>