#!/usr/bin/env php
<?php
require dirname(__DIR__) . '/vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use phpseclib3\Net\SSH2;
use React\EventLoop\LoopInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class SshTerminal implements MessageComponentInterface {
    protected $clients;
    protected $sshConnections;
    protected $loop;
    protected $lastMessageTime;
    protected $jwtSecret;

    const IDLE_TIMEOUT = 900; // 15 minutes

    public function __construct(LoopInterface $loop) {
        $this->clients = new \SplObjectStorage;
        $this->sshConnections = [];
        $this->lastMessageTime = [];
        $this->loop = $loop;
        // In a real app, this would be loaded securely from the ownCloud config
        $this->jwtSecret = 'your-super-secret-key-that-is-at-least-32-bytes-long';

        // Add a timer to check for idle connections
        $this->loop->addPeriodicTimer(60, function () {
            $now = time();
            foreach ($this->clients as $client) {
                $resourceId = $client->resourceId;
                if (isset($this->lastMessageTime[$resourceId])) {
                    if ($now - $this->lastMessageTime[$resourceId] > self::IDLE_TIMEOUT) {
                        echo "Client {$resourceId} timed out due to inactivity.\n";
                        $client->send("Connection timed out due to inactivity.\n");
                        $client->close();
                    }
                }
            }
        });
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        $this->lastMessageTime[$conn->resourceId] = time();
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $resourceId = $from->resourceId;
        $this->lastMessageTime[$resourceId] = time();

        if (!isset($this->sshConnections[$resourceId])) {
            // First message must be the JWT
            try {
                $decoded = JWT::decode($msg, new Key($this->jwtSecret, 'HS256'));
                $host = $decoded->host;
                $user = $decoded->user;
                $pass = $decoded->pass; // In a real app, use ownCloud's credential store

                echo "JWT validated for user {$user}. Attempting SSH to {$host}...\n";
                // TODO: Log successful JWT validation to ownCloud log

            } catch (\Exception $e) {
                echo "Invalid JWT received from {$resourceId}: {$e->getMessage()}\n";
                // TODO: Log failed JWT validation
                $from->send("Invalid authentication token.\n");
                $from->close();
                return;
            }

            try {
                $ssh = new SSH2($host);
                // TODO: Add logic to use public key auth if specified
                if (!$ssh->login($user, $pass)) {
                    // TODO: Log failed SSH login
                    $from->send("SSH Login Failed.\n");
                    $from->close();
                    return;
                }

                $this->sshConnections[$resourceId] = $ssh;
                $from->send("SSH Connection Established.\n");
                // TODO: Log successful SSH connection

                // Correctly pipe data from SSH to WebSocket using the event loop
                $this->loop->addPeriodicTimer(0.01, function () use ($ssh, $from, $resourceId) {
                    if (!$ssh->isConnected() || !$this->clients->contains($from)) {
                        // Clean up the timer if the connection is closed
                        $this->loop->cancelTimer($this);
                        if ($this->clients->contains($from)) {
                            $from->close();
                        }
                        return;
                    }
                    if ($data = $ssh->read()) {
                        $from->send($data);
                    }
                });

            } catch (\Exception $e) {
                // TODO: Log connection error
                $from->send("Error connecting: " . $e->getMessage() . "\n");
                $from->close();
            }
        } else {
            // If we already have an SSH connection, forward the message to it.
            $this->sshConnections[$resourceId]->write($msg);
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $resourceId = $conn->resourceId;
        echo "Connection {$resourceId} has disconnected\n";
        // TODO: Log disconnection
        if (isset($this->sshConnections[$resourceId])) {
            $this->sshConnections[$resourceId]->disconnect();
            unset($this->sshConnections[$resourceId]);
        }
        if (isset($this->lastMessageTime[$resourceId])) {
            unset($this->lastMessageTime[$resourceId]);
        }
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        // TODO: Log error
        $resourceId = $conn->resourceId;
        if (isset($this->sshConnections[$resourceId])) {
            $this->sshConnections[$resourceId]->disconnect();
            unset($this->sshConnections[$resourceId]);
        }
        if (isset($this->lastMessageTime[$resourceId])) {
            unset($this->lastMessageTime[$resourceId]);
        }
        $conn->close();
    }
}

$loop = \React\EventLoop\Factory::create();

$server = new IoServer(
    new HttpServer(
        new WsServer(
            new SshTerminal($loop)
        )
    ),
    new \React\Socket\Server('127.0.0.1:9090', $loop),
    $loop
);

echo "WebSocket server started on localhost:9090\n";
$server->run();
