<?php
namespace Core;

use Core\Error;
use Core\Session;

/**
 * Request Class
 **/
class Request
{
	/* Properties */
	protected $controller = "Home";
	protected $method = "init";
	protected $args = [];
	protected $post = [];

	protected $preg = "/^[A-Z]/";

	/* Default Constructor */
	public function __construct()
	{
		$requestURI = explode('/', $_SERVER['REQUEST_URI']);
		$scriptName = explode('/', $_SERVER['SCRIPT_NAME']);

		for ($i = 0; $i < sizeof($scriptName); $i++) {
			if ($requestURI[$i] == $scriptName[$i]) {
				unset($requestURI[$i]);
			}
		}

		$command = array_values($requestURI);

		//Settings controller, method and args
		foreach ($command as $key => $value) {
			if ($key == 0 && preg_match($this->preg, $value) == 1) {
				$this->controller = $value;
			} elseif ($key == 1 && preg_match($this->preg, $value) == 1) {
				$this->method = lcfirst($value);
			} elseif (!empty($value)) {
				$this->args[] = urldecode($value);
			}
		}
	}

	/**
	 * Get Controller
	 *
	 * @return string $this->controller
	 **/
	public function getController()
	{
		return $this->controller;
	}

	/**
	 * Get Method
	 *
	 * @return string $this->method
	 **/
	public function getMethod()
	{
		return $this->method;
	}

	/**
	 * Get Args
	 *
	 * @return Array $this->args
	 **/
	public function getArgs()
	{
		return $this->args;
	}

	/**
	 * Get Post
	 *
	 * @return Array $this->post
	 **/
	public function getPost()
	{
		return $this->post;
	}
}
