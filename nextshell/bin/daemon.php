<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use OCA\nextshell\WebSocket\SshHandler;

// We expect 5 arguments: script name, port, idleTimeout, sessionTimeout, proxyConfig
if ($argc < 5) {
    fwrite(STDERR, "Usage: php daemon.php <port> <idleTimeout> <sessionTimeout> <base64ProxyConfig>\n");
    exit(1);
}

$port = (int)$argv[1];
$idleTimeout = (int)$argv[2];
$sessionTimeout = (int)$argv[3];
$proxyConfig = json_decode(base64_decode($argv[4]), true);

// Basic validation
if ($port <= 0 || $port > 65535) {
    fwrite(STDERR, "Invalid port number provided.\n");
    exit(1);
}

// Create the SshHandler with the passed configuration
$sshHandler = new SshHandler($idleTimeout, $sessionTimeout, $proxyConfig);

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            $sshHandler
        )
    ),
    $port,
    '0.0.0.0' // Bind to all interfaces
);

echo "NextShell WebSocket server starting on port $port\n";
$server->run();
