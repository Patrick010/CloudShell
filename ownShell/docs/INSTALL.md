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
