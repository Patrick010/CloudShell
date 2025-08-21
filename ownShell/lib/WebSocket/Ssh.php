<?php
namespace OCA\OwnShell\WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use phpseclib3\Net\SSH2;
use SplObjectStorage;
use React\EventLoop\LoopInterface;

class Ssh implements MessageComponentInterface {
    protected $clients;
    protected $loop;

    public function __construct(LoopInterface $loop) {
        $this->clients = new SplObjectStorage;
        $this->loop = $loop;
        echo "ownShell SSH WebSocket server started.\n";
    }

    public function onOpen(ConnectionInterface $conn) {
        echo "Attempting SSH connection for {$conn->resourceId}...\n";

        // IMPORTANT: This assumes an SSH server is running on localhost
        // with a user 'user' and password 'password'.
        // This will need to be configured in the testing environment.
        $ssh = new SSH2('127.0.0.1');
        if (!$ssh->login('user', 'password')) {
            echo "SSH Login Failed for {$conn->resourceId}.\n";
            $conn->send("Error: SSH connection failed. Check server configuration.\n");
            $conn->close();
            return;
        }

        echo "SSH Login successful for {$conn->resourceId}.\n";

        $ssh->enablePTY();
        $ssh->read(); // Read banner

        // Attach the SSH object to the connection
        $this->clients->attach($conn, $ssh);

        // Poll for new output from the shell
        $timer = $this->loop->addPeriodicTimer(0.05, function() use ($conn, $ssh) {
            if ($this->clients->contains($conn)) {
                $output = $ssh->read();
                if ($output) {
                    $conn->send($output);
                }
            }
        });

        $conn->on('close', function() use ($timer) {
            $this->loop->cancelTimer($timer);
        });

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        if ($this->clients->contains($from)) {
            $ssh = $this->clients[$from];
            $ssh->write($msg);
        }
    }

    public function onClose(ConnectionInterface $conn) {
        if ($this->clients->contains($conn)) {
            $ssh = $this->clients[$conn];
            $ssh->disconnect();
            $this->clients->detach($conn);
        }
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        if ($this->clients->contains($conn)) {
            $ssh = $this->clients[$conn];
            $ssh->disconnect();
            $this->clients->detach($conn);
        }
        $conn->close();
    }
}
