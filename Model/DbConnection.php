<?php
Class DbConnection
{
	protected $servername;
	protected $database;
	protected $username;
	protected $password;
	public $conn;
	public function __construct()
	{
		$this->servername = Config::$DB_SERVER;
		$this->database = Config::$DB_NAME;
		$this->username = Config::$DB_USERNAME;
		$this->password  = Config::$DB_PASSWORD;
		try
		{
		    $this->conn = new PDO("mysql:host=$this->servername;dbname=$this->database", $this->username, $this->password);

		}
		catch(Exception $e)
	    {
	    	echo "error connection Connection failed: " . $e->getMessage();
	    }
	}	

}