<?php
namespace OCA\nextshell\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;
use OCP\IAppConfig;

class PageController extends Controller {

    private IAppConfig $config;

    public function __construct(string $appName, IRequest $request, IAppConfig $config) {
        parent::__construct($appName, $request);
        $this->config = $config;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index(): TemplateResponse {
        $port = $this->config->getAppValue($this->appName, 'websocket_port', 8080);

        // Determine WebSocket protocol
        $protocol = $this->request->getServerProtocol() === 'https' ? 'wss' : 'ws';
        $host = $this->request->getServerHost();

        $params = [
            'websocket_url' => sprintf('%s://%s:%d', $protocol, $host, $port)
        ];

        return new TemplateResponse($this->appName, 'main', $params);
    }
}
