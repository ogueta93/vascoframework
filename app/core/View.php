<?php
namespace Core;

use Core\I18n;

/**
 * View Class
 **/
class View
{
	/* Propierties */
	protected $default = 'defaultView';
	protected $view;
	protected $params;

	/* Object Propierties */
	protected $I18n;

	/**
	 * Default Constructor
	 *
	 * @param $view
	 **/
	public function __construct($view = null)
	{
		if (is_null($view)) {
			$view = $this->default;
		}
		$this->view = $view;

		$this->I18n = new I18n();
	}

	/**
	 * Set view
	 *
	 * @param $view string
	 **/
	public function setView($view)
	{
		$this->view = $view;
	}

	/**
	 * Show the Controller's View
	 *
	 * @param Array $params
	 **/
	public function returnView($params = null)
	{
		ob_start();

		$this->params = $params;

		include DIR . 'app/views/' . $this->view . '.php';
		$view = ob_get_contents();

		ob_end_clean();

		print $view;
	}

	/**
	 * Gets the html view data
	 *
	 * @param Array $params
	 * @return string $view
	 **/
	public function getViewData($params = null)
	{
		ob_start();

		$this->params = $params;

		include DIR . 'app/views/' . $this->view . '.php';
		$view = ob_get_contents();

		ob_end_clean();

		return $view;
	}
}
