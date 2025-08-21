<?php
namespace OCA\OwnShell\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;
use OCP\IURLGenerator;
use OCP\IUserManager;
use OCP\IGroupManager;
use OCP\IConfig;
use Firebase\JWT\JWT;

class PageController extends Controller {

    private $urlGenerator;
    private $userManager;
    private $groupManager;
    private $config;
    private $userId;

    public function __construct($AppName, IRequest $request, IURLGenerator $urlGenerator, IUserManager $userManager, IGroupManager $groupManager, IConfig $config) {
        parent::__construct($AppName, $request);
        $this->urlGenerator = $urlGenerator;
        $this->userManager = $userManager;
        $this->groupManager = $groupManager;
        $this->config = $config;
        $this->userId = $this->userManager->getLoggedInUser()->getUID();
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index() {
        // Security Check: Only users in the 'ssh_users' group can access this page.
        $sshGroup = $this->groupManager->get('ssh_users');
        if (!$sshGroup || !$sshGroup->inGroup($this->userManager->get($this->userId))) {
            return new TemplateResponse('core', '403', ['message' => 'You are not authorized to use the SSH terminal.'], 'guest');
        }

        $allowedHosts = $this->config->getAppValue($this->appName, 'allowed_hosts', '');
        $hostsArray = !empty($allowedHosts) ? array_map('trim', explode(',', $allowedHosts)) : [];
        $jwtSecret = $this->config->getAppValue($this->appName, 'jwt_secret', 'your-super-secret-key-that-is-at-least-32-bytes-long');

        $tokens = [];
        foreach ($hostsArray as $host) {
            $payload = [
                'iss' => 'ownCloud',
                'aud' => 'ownShell',
                'iat' => time(),
                'exp' => time() + 3600, // Token valid for 1 hour
                'user' => $this->userId,
                'host' => $host,
                'pass' => 'password' // Placeholder
            ];
            $tokens[$host] = JWT::encode($payload, $jwtSecret, 'HS256');
        }

        $params = [
            'hosts' => $hostsArray,
            'tokens' => $tokens,
        ];

        return new TemplateResponse($this->appName, 'main', $params);
    }
}
