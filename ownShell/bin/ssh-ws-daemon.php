#!/usr/bin/env php
<?php
require __DIR__ . '/../vendor/autoload.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use OCA\OwnShell\WebSocket\Ssh;
use React\EventLoop\Factory as LoopFactory;
use React\Socket\Server as SocketServer;

// The script will listen on this port
$port = 8080;

echo "ownShell WebSocket daemon starting on port $port...\n";

$loop = LoopFactory::create();

$socket = new SocketServer('0.0.0.0:' . $port, $loop);

$server = new IoServer(
    new HttpServer(
        new WsServer(
            new Ssh($loop)
        )
    ),
    $socket,
    $loop
);

$server->run();
