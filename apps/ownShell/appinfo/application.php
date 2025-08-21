<?php
namespace OCA\OwnShell\AppInfo;

use \OCP\AppFramework\App;
use \OCP\IContainer;
use \OCP\AppFramework\IAppContainer;

use OCA\OwnShell\Controller\PageController;
use OCA\OwnShell\Controller\AdminController;
use OCA\OwnShell\Settings\Admin;

class Application extends App {

    public function __construct(array $urlParams = []) {
        parent::__construct('ownshell', $urlParams);
    }

    public function register(IAppContainer $container) {

        /**
         * Controllers
         */
        $container->registerService('PageController', function (IContainer $c) {
            return new PageController(
                $c->query('AppName'),
                $c->query('Request'),
                $c->query('OCP\IURLGenerator'),
                $c->query('OCP\IUserManager'),
                $c->query('OCP\IGroupManager'),
                $c->query('OCP\IConfig')
            );
        });

        $container->registerService('AdminController', function (IContainer $c) {
            return new AdminController(
                $c->query('AppName'),
                $c->query('Request'),
                $c->query('OCP\IConfig')
            );
        });

        /**
         * Admin Settings
         */
        $container->registerService('AdminSettings', function (IContainer $c) {
            return new Admin(
                $c->query('AppName'),
                $c->query('IConfig'),
                $c->query('IURLGenerator')
            );
        });

        /**
         * Routes
         */
        $container->get('OCP\INavigationManager')->add([
            'id' => $this->getId(),
            'order' => 10,
            'href' => $container->get('OCP\IURLGenerator')->linkToRoute('ownshell.page.index'),
            'icon' => $container->get('OCP\IURLGenerator')->imagePath($this->getId(), 'app.svg'),
            'name' => $container->get('OCP\IL10N')->t('ownShell'),
        ]);
    }
}
