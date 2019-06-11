<?php
namespace Core;

use Core\Singleton;
use Core\Config;
use Core\Controller;
use Core\Request;
use Core\Middleware;
use Core\Error;

use Core\Helpers\Session;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class WebSocketApp extends Singleton implements MessageComponentInterface 
{
	/* Properties */
	protected $clients;
	protected $from; 
	
	/* Objects Propierties */
	public $Config = null;
	public $Session = null;
	public $Middleware = null;
	public $Memcache = null;

	/* Default Constructor */
	public function __construct() 
	{
		$this->clients = new \SplObjectStorage;

		$this->Config = new Config( 'config' );
		$this->Session = new Session();
		$this->Memcache = new \Memcached();

		$this->Config->debugMode();
		$this->initMemcache();
	}

	public function onOpen( ConnectionInterface $conn )
	{
		// Store the new connection to send messages to later
		$this->clients->attach( $conn );
		$this->addClient( (int)$conn->resourceId );

		echo "New connection! ({$conn->resourceId})\n";
	}

	public function onMessage( ConnectionInterface $from , $msg )
	{		
		$numRecv = count( $this->clients ) - 1;
		echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
		, $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

		/* Getting clientId */
		$this->from = $from;

		/* Parsing msg */
		$parsedMsg = $this->parseParams( $msg );
		$controller = $parsedMsg[ 'c' ];
		$method = $parsedMsg[ 'm' ];
		$params = $parsedMsg[ 'p' ];
		
		/* Execute Controller */		
		try 
		{
			require( DIR.'app/controllers/'.$controller.'.php' );
			$Controller = new $controller( $method , [] , $params );

			$Controller->_execute();
		}
		catch( \Exception $error )
		{
			$Error = new Error( $error );
			$Error->threatError();
		}
	}

	public function onClose( ConnectionInterface $conn )
	{
		// The connection is closed, remove it, as we can no longer send it messages
		$this->clients->detach( $conn );
		$this->Memcache->delete( $conn->resourceId );

		echo "Connection { $conn->resourceId } has disconnected\n";
	}

	public function onError( ConnectionInterface $conn , \Exception $e )
	{
		echo "An error has occurred: {$e->getMessage()}\n";

		$conn->close();
	}
	
	/**
	* Get current clientId
	*
	* @return int clientId
	**/
	public function getClientId()
	{
		return (int)$this->from->resourceId;
	}

	/**
	* Get clients
	*
	* @return SplObjectStorage $clients
	**/
	public function getClients()
	{
		return $this->clients;
	}

	/**
	* Get current from
	*
	* @return ConnectionInterface $rom
	**/
	public function getFrom()
	{
		return $this->from;
	}

	/**
	* Add new Client to Memcache
	*
	* @param int $resourceId
	**/
	public function addClient( int $resourceId )
	{
		$this->Memcache->set( $resourceId , [] , 0 );
	}

	/**
	* Init Memcache
	*
	**/
	protected function initMemcache()
	{
		$this->Memcache->addServer( 'localhost' , $this->Config->getValue( 'memcachedPort' ) ) or die ( "Could not connect" );
		$this->Memcache->flush();
	}

	/**
	* Parsing Json paramas to Array Data for WebsocketApp
	*
	* @param JSON $msg
	* @return Array $data
	**/
	protected function parseParams( $msg )
	{
		return json_decode( $msg , true );
	}
}