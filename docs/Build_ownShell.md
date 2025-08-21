# Task: Build, Harden, Test, and Package ownShell for Production

Jules, your task is to **fully build a hardened, production-ready ownShell plugin** for ownCloud. You must execute everything; no preparation or partial work. Deliverables: `ownShell-1.0.0.zip`, `ownShell-1.0.0.tar.gz`, fully documented and production-ready.

---

## **1. Environment Setup**

Ensure you have:

- PHP 8.2+ with Composer
- Node.js 18+
- Git
- Zip and tar utilities

---

## **2. Folder Structure**

Create `ownShell/` with the following subfolders:

ownShell/
├── appinfo/
├── bin/
├── css/
├── js/
├── lib/{Controller,Service,WebSocket}
├── templates/
├── docs/
├── tests/{phpunit,jest}
└── composer.json


---

## **3. File Contents**

### **appinfo/info.xml**
```xml
<?xml version="1.0"?>
<info>
  <id>ownShell</id>
  <name>ownShell</name>
  <summary>Web-based SSH terminal for ownCloud</summary>
  <description>Allows users to connect via SSH to LAN servers through ownCloud browser interface.</description>
  <version>1.0.0</version>
  <licence>AGPL</licence>
  <author>Your Name</author>
  <namespace>OwnShell</namespace>
  <category>tools</category>
  <owncloud min-version="10.0" max-version="10.11"/>
</info>

appinfo/app.php

<?php
namespace OCA\OwnShell\AppInfo;

use OCP\AppFramework\App;

class Application extends App {
    public function __construct(array $urlParams = []) {
        parent::__construct('ownShell', $urlParams);
    }
}

bin/ssh-ws-daemon.php

#!/usr/bin/env php
<?php
require __DIR__ . '/../vendor/autoload.php';
echo "ownShell WebSocket daemon starting...\n";
// TODO: Initialize ReactPHP/Ratchet server and phpseclib SSH handling

Make executable: chmod +x bin/ssh-ws-daemon.php
js/terminal.js

document.addEventListener('DOMContentLoaded', function() {
    const term = new Terminal();
    term.open(document.getElementById('terminal'));
    term.write('Welcome to ownShell SSH Terminal\r\n');
});

templates/main.php

<div id="terminal" style="width:100%; height:500px; background-color:black;"></div>

docs/INSTALL.md

# ownShell Installation Guide

## Requirements
- ownCloud 10.0 - 10.11
- PHP 8.2+
- Composer installed
- Node.js 18+

## Installation
1. Copy `ownShell` to `/apps/`.
2. Enable the app:
```bash
occ app:enable ownShell

    Set up the WebSocket daemon (systemd):

[Unit]
Description=ownShell WebSocket Daemon
After=network.target

[Service]
ExecStart=/usr/bin/php /var/www/owncloud/apps/ownShell/bin/ssh-ws-daemon.php
Restart=always
User=www-data
Group=www-data

[Install]
WantedBy=multi-user.target

    Enable and start:

systemctl enable --now owncloud-ssh-ws.service

    Verify daemon:

systemctl status owncloud-ssh-ws.service


### **docs/USAGE.md**
```markdown
# ownShell User Guide

1. Log into ownCloud.
2. Open **SSH Terminal** in the sidebar.
3. Select a whitelisted host.
4. Click **Connect**.

Authentication:
- Password or key-based.
- Idle timeout disconnects sessions.
- Session logs are recorded.

docs/ADMIN.md

# ownShell Admin Guide

## User Access
- Only users in allowed groups can access SSH terminal.

## Whitelisted Hosts
- Admins define accessible hosts.

## WebSocket Daemon
- Manage via systemd or Admin UI.
- TLS recommended for wss:// connections.

## Security
- Idle timeout configurable.
- Session logs include user, host, start/end times.

composer.json

{
  "name": "yourname/ownShell",
  "description": "Web-based SSH terminal for ownCloud",
  "type": "owncloud-app",
  "require": {
    "php": ">=8.2",
    "phpseclib/phpseclib": "^3.0",
    "react/socket": "^1.12",
    "cboden/ratchet": "^0.4"
  },
  "autoload": {
    "psr-4": {
      "OCA\\OwnShell\\": "lib/"
    }
  }
}

4. Hardening and Linting
PHP

    phpcs --standard=PSR12 apps/ownShell/

    php-cs-fixer fix --config=.php_cs.dist

    Static analysis:

        vendor/bin/phpstan analyse apps/ownShell/ --level=max

        vendor/bin/psalm

    Sanitize all user inputs.

    Enforce host whitelists, idle timeouts, TLS for WebSocket.

JS

    npx eslint js/

    npx prettier --write js/

5. Testing

    PHP: vendor/bin/phpunit --configuration phpunit.xml

    JS: npm test

    Integration tests:

        WebSocket connection

        SSH session

        Access control and idle timeout

6. Packaging

Create build_ownShell_package.sh:

#!/bin/bash
set -e
zip -r ownShell-1.0.0.zip ownShell/
tar -czf ownShell-1.0.0.tar.gz ownShell/
echo "✅ Packages created: ownShell-1.0.0.zip and ownShell-1.0.0.tar.gz"

Run it to produce distributables.
7. Verification

    Install on clean ownCloud:

cp ownShell-1.0.0.zip /var/www/owncloud/apps/
occ app:enable ownShell
systemctl enable --now owncloud-ssh-ws.service

    Confirm:

    Plugin appears in UI

    SSH terminal works

    Logs record sessions correctly

8. Deliverables

    ownShell-1.0.0.zip

    ownShell-1.0.0.tar.gz

    All documentation files

    Verified plugin on clean ownCloud install

Execute all steps yourself. The plugin must be production-ready, fully hardened, linted, tested, and packaged for marketplace submission.