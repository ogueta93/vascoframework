<?php

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Core\WebSocketApp;

require_once 'vendor/autoload.php';
require_once "app/core/functions/functions.php";
require_once "app/core/constants/webSocketConstants.php";

$WebSocketApp = WebSocketApp::getInstance();
$Server = IoServer::factory(
	new HttpServer(
		new WsServer(
			$WebSocketApp
		)
	),
	8080
);

$Server->run();
