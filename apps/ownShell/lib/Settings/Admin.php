<?php
namespace OCA\OwnShell\Settings;

use OCP\Settings\ISettings;
use OCP\IConfig;
use OCP\IURLGenerator;
use OCP\AppFramework\Http\TemplateResponse;

class Admin implements ISettings {

    private $config;
    private $urlGenerator;
    private $appName;

    public function __construct($AppName, IConfig $config, IURLGenerator $urlGenerator) {
        $this->appName = $AppName;
        $this->config = $config;
        $this->urlGenerator = $urlGenerator;
    }

    public function getForm() {
        $params = [
            'allowed_hosts' => $this->config->getAppValue($this->appName, 'allowed_hosts', 'localhost'),
            'jwt_secret' => $this->config->getAppValue($this->appName, 'jwt_secret', 'your-super-secret-key-that-is-at-least-32-bytes-long')
        ];
        return new TemplateResponse($this->appName, 'admin', $params);
    }

    public function getSection() {
        return 'ownshell'; // The section ID
    }

    public function getPriority() {
        return 10;
    }
}
