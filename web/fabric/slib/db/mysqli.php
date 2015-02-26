<?php

/* * *********************************\
  Fabrizio Parrella
  updated on: 09/20/2004 - v.1.0		- initial release
  updated on: 09/21/2004 - v.1.1		- added fetch_object
  updated on: 09/22/2004 - v.1.2		- change the show/hide errors system
  updated on: 09/22/2004 - v.1.2.1	- added the $debug variable
  updated on: 10/06/2004 - v.1.2.2	- added the total_time variable to keep track of the time that is needed to run the queries
  updated on: 01/04/2005 - v.1.3		- added transactions
  updated on: 07/29/2005 - v.1.4		- added the possibility to substitute text in the query
  updated on: 08/08/2005 - v.1.4.1	- added mysqli_escape_string
  updated on: 10/13/2005 - v.1.5		- added the possibility to have the query ALWAYS cache (when it is a select)
  updated on: 10/13/2005 - v.1.5.1	- added some extra error checking
  updated on: 11/01/2005 - v.1.5.2	- if in a query is set SQL_NO_CACHE, the select will not force the SQL_CACHE
  updated on: 11/08/2005 - v.1.5.3	- added a function catled dbug to print or not the debug information
  updated on: 12/27/2005 - v.1.6		- adapted to work withPHP4 and PHP5 w/o error messages (E_STRICTS, OR E_WARNING, OR E_NOTE)
  updated on: 12/29/2005 - v.1.6.1	- added mysqli_num_fields, mysqli_fetch_field, and mysqli_fetch_row
  updated on: 02/04/2006 - v.1.6.2	- added trigger_error when mysql generate an error, this will allow the programmer to manage the error in a better way
  updated on: 02/20/2006 - v.1.6.3	- fixed the variable: db_accesses to be internal and not global
  updated on: 02/27/2006 - v.1.6.4	- fixed the email to send the whole current page and not only the script name
  updated on: 06/25/2006 - v.1.7		- changed function "query" and "query_first", added a second parameter: $record_type
  added functions:
  mysqli_data_seek:  if the second parameter is not passed, will position on a random record
  set_default_result_type:	will change the default $result_type for the query function
  updated on: 07/04/2006 - v.1.8		- added close function to close a mysql connection
  updated on: 07/21/2006 - v.1.9		- added connect_first_use parameter, to allow the class to connect only when it is used the first time
  this will avoid connection to the database when there are not queries executed
  updated on: 08/22/2006 - v.1.10		- added get_total_time() to get $this->total_time
  updated on: 08/22/2006 - v.1.11		- added an extra value to SQL_CACHE: 2 = strip out SQL_CACHE from statement
  updated on: 09/02/2007 - v.1.12		- added function set_variables
  updated on: 09/02/2007 - v.1.13		- fixed a warning message
  updated on: 02/08/2011 - v.1.14		- automatically strip comments from query


  this class is one of the many classes to access the MYSQL database.

  this class will send an email evrytime that there is an error with some query, in this way
  will be simpler to monitor the site and to see if there is any kind of problem
  with MYSQL/query

  \********************************** */

class db_mysqli extends db{
	protected $mysqli			 = false;
	protected $link_id			 = 0;
	protected $read_link_id		 = 0;
	protected $query_id			 = 0;
	protected $record			 = array();
	public $errdesc				 = '';
	public $errno				 = 0;
	public $show_error			 = 0;
	public $die_error			 = 1;
	public $trigger_error		 = 1;
	public $strip_comment		 = 1;
	public $database			 = '';
	public $server				 = '';
	public $user				 = '';
	public $password			 = '';
	public $read_database		 = '';
	public $read_server			 = '';
	public $read_user			 = '';
	public $read_password		 = '';
	public $log					 = 0; //abilita il file log
	public $filelog				 = ''; //nome del file log
	public $appname				 = 'DataBase Managment';
	public $admin_email			 = '';
	public $debug				 = 0;
	protected $total_time		 = 0;
	protected $db_accesses		 = 0;
	protected $substitute		 = array();
	protected $default_result_type;
	public $SQL_CACHE			 = 1; //if = 1, will add SQL_CACHE to all the select queries
	public $connect_first_use	 = false;  //if = true, will connect only when the first query is executed
	protected $in_trans			 = false; //if I am into a transaction
	private $timezone			 = '';
	public function set_variables($vars = array()){
		//this function can be used set all the variables for this class at once
		foreach($vars as $key=> $value){
			if(isset($this->$key)){
				$this->$key = $value;
			}
		}
	}
	function __construct(){
		parent::$instance = &$this;
		$this->substitute['what']	 = '';
		$this->substitute['with']	 = '';
//		$this->default_result_type	= MYSQLI_ASSOC;
//		$this->default_result_type	= MYSQLI_NUM;
		$this->default_result_type	 = MYSQLI_BOTH;  //mysql default
	}
	function set_default_result_type($result_type){
		if(
				$result_type == MYSQLI_ASSOC ||
				$result_type == MYSQLI_NUM ||
				$result_type == MYSQLI_BOTH
		){
			$this->default_result_type = $result_type;
		}
	}
	function substitute($what, $with){
		$this->substitute['what']	 = $what;
		$this->substitute['with']	 = $with;
	}
	function connect($persistant = false, $first_use = false, $read = false){
		$this->persistant = $persistant;
		if(!$this->connect_first_use || $first_use){
			$time1		 = $this->getmicrotime();
			$server		 = $read?$this->read_server:$this->server;
			$user		 = $read?$this->read_user:$this->user;
			$password	 = $read?$this->read_password:$this->password;
			$database	 = $read?$this->read_database:$this->database;
			$link_id	 = $read?$this->read_link_id:$this->link_id;
			if(0 == $link_id){
				if($this->database != ''){
					if($persistant){
						$link_id = @mysqli_connect('p:'.$server, $user, $password, $database);
					}else{
						$link_id = @mysqli_connect($server, $user, $password, $database);
					}
				}else{
					if($persistant){
						$link_id = @mysqli_connect('p:'.$server, $user, $password);
					}else{
						$link_id = @mysqli_connect($server, $user, $password);
					}
				}
				if(!$link_id){
					$this->print_error('Link-ID == false, connect failed'.($read?' - read':''), $read);
				}
				if($read){
					$this->read_link_id = $link_id;
				}else{
					$this->link_id = $link_id;
				}
				if($this->timezone != ''){
					$this->query('SET time_zone = "'.$this->timezone.'";') or die('Error While Setting MySQL Timezone '.$this->timezone.' !<br />'.mysqli_error());
				}
			}
			$time2 = $this->getmicrotime();
			if($read){
//				unset($this->read_server, $this->read_user, $this->read_password);
			}else{
//				unset($this->server, $this->user, $this->password);
			}
			$this->total_time += $time2 - $time1;
		}
	}
	function pconnect($read = false){
		$this->connect(true, false, $read);
	}
	function is_connected($read = false){
		return (bool)($read?$this->read_link_id:$this->link_id);
	}
	function geterrdesc(){
		$this->error = mysqli_error();
		return $this->error;
	}
	function geterrno(){
		$this->errno = mysqli_errno();
		return $this->errno;
	}
	function select_db($database = ''){
		if($database != ''){
			$this->database = $database;
		}
		if(!mysqli_select_db($this->database)){
			$this->print_error('Impossible to open the database '.$this->database);
		}
	}
	function query($query_string, $read = false){
		if(!$this->is_connected($read) && $this->connect_first_use){
			//first connection, I try to connect
			$this->connect($this->persistant, true, $read);
		}
		$link_id = $read?$this->read_link_id:$this->link_id;
		if($link_id){
			$time1			 = $this->getmicrotime();
			$this->db_accesses++;
			$query_string	 = trim($query_string);

			if($this->substitute['what'] != '' && $this->substitute['with'] != ''){
				$query_string = str_replace($this->substitute['what'], $this->substitute['with'], $query_string);
			}

			if($this->SQL_CACHE > 0 && strtoupper(substr($query_string, 0, 6)) == 'SELECT'){
				if($this->SQL_CACHE == 1 && !preg_match('/^SELECT[\s+]SQL_NO_CACHE/i', $query_string)){
					//add cache
					$query_string	 = preg_replace('/^SELECT[\s+]SQL_CACHE/i', 'SELECT ', $query_string);
					$query_string	 = preg_replace('/^SELECT[\s+]/i', 'SELECT SQL_CACHE ', $query_string);
				}elseif($this->SQL_CACHE == 2){
					//remove cache
					$query_string	 = preg_replace('/^SELECT[\s+]SQL_CACHE/i', 'SELECT ', $query_string);
					$query_string	 = preg_replace('/^SELECT[\s+]SQL_NO_CACHE/i', 'SELECT ', $query_string);
				}
			}
			if($this->strip_comment == 1){
				//remove comments
				$query_string = preg_replace('@/\*[^\*].*?\*/@s', '', $query_string);
			}

			$this->dbug('<font size="1">::q'.$this->db_accesses.'::'.$query_string);

			$this->query_id	 = mysqli_query($link_id, $query_string);
			$this->write_log($query_string); //save the query log
			$time2			 = $this->getmicrotime();
			$this->dbug('::('.number_format(($time2 - $time1), 10).')</font><br />');
			$this->total_time += $time2 - $time1;
			if(!$this->query_id || mysqli_errno($link_id) > 0){
				$this->print_error('QUERY SQL not valid: '.$query_string, $read);
				if($this->in_trans){
					$this->rollback_transaction();
				}
			}
			return $this->query_id;
		}else{
			return false;
		}
	}
	function query_first($query_string, $result_type = NULL, $read = false){
		if($this->query($query_string, $read) != false){   //call the query function
			$returnarray = $this->fetch_array($this->query_id, $result_type);
			$this->free_result($this->query_id);
			return $returnarray;
		}else{
			return false;
		}
	}
	function data_seek($query_id = -1, $pos = -1){
		$time1 = $this->getmicrotime();
		if($query_id !== -1){
			$this->query_id = $query_id;
		}
		if($this->query_id){
			if($this->num_rows($this->query_id) > 0){
				if($pos == -1){
					//a random position
					$pos = rand(1, $this->num_rows($this->query_id)) - 1;
				}
				$return = mysqli_data_seek($this->query_id, $pos);
			}else{
				$return = false;
			}
		}else{
			$return = false;
		}

		$time2 = $this->getmicrotime();
		$this->total_time += $time2 - $time1;
		return $return;
	}
	function fetch_array($query_id = -1, $result_type = NULL){
		$time1 = $this->getmicrotime();
		if($query_id !== -1){
			$this->query_id = $query_id;
		}
		if($result_type == NULL) $result_type = $this->default_result_type;
		if($this->query_id){
			$this->record = mysqli_fetch_array($this->query_id, $result_type);
		}else{
			$this->record = false;
		}

		$time2 = $this->getmicrotime();
		$this->total_time += $time2 - $time1;
		return $this->record;
	}
	function fetch_object($query_id = -1){
		$time1 = $this->getmicrotime();
		/*
		  thanks to:
		  Thomas Bruhin - mailto:thomas@mediasonics.ch
		  Media Sonics - http://www.mediasonics.ch
		 */
		if($query_id !== -1){
			$this->query_id = $query_id;
		}

		if($this->query_id){
			$this->record = mysqli_fetch_object($this->query_id);
		}else{
			$this->record = false;
		}

		$time2 = $this->getmicrotime();
		$this->total_time += $time2 - $time1;
		return $this->record;
	}
	function fetch_row($query_id = -1){
		$time1 = $this->getmicrotime();
		if($query_id !== -1){
			$this->query_id = $query_id;
		}

		if($this->query_id){
			$this->record = mysqli_fetch_row($this->query_id);
		}else{
			$this->record = false;
		}

		$time2 = $this->getmicrotime();
		$this->total_time += $time2 - $time1;
		return $this->record;
	}
	function fetch_field($query_id = -1, $field_offset = 0){
		$time1 = $this->getmicrotime();
		if($query_id !== -1){
			$this->query_id = $query_id;
		}

		if($this->query_id){
			$this->record = mysqli_fetch_field($this->query_id, $field_offset);
		}else{
			$this->record = false;
		}

		$time2 = $this->getmicrotime();
		$this->total_time += $time2 - $time1;
		return $this->record;
	}
	function num_fields($query_id = -1){
		$time1 = $this->getmicrotime();
		if($query_id !== -1){
			$this->query_id = $query_id;
		}

		if($this->query_id){
			$this->record = mysqli_num_fields($this->query_id);
		}else{
			$this->record = 0;
		}

		$time2 = $this->getmicrotime();
		$this->total_time += $time2 - $time1;
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
	function free_result($query_id = -1){
		$time1 = $this->getmicrotime();
		if($query_id !== -1){
			$this->query_id = $query_id;
		}
		$return	 = mysqli_free_result($this->query_id);
		$time2	 = $this->getmicrotime();
		$this->total_time += $time2 - $time1;
		return $return;
	}
	function num_rows($query_id = -1){
		$time1 = $this->getmicrotime();
		if($query_id !== -1){
			$this->query_id = $query_id;
		}
		$return	 = mysqli_num_rows($this->query_id);
		$time2	 = $this->getmicrotime();
		$this->total_time += $time2 - $time1;
		return $return;
	}
	function affected_rows($read = false){
		$time1	 = $this->getmicrotime();
		$link_id = $read?$this->read_link_id:$this->link_id;
		if($link_id){
			$return = mysqli_affected_rows($link_id);
		}
		$time2 = $this->getmicrotime();
		$this->total_time += $time2 - $time1;
		return $return;
	}
	function insert_id($read = false){
		$time1	 = $this->getmicrotime();
		$link_id = $read?$this->read_link_id:$this->link_id;
		if($link_id){
			$return = mysqli_insert_id($link_id);
		}
		$time2 = $this->getmicrotime();
		$this->total_time += $time2 - $time1;
		return $return;
	}
	function real_escape_string($string, $read = false){
		$time1 = $this->getmicrotime();
		if(!$this->is_connected($read) && $this->connect_first_use){
			//first connection, I try to connect
			$this->connect($this->persistant, true, $read);
		}
		$link_id = $read?$this->read_link_id:$this->link_id;
		if($string != '' && $link_id){
			$return = mysqli_real_escape_string($link_id, $string);
		}else{
			$return = $string;
		}
//		$return = mysqli_real_escape_string($string);
		$time2 = $this->getmicrotime();
		$this->total_time += $time2 - $time1;
		return $return;
	}
	function escape_string($string){
		//alias for escape_string($string)
		return $this->real_escape_string($string);
	}
	function print_error($msg, $read = false){
		// need to add a session variable to send the errr message to the email
		// only once per session or every 15 minutes

		$link_id = $read?$this->read_link_id:$this->link_id;
		if($link_id){
			$this->errdesc	 = mysqli_error($link_id);
			$this->errno	 = mysqli_errno($link_id);
		}else{
			//no connection, just gettin the last error from Mysql
			$this->errdesc	 = 'NO LINK-ID';
			$this->errno	 = 0;
		}
		if(!isset($_SESSION[$this->appname][$this->errno]) || ($_SESSION[$this->appname][$this->errno] + (60 * 15)) > time()){
			$_SESSION[$this->appname][$this->errno] = time();

			$messagehtml = 'Error in the DB in '.$this->appname.': '.$msg.'<br />';
			$messagehtml .='error mySQL         : '.$this->errdesc.'<br />';
			$messagehtml .='error number mySQL  : '.$this->errno.'<br />';
			$messagehtml .='Date                : '.date('d.m.Y @ H:i').'<br />';
			$messagehtml .='Page                : http'.(isset($_SERVER['HTTPS'])?'s':'').'://'.(isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:'').(isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:'').'<br />';
			$messagehtml .='Referrer            : '.(isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'').'<br />';
			$messagehtml .='User IP             : '.(isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'').'<br />';
			$messagehtml .= 'Server IP          : '.(isset($_SERVER['SERVER_ADDR'])?$_SERVER['SERVER_ADDR']:'')."\n";
			$messagehtml .= 'SERVER NAME        : '.(isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:'')."\n";

			$message = 'Error in the DB in '.$this->appname.': '.$msg."\n";
			$message .='error mySQL         : '.$this->errdesc."\n";
			$message .='error number mySQL  : '.$this->errno."\n";
			$message .='Date                : '.date('d.m.Y @ H:i')."\n";
			$message .='Page                : http'.(isset($_SERVER['HTTPS'])?'s':'').'://'.(isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:'').(isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:'')."\n";
			$message .='Referrer            : '.(isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'')."\n";
			$message .='User IP             : '.(isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'')."\n";
			$message .= 'Server IP          : '.(isset($_SERVER['SERVER_ADDR'])?$_SERVER['SERVER_ADDR']:'')."\n";
			$message .= 'SERVER NAME        : '.(isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:'')."\n";

			if($this->show_error == 1){
				echo $messagehtml;
			}elseif($this->show_error == 0){
				echo '<center><h1>ERROR !!</h1><br />an e-mail has been sent to the staff<br />we are sorry for this problem</center>';
			}
			$admin_email = $this->admin_email;
			$headers	 = '';
			$headers .= 'From: "web Site" <'.$admin_email.'>'."\n";
			$headers .= 'Reply-To: "database" <'.$admin_email.'>'."\n";
			$headers .= 'X-Sender: <$admin_email>'."\n";
			$headers .= 'X-Mailer: PHP/'.phpversion()."\n"; //mailer
			$headers .= 'X-Priority: 3'."\n"; //1 UrgentMessage, 3 Normal

			if($admin_email != ''){
				mail($admin_email, '['.$this->appname.']->Error DB', $message, $headers);
			}
			if($this->trigger_error == 1){
				trigger_error('SQL ERROR!!('.$this->errno.')'.$this->errdesc, E_USER_ERROR);
				if($read){
					$this->read_link_id = false;
				}else{
					$this->link_id = false;
				}
			}
			if($this->die_error == 1){
				die();
			}
		}
	}
	function write_log($testo){
		//salvo il log delle query che vengono eseguite su un file
		if($this->log == 1){
			if($this->filelog == '') $this->filelog	 = date('Y-m-d').'.txt';
			$testo			 = trim($testo);
			if((substr($testo, 0, 6) == 'INSERT' || substr($testo, 0, 6) == 'UPDATE' || substr($testo, 0, 6) == 'DELETE' || substr($testo, 0, 7) == 'REPLACE')){
				$fp = fopen($this->filelog, 'a');
				if($fp){
					$testo	 = str_replace("\n", "\\n", $testo);
					$testo	 = str_replace("\r", "\\r", $testo);
					fputs($fp, $testo."\n");
					fclose($fp);
				}
			}
		}
	}
	function dbug($text){
		if($this->debug == 1){
			echo $text;
		}
		$this->write_log('# dbug:  '.$text);
	}
	function getmicrotime(){
		static $var_controllo = 0;
		static $tempo1;

		if($var_controllo == 0){
			list($usec, $sec) = explode(' ', microtime());
			$tempo1			 = (float)$usec + (float)$sec;
			$var_controllo	 = 1;
		}elseif($var_controllo == 1){
			list($usec, $sec) = explode(' ', microtime());
			$tempo2				 = (float)$usec + (float)$sec;
			$tempo_esecuzione	 = $tempo2 - $tempo1;
			return $tempo_esecuzione;
		}
	}
	function close($read = NULL){
		if(is_null($read)){
			//close both
			if($this->read_link_id){
				mysqli_close($this->read_link_id);
			}
			if($this->link_id){
				mysqli_close($this->link_id);
			}
			return true;
		}else{
			$link_id = $read?$this->read_link_id:$this->link_id;
			if($link_id){
				return mysqli_close($link_id);
			}
		}
	}
	function get_total_time(){
		return $this->total_time;
	}
	function get_number_queries(){
		return $this->db_accesses;
	}
	// this will be called automatically at the end of scope
	public function __destruct(){
		$this->close();
	}
}

?>