# ownShell - Web-based SSH Terminal for ownCloud

ownShell is an ownCloud app that allows users to connect to SSH servers on the local network directly from the ownCloud web interface.

## Features

-   Web-based SSH terminal powered by xterm.js.
-   Connect to whitelisted SSH servers.
-   Password and key-based authentication (as per `phpseclib` capabilities).
-   Session logging and idle timeouts for security.

## Requirements

-   ownCloud 10.0 - 10.11
-   PHP 8.2+ with Composer
-   Node.js 18+

---

## Installation

1.  **Copy the App:**
    Copy the `ownShell` app directory into your ownCloud `apps/` directory.

2.  **Enable the App:**
    Use the `occ` command to enable the app:
    ```bash
    cd /path/to/your/owncloud
    sudo -u www-data php occ app:enable ownShell
    ```

3.  **Set up the WebSocket Daemon:**
    The terminal relies on a WebSocket daemon to bridge the connection to the SSH server. It needs to be run as a persistent background service. The recommended way is to use `systemd`.

    Create a new service file:
    ```bash
    sudo nano /etc/systemd/system/owncloud-ssh-ws.service
    ```

    Paste the following content into the file. **Make sure to adjust the `ExecStart` path to match your ownCloud installation path.**

    ```ini
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
    ```

4.  **Enable and Start the Daemon:**
    ```bash
    sudo systemctl enable --now owncloud-ssh-ws.service
    ```

5.  **Verify the Daemon:**
    Check the status of the service to ensure it is running correctly:
    ```bash
    sudo systemctl status owncloud-ssh-ws.service
    ```

---

## Usage

1.  Log into your ownCloud account.
2.  Navigate to the "SSH Terminal" entry in the sidebar.
3.  The terminal will open, and you can start using the SSH session.

**Note:** This is a Proof of Concept. The current version connects to a hardcoded SSH server (`user@127.0.0.1`). Future versions will allow administrators to configure whitelisted hosts and user access.

---

## Admin Configuration

(For future versions)

-   **User Access:** Limit access to specific groups.
-   **Whitelisted Hosts:** Define the list of allowed SSH servers.
-   **Security:** Configure idle timeouts and manage session logs.
