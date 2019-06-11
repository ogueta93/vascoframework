<?php
namespace Core;

use Core\I18n;
use Core\Response;
use Core\Helpers\DataTransform;

/**
* Worker Class
**/
class Worker
{
	/* Propierties */
	protected $log = '';
	protected $timeOut = 0;


	/* Objects Propierties */
	protected $I18n;

	/**
	* Default Constructor
	*
	* @param int $timeOut
	**/
	public function __construct()
	{
		ini_set('max_execution_time', $this->timeOut);

		$this->I18n = new I18n();
		$this->Response = Response::getInstance(); 
	}

	/**
	 * Return JSON data
	 *
	 * @param mixed $data
	 * @return json $result
	 **/
	public function returnJson($data)
	{
		$this->Response->returnJson($data);
	}

	/**
	* Return translated string in UTF-8
	*
	* @param string $key
	* @return string
	**/
	public function trad( $key )
	{
		return $this->I18n->getTrad( $key );
	}

	/**
	 * Gets log data
	 * 
	 * @return string log
	 */
	public function getLog()
	{
		return $this->log;
	}

	/**
	 * Sets logs data
	 * 
	 * @param string $line
	 * @param bool $trad
	 * @return void
	 */
	protected function setLog($line, $trad = false)
	{
		$this->log .= $trad ? $this->trad($line)."\n" : $line."\n";
	}

	/**
	 * Sets timeOut
	 *
	 * @param int $timeOut
	 **/
	public function setTimeOut($timeOut)
	{
		$this->timeOut = $timeOut;
	}

	/**
	 * Gets timeOut
	 *
	 * @return int $timeOut
	 **/
	public function getTimeOut()
	{
		return $this->timeOut;
	}
}
