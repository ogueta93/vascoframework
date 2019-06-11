<?php
namespace Core;

use Core\Config;
use Core\Controller;
use Core\Error;
use Core\Request;
use Core\Session;
use Core\Singleton;

/**
 * CronApp class
 **/
class CronApp extends Singleton
{
	/* Constants */
	const DIR = 'logs';
	const HTTP = 'http';
	const HTTPS = 'https';

	/* Properties */
	protected $protocol;
	protected $application;
	protected $controller;
	protected $method;
	protected $timeOut = 0;
	
	/* Objects Propierties */
	public $Config = null;
	public $Session = null;
	public $Request = null;

	/**
	 * Default Constructor
	 * 
	 * @param string $protocol
	 * @param string $alias
	 * @param string $application
	 * @param string $controller
	 * @param string $method
	 */
	public function __construct($protocol = null, $alias = null, $application = null, $controller = null, $method = null)
	{
		$this->protocol = $protocol ? $protocol : self::HTTP;
		$this->alias = $alias;
		$this->application = $application;
		$this->controller = $controller;
		$this->method = $method;
	}

	/**
	 * Starts cron
	 * 
	 * @return void
	 */
	public function startCron()
	{
		$dataLog = $this->processCurl();

		$filename = $this->getAlias()."_".date('Y-m-d');
		$file = sprintf('%s/%s', self::DIR, $filename);

		if (file_exists($file)) {
			file_put_contents($file, PHP_EOL.$dataLog['result'] , FILE_APPEND | LOCK_EX);
		} else {
			file_put_contents($file, $dataLog['result']);
		}
	}

	/**
	 * Calls a curl and return data
	 * 
	 * @return array $data;
	 */
	protected function processCurl()
	{
		$url = sprintf('%s://%s/%s/%s', $this->getProtocol(), $this->getApplication(), $this->getController(), $this->getMethod());

		$curlObj = curl_init();
		$options = [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT	=> $this->timeOut
		];

		curl_setopt_array($curlObj, $options);
		$returnData = curl_exec($curlObj);
		if (curl_errno($curlObj)) {
			$returnData = curl_error($curlObj);
		}

		return json_decode($returnData, true);
	}
	
	/**
	 * Sets App's protocol
	 *
	 * @param string $protocol
	 **/
	public function setProtocol($protocol)
	{
		$this->protocol = $protocol;
	}

	/**
	 * Gets App's protocol
	 *
	 * @return string $protocol
	 **/
	public function getProtocol()
	{
		return $this->protocol;
	}

	/**
	 * Sets App's alias
	 *
	 * @param string $alias
	 **/
	public function setAlias($alias)
	{
		$this->alias = $alias;
	}

	/**
	 * Gets App's alias
	 *
	 * @return string $alias
	 **/
	public function getAlias()
	{
		return $this->alias;
	}

	/**
	 * Sets App's application
	 *
	 * @param string $application
	 **/
	public function setApplication($application)
	{
		$this->application = $application;
	}

	/**
	 * Gets App's application
	 *
	 * @return string $application
	 **/
	public function getApplication()
	{
		return $this->application;
	}

	/**
	 * Gets App's controller
	 *
	 * @param string $controller
	 **/
	public function setController($controller)
	{
		$this->controller = $controller;
	}

	/**
	 * Gets App's controller
	 *
	 * @return string $controller
	 **/
	public function getController()
	{
		return $this->controller;
	}

	/**
	 * Sets App's method
	 *
	 * @param string $method
	 **/
	public function setMethod($method)
	{
		$this->method = $method;
	}

	/**
	 * Gets App's method
	 *
	 * @return string $method
	 **/
	public function getMethod()
	{
		return $this->method;
	}

	/**
	 * Sets App's timeOut
	 *
	 * @param int $timeOut
	 **/
	public function setTimeOut($timeOut)
	{
		$this->timeOut = $timeOut;
	}

	/**
	 * Gets App's timeOut
	 *
	 * @return int $timeOut
	 **/
	public function getTimeOut()
	{
		return $this->timeOut;
	}
}
