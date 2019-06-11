<?php
namespace Core;

use Core\View;
use Core\Error;

/**
* Middleware class
**/
class Middleware
{
	/* Properties */
	protected $defaultMiddleware = 'global';
	protected $middlewares;
	protected $controller;
	protected $method;
	protected $params;

	/**
	* Default Constructor
	*
	* @param $controller string, @param $method string
	**/
	public function __construct( $controller, $method , $params )
	{
		$this->controller = $controller;
		$this->method = $method;
		$this->params = $params;
		$this->middlewares = include( DIR."app/middlewares/rules.php" );
	}

	/**
	* Checks permissions from middlewares
	*
	* @return $result bool
	**/
	public function check()
	{
		try
		{
			$methods = $this->findMiddlewareMethod();
			$result = $this->getMiddlewareResult( $methods );
		}
		catch ( \Exception $error )
		{
			$Error = new Error( $error );
			$Error->threatError();
		}

		return $result;
	}

	/**
	* Returns the middleware's associated methods
	*
	* @return Array $result
	**/
	protected function findMiddlewareMethod()
	{
		$result = null; 

		if( !array_key_exists( $this->controller, $this->middlewares ) )
		{
			throw new \Exception( "There is not a Rule for this Controller: $this->controller" );
		}

		foreach( $this->middlewares[ $this->controller ] as $key => $middlewaresRules )
		{
			if ( $key != $this->defaultMiddleware )
			{
				$middlewaresMethods = array_map( 'trim' , explode( ',' , $key ) );
				
				if ( in_array( $this->method , $middlewaresMethods ) )
				{
					$result = $middlewaresRules;
					break;		
				}
			}
		}
		
		//if middleware has not been found	
		if ( !$result )
		{
			$result = $this->middlewares[ $this->controller ][ $this->defaultMiddleware ];
		}
		
		$result = !is_array( $result ) ? [ $result ] : $result;
		
		return $result;
	}

	/**
	* Returns the middleware result
	*
	* @param Array $methods
	* @return Boolean $result
	**/
	protected function getMiddlewareResult( $methods )
	{
		/* Midlewares Functions */
		require_once( DIR."app/middlewares/ruler.php" );

		$result = false;
		foreach ( $methods as $key => $method )
		{
			if( is_array( $this->params ) )
			{
				if( !$result = $method( ...$this->params ) )
				{
					break;
				}
			}
			else
			{
				if( !$result = $method( $this->params ) )
				{
					break;
				}
			}
		}

		return $result;
	}	
}
