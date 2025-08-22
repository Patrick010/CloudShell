<?php
namespace OCA\nextshell\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootable;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCA\nextshell\Controller\PageController;
use OCA\nextshell\Controller\SettingsApiController;
use OCA\nextshell\Settings\AdminSettings;
use OCA\nextshell\Settings\AdminSection;
use OCA\nextshell\BackgroundJob\DaemonMonitor;

class Application extends App implements IBootable {
    public const APP_ID = 'nextshell';

    public function __construct(array $urlParams = []) {
        parent::__construct(self::APP_ID, $urlParams);
    }

    public function boot(IBootContext $context): void {
        parent::boot($context);

        $container = $this->getContainer();

        // Register settings panels
        $container->registerService('OCA\nextshell\Settings\AdminSettings', function ($c) {
            return new AdminSettings(
                $c->get('AppName'),
                $c->get('L10N'),
                $c->get('AppConfig')
            );
        });
        $container->registerService('OCA\nextshell\Settings\AdminSection', function ($c) {
            return new AdminSection(
                $c->get('AppName'),
                $c->get('L10N'),
                $c->get('URLGenerator')
            );
        });

        // Register controllers
        $container->registerService('PageController', function ($c) {
            return new PageController(
                $c->get('AppName'),
                $c->get('Request'),
                $c->get('AppConfig')
            );
        });
        $container->registerService('SettingsApiController', function ($c) {
            return new SettingsApiController(
                $c->get('AppName'),
                $c->get('Request'),
                $c->get('AppConfig'),
                $c->get('AppManager'),
                $c->get('L10N')
            );
        });

        // Register background job
        $container->registerService('OCA\nextshell\BackgroundJob\DaemonMonitor', function($c) {
            return new DaemonMonitor(
                $c->get('AppName'),
                $c->get('AppConfig'),
                $c->get('AppManager'),
                $c->get('Logger'),
                $c->get('L10N')
            );
        });
        $context->getJobList()->add('OCA\nextshell\BackgroundJob\DaemonMonitor');
    }
}
