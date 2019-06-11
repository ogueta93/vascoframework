<?php
namespace Core;

use Core\Helpers\DataTransform;
use Core\View;
use Core\Response;

/**
 * Controller Class
 **/
class Controller
{
	/* Propierties */
	protected $method;
	protected $args = [];
	protected $post = [];
	protected $view = null;

	/* Objects Propierties */
	public $Middleware = null;
	public $View = null;
	protected $Response = null;

	/**
	 * Default Constructor
	 *
	 * @param Array $args
	 * @param Array $post
	 **/
	public function __construct($method, $args, $post)
	{
		$this->method = $method;
		$this->args = $args;
		$this->post = $post;

		$this->View = new View($this->view);
		$this->Response = Response::getInstance();
	}

	/**
	 * Execute a method with args and post class
	 *
	 * @return void
	 **/
	public function _execute()
	{
		$className = (new \ReflectionClass($this))->getShortName();
		$params = $this->_sanitizeParams();
		$method = $this->method;

		$this->Middleware = new Middleware($className, $method, $params);
		if ($this->Middleware->check()) {
			if (method_exists($this, $method)) {
				$this->$method(...$params);
			} else {
				throw new \Exception("The method: $method not exits");
			}
		}
	}

	/**
	 * Sanitize params for _execute method
	 *
	 * @return Array $sanitizeParams
	 **/
	protected function _sanitizeParams()
	{
		$sanitizeParams = [];

		if (!$this->post) {
			$sanitizeParams = $this->args;
		} else {
			$methodParams = $this->_get_func_args($this->method);
			foreach ($methodParams as $keyObject => $methodObject) {
				foreach ($this->post as $key => $value) {
					if ($methodObject->name == $key) {
						$sanitizeParams[$keyObject] = $value;
					}

				}
			}
		}

		return $sanitizeParams;
	}

	/**
	 * Default Constructor
	 *
	 * @param string $funcName
	 * @return Array $args
	 **/
	protected function _get_func_args($funcName)
	{
		$args = [];

		$reflection = new \ReflectionMethod($this, $funcName);
		foreach ($reflection->getParameters() as $param) {
			$args[] = $param;
		}

		return $args;
	}

	/**
	 * Get args
	 *
	 * @param $param string
	 * @return $result
	 **/
	public function getArgs($param = null)
	{
		$result = null;

		if ($param != null && array_key_exists($param, $this->args)) {
			$result = $this->args[$param];
		} else {
			$result = $this->args;
		}

		return $result;
	}

	/**
	 * Get post params
	 *
	 * @param $param string
	 * @return $result
	 **/
	public function getPost($param = null)
	{
		$result = null;

		$DataTransform = new DataTransform($this->post);
		$post = $DataTransform->toStdClass();

		if ($param != null && isset($post->$param)) {
			$result = $post->$param;
		} else {
			$result = $post;
		}

		return $result;
	}
}
