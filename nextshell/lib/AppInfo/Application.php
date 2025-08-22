<?php
namespace OCA\nextshell\AppInfo;

use OCP\AppFramework\App;
use OCA\nextshell\BackgroundJob\DaemonMonitor;
use OCA\nextshell\Controller\PageController;
use OCA\nextshell\Controller\SettingsApiController;
use OCA\nextshell\Settings\AdminSection;
use OCA\nextshell\Settings\AdminSettings;

class Application extends App {
    public const APP_ID = 'nextshell';

    public function __construct(array $urlParams = []) {
        parent::__construct(self::APP_ID, $urlParams);

        $container = $this->getContainer();

        // Register settings panels
        $container->registerService(AdminSettings::class, function ($c) {
            return new AdminSettings(
                $c->get('AppName'),
                $c->get('L10N'),
                $c->get('AppConfig')
            );
        });
        $container->registerService(AdminSection::class, function ($c) {
            return new AdminSection(
                $c->get('AppName'),
                $c->get('L10N'),
                $c->get('URLGenerator')
            );
        });

        // Register controllers
        $container->registerService(PageController::class, function ($c) {
            return new PageController(
                $c->get('AppName'),
                $c->get('Request'),
                $c->get('AppConfig')
            );
        });
        $container->registerService(SettingsApiController::class, function ($c) {
            return new SettingsApiController(
                $c->get('AppName'),
                $c->get('Request'),
                $c->get('AppConfig'),
                $c->get('AppManager'),
                $c->get('L10N')
            );
        });

        // Register background job
        $container->registerService(DaemonMonitor::class, function ($c) {
            return new DaemonMonitor(
                $c->get('AppName'),
                $c->get('AppConfig'),
                $c->get('AppManager'),
                $c->get('Logger'),
                $c->get('L10N')
            );
        });

        $jobList = \OC::$server->getBackgroundJobList();
        $jobList->add(DaemonMonitor::class);
    }
}
