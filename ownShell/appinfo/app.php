<?php
namespace OCA\OwnShell\AppInfo;

use OCP\AppFramework\App;

class Application extends App {
    public function __construct(array $urlParams = []) {
        parent::__construct('ownShell', $urlParams);
    }
}
