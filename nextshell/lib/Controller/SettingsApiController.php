<?php
namespace OCA\nextshell\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use OCP\IAppConfig;
use OCP\IAppManager;
use OCP\IL10N;

class SettingsApiController extends Controller {

    private IAppConfig $config;
    private IAppManager $appManager;
    private IL10N $l;

    public function __construct(string $appName, IRequest $request, IAppConfig $config, IAppManager $appManager, IL10N $l) {
        parent::__construct($appName, $request);
        $this->config = $config;
        $this->appManager = $appManager;
        $this->l = $l;
    }

    private function getPid(): ?int {
        return (int)$this->config->getAppValue($this->appName, 'daemon_pid') ?: null;
    }

    private function setPid(?int $pid): void {
        if ($pid) {
            $this->config->setAppValue($this->appName, 'daemon_pid', $pid);
        } else {
            $this->config->deleteAppValue($this->appName, 'daemon_pid');
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function getStatus(): JSONResponse {
        $pid = $this->getPid();
        if ($pid && posix_kill($pid, 0)) {
            return new JSONResponse(['running' => true, 'pid' => $pid]);
        }
        // If process is not running, ensure PID is cleared
        if ($pid) {
            $this->setPid(null);
        }
        return new JSONResponse(['running' => false]);
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function startDaemon(): JSONResponse {
        if ($this->getStatus()->getData()['running']) {
            return new JSONResponse(['success' => false, 'message' => $this->l->t('Daemon is already running.')], 400);
        }

        $this->config->setAppValue($this->appName, 'daemon_enabled', true);

        $port = $this->config->getAppValue($this->appName, 'websocket_port', 8080);
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

        // Check if port is available
        $socket = @stream_socket_server("tcp://0.0.0.0:$port", $errno, $errstr);
        if (!$socket) {
            return new JSONResponse(['success' => false, 'message' => $this->l->t('Port %s is already in use.', [$port])], 400);
        }
        fclose($socket);

        $daemonScriptPath = $this->appManager->getAppPath($this->appName) . '/bin/daemon.php';
        $phpPath = 'php'; // Assuming php is in PATH

        $command = sprintf(
            'nohup %s %s %d %d %d %s > /dev/null 2>&1 & echo $!',
            $phpPath,
            escapeshellarg($daemonScriptPath),
            $port,
            $idleTimeout,
            $sessionTimeout,
            escapeshellarg($encodedProxyConfig)
        );
        $pid = (int)shell_exec($command);

        if ($pid > 0) {
            $this->setPid($pid);
            // Give it a moment to potentially fail, then check
            sleep(1);
            if (posix_kill($pid, 0)) {
                 return new JSONResponse(['success' => true, 'pid' => $pid]);
            } else {
                $this->setPid(null);
                return new JSONResponse(['success' => false, 'message' => $this->l->t('Failed to start daemon. Check Nextcloud logs for details.')], 500);
            }
        }

        return new JSONResponse(['success' => false, 'message' => $this->l->t('Failed to execute start command.')], 500);
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function stopDaemon(): JSONResponse {
        $this->config->setAppValue($this->appName, 'daemon_enabled', false);
        $pid = $this->getPid();
        if (!$pid || !posix_kill($pid, 0)) {
            $this->setPid(null); // Clear stale PID
            return new JSONResponse(['success' => false, 'message' => $this->l->t('Daemon is not running or PID is stale.')], 400);
        }

        if (posix_kill($pid, SIGTERM)) {
            // Wait a moment for the process to terminate
            sleep(1);
            if (!posix_kill($pid, 0)) {
                $this->setPid(null);
                return new JSONResponse(['success' => true]);
            } else {
                 // Force kill if it didn't terminate
                posix_kill($pid, SIGKILL);
                $this->setPid(null);
                return new JSONResponse(['success' => true, 'message' => $this->l->t('Daemon was forcefully stopped.')]);
            }
        }

        return new JSONResponse(['success' => false, 'message' => $this->l->t('Failed to send stop signal to daemon.')], 500);
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function saveSettings(array $settings): JSONResponse {
        $this->config->setAppValue($this->appName, 'websocket_port', $settings['websocket_port']);
        $this->config->setAppValue($this->appName, 'session_timeout', $settings['session_timeout']);
        $this->config->setAppValue($this->appName, 'idle_timeout', $settings['idle_timeout']);
        $this->config->setAppValue($this->appName, 'proxy_type', $settings['proxy_type']);
        $this->config->setAppValue($this->appName, 'proxy_host', $settings['proxy_host']);
        $this->config->setAppValue($this->appName, 'proxy_port', $settings['proxy_port']);
        $this->config->setAppValue($this->appName, 'proxy_user', $settings['proxy_user']);
        $this->config->setAppValue($this->appName, 'proxy_password', $settings['proxy_password']);

        return new JSONResponse(['success' => true, 'message' => 'Settings saved.']);
    }
}
