<?php
namespace OCA\nextshell\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\Settings\ISettings;
use OCP\IL10N;

class AdminSettings implements ISettings {

    private IConfig $config;
    private IL10N $l;
    private string $appName;

    public function __construct(string $appName, IL10N $l, IConfig $config) {
        $this->config = $config;
        $this->l = $l;
        $this->appName = $appName;
    }

    /**
     * @return TemplateResponse
     */
    public function getPanel(): TemplateResponse {
        $params = [
            'websocket_port' => $this->config->getAppValue($this->appName, 'websocket_port', 8080),
            'session_timeout' => $this->config->getAppValue($this->appName, 'session_timeout', 3600),
            'idle_timeout' => $this->config->getAppValue($this->appName, 'idle_timeout', 600),
            'proxy_type' => $this->config->getAppValue($this->appName, 'proxy_type', ''),
            'proxy_host' => $this->config->getAppValue($this->appName, 'proxy_host', ''),
            'proxy_port' => $this->config->getAppValue($this->appName, 'proxy_port', ''),
            'proxy_user' => $this->config->getAppValue($this->appName, 'proxy_user', ''),
            'proxy_password' => $this->config->getAppValue($this->appName, 'proxy_password', ''),
        ];
        return new TemplateResponse($this->appName, 'admin', $params);
    }

    public function getSectionID(): string {
        return 'nextshell';
    }

    public function getPriority(): int {
        return 10;
    }
}
