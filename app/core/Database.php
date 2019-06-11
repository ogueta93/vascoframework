<?php
namespace Core;

use Core\Config;
use Core\database\VascoPDO;

/**
 * Database Class
 **/
class Database
{
	/* Constants */
	const DB_FILE = 'database';

	/* Object Properties */
	protected $Config = null;
	protected $PDO = null;

	/* Class Properties */
	protected $host = null;
	protected $database = null;
	protected $user = null;
	protected $password = null;
	protected $connection = null;

	/* Default Constructor */
	public function __construct($dbFile = self::DB_FILE)
	{
		$this->Config = Config::getInstance();

		$this->host = $this->Config->getValue($dbFile, 'host');
		$this->database = $this->Config->getValue($dbFile, 'database');
		$this->user = $this->Config->getValue($dbFile, 'user');
		$this->password = $this->Config->getValue($dbFile, 'password');
		$this->connection = sprintf('%s:host=%s;dbname=%s', $this->Config->getValue($dbFile, 'engine'), $this->host, $this->database);

		$this->setConnection();
	}

	/**
	 * Get PDO Object
	 *
	 * @return PDO $PDO
	 **/
	public function getPDO()
	{
		return $this->PDO;
	}

	/**
	 * Set DB connection
	 *
	 * @return void
	 **/
	public function setConnection()
	{
		$this->PDO = new VascoPDO($this->connection, $this->user, $this->password);
	}

	/**
	 * Close DB connection
	 *
	 * @return void
	 **/
	public function closeConnection()
	{
		$this->PDO = null;
	}
}
