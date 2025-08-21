<?php
namespace OCA\OwnShell\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use OCP\IConfig;

class AdminController extends Controller {

    private $config;

    public function __construct($AppName, IRequest $request, IConfig $config) {
        parent::__construct($AppName, $request);
        $this->config = $config;
    }

    /**
     * @AdminRequired
     */
    public function save($allowed_hosts, $jwt_secret) {
        $this->config->setAppValue($this->appName, 'allowed_hosts', $allowed_hosts);
        $this->config->setAppValue($this->appName, 'jwt_secret', $jwt_secret);
        return new JSONResponse(['status' => 'success']);
    }
}
