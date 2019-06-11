<?php
namespace Core;

use Core\Response;
use Core\View;
Use Core\I18n;

/**
* Error Class
**/
class Error
{
	/* Properties */
	protected $error = array();
	protected $view = 'errors/defaultError';

	/* Object Properties */
	protected $I18n;
	protected $View;
	protected $Response;

	/* Default Constructor */
	function __construct( \Exception $error, $view = null )
	{
		$this->error = $error;

		if( $view )
		{
			$this->view = $view;
		}

		$this->I18n = new I18n();
		$this->View = new View($this->view);
		$this->Response = Response::getInstance();
	}


	/**
	* Threat an error an choose best option
	*
	* @param bool apiError
	**/
	public function threatError( $apiError = false )
	{
		if ( $apiError )
		{
			$this->ajaxError();
		}
		else
		{
			$this->defaultError();
		}
	}

	/**
	* Threat a comon error and show it on a view
	*
	* @return View with errors
	**/
	protected function defaultError()
	{
		http_response_code( 500 );

		$data = [];
		$data['errors'] = explode( '#' , $this->error );

		$this->View->returnView( $data );
	}

	/**
	* Threat a Json Error
	*
	* @return Json errors
	**/
	protected function ajaxError()
	{
		http_response_code( 500 );

		$data = [
			'status' => false,
			'error' => [
				'code' => $this->error->getCode(),
				'msg' => $this->I18n->getTrad( $this->error->getMessage() )
			]
		];

		$this->Response->returnJson($data);
	}
}
