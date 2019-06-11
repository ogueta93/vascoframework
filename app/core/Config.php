<?php
namespace Core;

use Core\Helpers\DataTransform;
use Core\Singleton;

/**
 * Configuration Class
 **/
class Config extends Singleton
{
	/* Properties */
	protected $config = null;
	protected $rawConfiguration = null;
	protected $configuration = null;

	/**
	 * Gets a specifig config
	 *
	 * @return void
	 * @param string $config
	 */
	protected function getConfig($config)
	{
		$this->config = $config;
		//transform configurationto stdClass
		$configuration = include DIR . 'app/configs/' . $config . '.php';
		$DataTransform = $this->DataTransform = new DataTransform($configuration);

		$this->rawConfiguration = $configuration;
		$this->configuration = $DataTransform->toStdClass();
	}

	/**
	 * Gets Config's value
	 *
	 * @param string $config
	 * @param string $param
	 * @return $result
	 **/
	public function getValue($config, $param)
	{
		$result = null;

		$this->getConfig($config);

		if (isset($this->configuration->$param)) {
			$result = $this->configuration->$param;
		}

		return $result;
	}

	/**
	 * Gets Config's value
	 *
	 * @param string $config
	 * @param string $param
	 * @return $result
	 **/
	public function getRawValue($config, $param)
	{
		$result = null;

		$this->getConfig($config);

		if (isset($this->rawConfiguration[$param])) {
			$result = $this->rawConfiguration[$param];
		}

		return $result;
	}

	/**
	 * Set Debug Mode
	 **/
	public function debugMode()
	{
		if ($this->getValue('config', 'debug') == true) {
			error_reporting(E_ALL);
			ini_set("display_errors", "1");
			set_error_handler('exceptions_error_handler');
		}
	}
}
