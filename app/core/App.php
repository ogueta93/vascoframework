<?php
namespace Core;

use Core\Config;
use Core\Controller;
use Core\Error;
use Core\Request;
use Core\Session;
use Core\Singleton;

/**
 * App class
 **/
class App extends Singleton
{
	/* Constants */
	const CONTROLLER_DIR = "app/controllers/";

	/* Properties */
	protected $controller = null;
	protected $method = null;
	protected $apiApp = false;

	/* Objects Propierties */
	public $Config = null;
	public $Session = null;
	public $Request = null;
	

	/* Default Constructor */
	public function __construct()
	{
		$this->setHeaders();
		/* Config */
		$this->Config = Config::getInstance();
		$this->Config->debugMode();

		/* api app */
		$this->apiApp = $this->Config->getValue('config', 'apiApp');

		/* Sessison Start */
		if ($this->Config->getValue('config', 'sessionStart')) {
			$this->Session = Session::getInstance();
		}

		$this->Request = new Request();
	}

	public function startApp()
	{
		/* Execute Controller */
		$controllerName = $this->Request->getController();
		$method = $this->Request->getMethod();
		$args = $this->Request->getArgs();
		$post = $this->Request->getPost();

		try
		{
			$controller = $this->findController($controllerName);
			$Controller = new $controller($method, $args, $post);

			$Controller->_execute();
		} catch (\Exception $error) {
			$Error = new Error($error);
			$Error->threatError($this->apiApp);
		}
	}

	/**
	 * Get App's controller
	 *
	 * @return string $controller
	 **/
	public function getController()
	{
		return $this->controller;
	}

	/**
	 * Get App's method
	 *
	 * @return string $method
	 **/
	public function getMethod()
	{
		return $this->method;
	}

	/**
	 * Get api mode
	 *
	 * @return bool $apiApp
	 **/
	public function getApiMode()
	{
		return $this->apiApp;
	}

	/**
	 * Find Controller on controllers directory on returns realPath to invoke
	 *
	 * @param string $controllerName
	 * @return string $realPath
	 **/
	protected function findController($controllerName)
	{
		$findedFile = false;
		$dirPattern = self::CONTROLLER_DIR;

		$iti = new \RecursiveDirectoryIterator($dirPattern);
		foreach (new \RecursiveIteratorIterator($iti) as $file) {
			if (strpos($file, $controllerName) !== false) {
				$findedFile = $file;
				break;
			}
		}

		if ($findedFile) {
			$path = preg_replace("/app\/controllers/", "", $findedFile->getPath());
			$path = preg_replace("/\//", "\\", $path);
			$realPath = "\\Controllers$path\\$controllerName";

			return $realPath;
		} else {
			throw new \Exception("There is not controller for: $controllerName");
		}
	}
	
	/**
	 * Set headers
	 * 
	 * @return void
	 */
	public function setHeaders()
	{
		header('Access-Control-Allow-Origin: *');
	}
}
