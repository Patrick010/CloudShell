# Task: Build and Package ownShell for Marketplace

Jules, your task is to **fully build, package, and prepare the ownShell plugin** for ownCloud marketplace submission. This includes generating all files, folder structure, and producing both ZIP and tar.gz distributables. You must execute everything — do not leave it in preparation.

---

## **Steps to Execute**

1. **Create the build script**

Copy the following script into a file named `build_ownShell_package.sh`:

```bash
#!/bin/bash
set -e

# Base directory and output files
BASE_DIR="ownShell"
ZIP_FILE="ownShell-1.0.0.zip"
TAR_FILE="ownShell-1.0.0.tar.gz"

# Clean previous builds
rm -rf "$BASE_DIR" "$ZIP_FILE" "$TAR_FILE"

# Create folder structure
mkdir -p "$BASE_DIR"/{appinfo,bin,css,js,lib/{Controller,Service,WebSocket},templates,docs,tests/{phpunit,jest}}

# -----------------------
# appinfo/info.xml
cat > "$BASE_DIR/appinfo/info.xml" <<'EOF'
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
EOF

# appinfo/app.php
cat > "$BASE_DIR/appinfo/app.php" <<'EOF'
<?php
namespace OCA\OwnShell\AppInfo;

use OCP\AppFramework\App;

class Application extends App {
    public function __construct(array $urlParams = []) {
        parent::__construct('ownShell', $urlParams);
    }
}
EOF

# bin/ssh-ws-daemon.php
cat > "$BASE_DIR/bin/ssh-ws-daemon.php" <<'EOF'
#!/usr/bin/env php
<?php
require __DIR__ . '/../vendor/autoload.php';
echo "ownShell WebSocket daemon starting...\n";
// TODO: Initialize ReactPHP/Ratchet server and phpseclib SSH handling
EOF
chmod +x "$BASE_DIR/bin/ssh-ws-daemon.php"

# js/terminal.js
cat > "$BASE_DIR/js/terminal.js" <<'EOF'
// xterm.js integration placeholder
document.addEventListener('DOMContentLoaded', function() {
    const term = new Terminal();
    term.open(document.getElementById('terminal'));
    term.write('Welcome to ownShell SSH Terminal\r\n');
});
EOF

# templates/main.php
cat > "$BASE_DIR/templates/main.php" <<'EOF'
<div id="terminal" style="width:100%; height:500px; background-color:black;"></div>
EOF

# docs/INSTALL.md
cat > "$BASE_DIR/docs/INSTALL.md" <<'EOF'
# ownShell Installation Guide

## Requirements
- ownCloud 10.0 - 10.11
- PHP 8.2+
- Composer installed
- Node.js 18+

## Manual Installation
1. Copy `ownShell` folder to `/apps/`.
2. Enable the app:
   ```bash
   occ app:enable ownShell

    Set up WebSocket daemon via systemd:

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

EOF
docs/USAGE.md

cat > "$BASE_DIR/docs/USAGE.md" <<'EOF'
ownShell User Guide
Accessing the SSH Terminal

    Log into ownCloud.

    Click SSH Terminal in the sidebar.

    Select a host from the dropdown (whitelisted by admin).

    Click Connect.

Authentication

    Password or key-based authentication as configured.

    Idle timeout enforced; session disconnects after inactivity.

    Session logs recorded for auditing.
    EOF

docs/ADMIN.md

cat > "$BASE_DIR/docs/ADMIN.md" <<'EOF'
ownShell Administration Guide
User Access

    Only users in allowed groups can use SSH Terminal.

Whitelisted Hosts

    Admins define which hosts can be connected.

WebSocket Daemon

    Manage via systemd or Admin UI.

    TLS recommended for wss://.

Security & Logging

    Idle timeout configurable.

    Session logs capture user, host, start/end times.
    EOF

composer.json

cat > "$BASE_DIR/composer.json" <<'EOF'
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
"OCA\OwnShell\": "lib/"
}
}
}
EOF
-----------------------
Create ZIP

zip -r "$ZIP_FILE" "$BASE_DIR"
Create tar.gz

tar -czf "$TAR_FILE" "$BASE_DIR"

echo "✅ ownShell packages created: $ZIP_FILE and $TAR_FILE"


---

2. **Make the script executable**:
```bash
chmod +x build_ownShell_package.sh

    Run the script:

./build_ownShell_package.sh

Outcome

    ownShell-1.0.0.zip → ready for download / Marketplace submission.

    ownShell-1.0.0.tar.gz → Linux-friendly package.

    All documentation (INSTALL.md, USAGE.md, ADMIN.md) included.

    Folder structure and stubs for PHP, JS, templates, and bin daemon created.