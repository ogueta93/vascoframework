<?php
namespace Controllers;

use Core\Controller;

/**
 * Home Controller
 **/
class Home extends Controller
{
	/* Propierties */
	protected $view = 'defaultView';

	/**
	 * Default Method
	 **/
	public function init()
	{
		$this->View->returnView();
	}
}
