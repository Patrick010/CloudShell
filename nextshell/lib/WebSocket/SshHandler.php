<?php
namespace OCA\nextshell\WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use phpseclib3\Net\SSH2;
use phpseclib3\Exception\UnableToConnectException;
use React\EventLoop\Loop;

class SshHandler implements MessageComponentInterface {
    protected $clients;
    private int $idleTimeout;
    private int $sessionTimeout;
    private array $proxyConfig;

    public function __construct(int $idleTimeout, int $sessionTimeout, array $proxyConfig) {
        $this->clients = new \SplObjectStorage;
        $this->idleTimeout = $idleTimeout;
        $this->sessionTimeout = $sessionTimeout;
        $this->proxyConfig = $proxyConfig;
        echo "SshHandler initialized.\n";
        echo "Idle Timeout: {$this->idleTimeout}s, Session Timeout: {$this->sessionTimeout}s\n";
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn, new \stdClass);
        $this->clients[$conn]->state = 'awaiting_init';
        $this->clients[$conn]->last_activity = time();
        $this->clients[$conn]->session_start_time = time();
        $this->clients[$conn]->ssh = null;

        $conn->send(json_encode(['type' => 'status', 'message' => 'Connection established. Please provide connection details.']));
        echo "Connection {$conn->resourceId} opened.\n";

        // Start session timeout check if enabled
        if ($this->sessionTimeout > 0) {
            $this->setupSessionTimeout($conn);
        }
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $this->clients[$from]->last_activity = time();
        $data = json_decode($msg, true);
        $state = $this->clients[$from]->state;

        echo "Message from {$from->resourceId} in state {$state}: $msg\n";

        switch ($state) {
            case 'awaiting_init':
                if (isset($data['type']) && $data['type'] === 'connect' && isset($data['target'])) {
                    $this->startSshConnection($from, $data['target']);
                }
                break;
            case 'awaiting_password':
                if (isset($data['type']) && $data['type'] === 'auth' && isset($data['password'])) {
                    $this->attemptLogin($from, $data['password']);
                }
                break;
            case 'authenticated':
                 if (isset($data['type'])) {
                    if ($data['type'] === 'data' && isset($data['payload'])) {
                        $this->clients[$from]->ssh->write($data['payload']);
                    } elseif ($data['type'] === 'resize' && isset($data['rows']) && isset($data['cols'])) {
                        $this->clients[$from]->ssh->setWindowSize($data['cols'], $data['rows']);
                    }
                }
                break;
        }
    }

    private function startSshConnection(ConnectionInterface $conn, string $target) {
        [$user, $host] = explode('@', $target, 2);
        if (!$user || !$host) {
            $conn->send(json_encode(['type' => 'error', 'message' => 'Invalid target format. Use user@host.']));
            $conn->close();
            return;
        }

        try {
            $ssh = new SSH2($host);
            $this->clients[$conn]->ssh = $ssh;
            $this->clients[$conn]->state = 'awaiting_password';
            $conn->send(json_encode(['type' => 'prompt_password']));
            echo "SSH connection initiated for {$conn->resourceId} to $target.\n";
        } catch (UnableToConnectException $e) {
            $conn->send(json_encode(['type' => 'error', 'message' => "Failed to connect to {$host}: " . $e->getMessage()]));
            $conn->close();
        }
    }

    private function attemptLogin(ConnectionInterface $conn, string $password) {
        /** @var SSH2 $ssh */
        $ssh = $this->clients[$conn]->ssh;
        if ($ssh->login($ssh->getUsername(), $password)) {
            $this->clients[$conn]->state = 'authenticated';
            $conn->send(json_encode(['type' => 'auth_success']));
            $ssh->read(); // Clear banner
            $ssh->setWindowSize(80, 24); // Default size
            $ssh->enablePTY();
            $ssh->exec('echo "Welcome to NextShell!"'); // Start the shell

            // Setup periodic reader
            $this->setupStreamReader($conn);
            // Setup idle timeout
            if ($this->idleTimeout > 0) {
                $this->setupIdleTimeout($conn);
            }
            echo "Login successful for {$conn->resourceId}.\n";
        } else {
            $conn->send(json_encode(['type' => 'error', 'message' => 'Authentication failed.']));
            $conn->close();
        }
    }

    private function setupStreamReader(ConnectionInterface $conn) {
        $ssh = $this->clients[$conn]->ssh;
        Loop::addPeriodicTimer(0.01, function ($timer) use ($conn, $ssh) {
            if (!$this->clients->contains($conn) || !$ssh->isConnected()) {
                Loop::cancelTimer($timer);
                return;
            }
            $output = $ssh->read();
            if (!empty($output)) {
                $conn->send(json_encode(['type' => 'data', 'payload' => $output]));
            }
        });
    }

    private function setupIdleTimeout(ConnectionInterface $conn) {
        Loop::addPeriodicTimer(10, function ($timer) use ($conn) {
            if (!$this->clients->contains($conn)) {
                Loop::cancelTimer($timer);
                return;
            }
            if (time() - $this->clients[$conn]->last_activity > $this->idleTimeout) {
                echo "Connection {$conn->resourceId} timed out due to inactivity.\n";
                $conn->send(json_encode(['type' => 'status', 'message' => 'Session terminated due to inactivity.']));
                $conn->close();
                Loop::cancelTimer($timer);
            }
        });
    }

    private function setupSessionTimeout(ConnectionInterface $conn) {
        Loop::addTimer($this->sessionTimeout, function() use ($conn) {
            if ($this->clients->contains($conn)) {
                echo "Connection {$conn->resourceId} timed out due to session limit.\n";
                $conn->send(json_encode(['type' => 'status', 'message' => 'Session terminated due to maximum duration.']));
                $conn->close();
            }
        });
    }

    public function onClose(ConnectionInterface $conn) {
        if ($this->clients->contains($conn)) {
            if ($this->clients[$conn]->ssh instanceof SSH2 && $this->clients[$conn]->ssh->isConnected()) {
                $this->clients[$conn]->ssh->disconnect();
            }
            $this->clients->detach($conn);
        }
        echo "Connection {$conn->resourceId} has disconnected.\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->send(json_encode(['type' => 'error', 'message' => 'A server error occurred: ' . $e->getMessage()]));
        $conn->close();
    }
}
