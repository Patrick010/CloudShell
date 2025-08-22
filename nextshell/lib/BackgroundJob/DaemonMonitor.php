<?php
namespace OCA\nextshell\BackgroundJob;

use OCP\BackgroundJob\Job;
use OCP\IAppConfig;
use OCP\IAppManager;
use OCP\ILogger;
use OCP\IL10N;

class DaemonMonitor extends Job {

    private string $appName;
    private IAppConfig $config;
    private IAppManager $appManager;
    private ILogger $logger;
    private IL10N $l;

    public function __construct(string $appName, IAppConfig $config, IAppManager $appManager, ILogger $logger, IL10N $l) {
        $this->appName = $appName;
        $this->config = $config;
        $this->appManager = $appManager;
        $this->logger = $logger;
        $this->l = $l;
    }

    protected function run($argument) {
        $this->logger->debug("Running NextShell DaemonMonitor job", ['app' => $this->appName]);

        $isEnabled = $this->config->getAppValue($this->appName, 'daemon_enabled', false);
        if (!$isEnabled) {
            $this->logger->debug("Daemon is disabled, skipping check.", ['app' => $this->appName]);
            return;
        }

        $pid = (int)$this->config->getAppValue($this->appName, 'daemon_pid');
        if ($pid && posix_kill($pid, 0)) {
            $this->logger->debug("Daemon is running with PID: $pid. All good.", ['app' => $this->appName]);
            return;
        }

        $this->logger->warning("NextShell daemon is not running, but it is enabled. Attempting to restart.", ['app' => $this->appName]);

        $port = $this->config->getAppValue($this->appName, 'websocket_port', 8080);
        $socket = @stream_socket_server("tcp://0.0.0.0:$port", $errno, $errstr, STREAM_SERVER_BIND);
        if (!$socket) {
            $this->logger->error("Could not restart daemon: Port $port is in use.", ['app' => $this->appName]);
            return;
        }
        fclose($socket);

        $daemonScriptPath = $this->appManager->getAppPath($this->appName) . '/bin/daemon.php';
        $phpPath = 'php';

        $idleTimeout = $this->config->getAppValue($this->appName, 'idle_timeout', 600);
        $sessionTimeout = $this->config->getAppValue($this->appName, 'session_timeout', 3600);
        $proxyConfig = [
            'type' => $this->config->getAppValue($this->appName, 'proxy_type', ''),
            'host' => $this->config->getAppValue($this->appName, 'proxy_host', ''),
            'port' => $this->config->getAppValue($this->appName, 'proxy_port', ''),
            'user' => $this->config->getAppValue($this->appName, 'proxy_user', ''),
            'password' => $this->config->getAppValue($this->appName, 'proxy_password', ''),
        ];
        $encodedProxyConfig = base64_encode(json_encode($proxyConfig));

        $command = sprintf(
            'nohup %s %s %d %d %d %s > /dev/null 2>&1 & echo $!',
            $phpPath,
            escapeshellarg($daemonScriptPath),
            $port,
            $idleTimeout,
            $sessionTimeout,
            escapeshellarg($encodedProxyConfig)
        );

        $newPid = (int)shell_exec($command);

        if ($newPid > 0) {
            $this->config->setAppValue($this->appName, 'daemon_pid', $newPid);
            $this->logger->info("NextShell daemon restarted successfully with new PID: $newPid", ['app' => $this->appName]);
        } else {
            $this->logger->error("Failed to restart NextShell daemon. The command failed to execute.", ['app' => $this->appName]);
        }
    }
}
