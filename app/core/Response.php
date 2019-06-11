<?php
namespace Core;

use Core\Helpers\DataTransform;
use Core\View;
use Core\Response;
use Core\Singleton;

/**
 * Response Class
 **/
class Response extends Singleton
{
	/* Propierties */
	protected $data = null;

	/* Objects Propierties */

	/**
	 * Default Constructor
	 *
	 **/
	public function __construct() 
	{}
	
	/**
	 * Set data
	 * 
	 * @param mixed $data
	 * @param bool $merge
	 * @return void
	 */
	public function setData($data, $merge = true)
	{
		if (is_array($data)) {
			$this->data = $merge ? array_replace_recursive($this->data, $data) : $data;
		} else {
			$this->data = $data;
		}
	}
	
	/**
	 * Get data
	 * 
	 * @return mixed $data
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * Return JSON data
	 *
	 * @param mixed $data
	 * @return json $result
	 **/
	public function returnJson($data = null)
	{
		if ($data) {
			$this->setData($data, false);
		}

		$DataTransform = new DataTransform($this->data);
		echo $DataTransform->toJSON();die();
	}
}
