<?php
class db
{
	private static $_instance = null;
	private $handler;
	private $result = false;
	public static function getInstance()
	{
		if ( !self::$_instance )
		{
			$class = __CLASS__;
			self::$_instance = new $class();
		}
		return self::$_instance;
	}
	private function __construct()
	{
		$this->connect();
	}
	private function __clone() {}

	public function connect()
	{
		$dbhost = $dbuser = $dbpasswd = $dbname = '';
		include(realpath(dirname(__FILE__) . '/config.php'));
		$this->handler = new mysqli($dbhost, $dbuser, $dbpasswd, $dbname);
		if ( $this->handler->connect_error )
		{
			die('Connect error::' . $this->handler->connect_errno . '::'. $this->handler->connect_error);
		}
		if ( $this->handler->query('SET NAMES \'utf8\'') === false )
		{
			die('Error::' . $this->handler->errno . '::' . $this->handler->error);
		}
	}

	public function query($sql)
	{
		$this->result = $this->handler->query($sql);
		return $this->result;
	}

	//will only return the last result…
	public function multi_query_last_result($sql)
	{	$this->result = false;
		if($this->handler->multi_query($sql)){
			$continue = true;
			while($continue){
				$this->result = $this->handler->store_result();
				if($this->handler->more_results()){
					$this->handler->next_result();
				}
				else{
					$continue=false;
				}
			}
		}
		return $this->result;
	}

	public function transaction($query_array){
		$this->handler->begin_transaction();
		$i=1;
		foreach ($query_array as $query) {
			$this->query($query);
			if(!$this->result){
				$commit = false;
				$this->handler->rollback();
				throw new Exception("Error “".$this->handler->error."” in “".$query."” (query #".$i.").");
			}
			$i++;
		}
		$this->handler->commit();
		return $this->result;
	}

	public function fetch_object(){
		if($this->result){
			return $this->result->fetch_object();
		}
		else{
			return false;
		}
	}

	public function fetch_assoc(){
		if($this->result){
			return $this->result->fetch_assoc();
		}
		else{
			return false;
		}
	}

	public function insert_id()
	{
		return $this->handler->insert_id;
	}

	public function affected_rows()
	{
		return $this->handler->affected_rows;
	}

	public function has_result(){
		return ((bool) $this->result);
	}

	public function num_rows(){
		$res = 0;
		if($this->has_result()){
			$res = $this->result->num_rows;
		}
		return $res;
	}

	public function get_result(){
		return $this->result;
	}

	public function escape($str)
	{
		return is_string($str) ? '\'' . $this->handler->real_escape_string($str) . '\'' : intval($str);
	}
}
?>
