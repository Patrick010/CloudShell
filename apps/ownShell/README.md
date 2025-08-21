# ownShell for ownCloud

ownShell is an ownCloud application that provides a secure, web-based SSH terminal directly within the ownCloud interface. It allows authorized users to connect to a pre-approved list of servers, enhancing productivity for administrators and developers.

## Features

-   In-browser SSH terminal powered by xterm.js.
-   Secure, token-based authentication (JWT).
-   Centralized user control via ownCloud groups (`ssh_users`).
-   Admin-configurable list of allowed hosts.
-   Persistent terminal daemon that runs as a system service.

## Installation and Configuration

Installing ownShell involves three main parts: installing the ownCloud app, configuring the WebSocket daemon service, and setting up your web server to proxy WebSocket connections.

### Step 1: Install the ownCloud App

1.  Place the `ownShell` directory inside your ownCloud `apps/` directory.
2.  Enable the app using the `occ` command:
    ```bash
    sudo -u www-data php /path/to/owncloud/occ app:enable ownshell
    ```

### Step 2: Configure and Start the WebSocket Daemon

The terminal daemon is a background process that handles the SSH connections. It needs to be run as a persistent service using `systemd`.

1.  **Copy the Service File**: A template for the service file is provided in the root of this repository (`owncloud-ssh-ws.service.template`). Copy this file to your system's `systemd` directory.
    ```bash
    sudo cp /path/to/owncloud-ssh-ws.service.template /etc/systemd/system/owncloud-ssh-ws.service
    ```
2.  **Edit the Service File**: Open the new service file and ensure the `ExecStart` path is correct for your server's environment.
    ```
    ExecStart=/usr/bin/php /var/www/owncloud/apps/ownShell/bin/ssh-ws-daemon.php
    ```
3.  **Enable and Start the Service**: Reload the systemd daemon, enable the new service to start on boot, and start it now.
    ```bash
    sudo systemctl daemon-reload
    sudo systemctl enable --now owncloud-ssh-ws.service
    ```
4.  **Check Status**: You can check that the service is running correctly with:
    ```bash
    sudo systemctl status owncloud-ssh-ws.service
    ```

### Step 3: Configure Web Server Reverse Proxy

The ownCloud app communicates with the daemon via a WebSocket. Your web server (Nginx or Apache) must be configured to proxy requests from `/apps/ownshell/ws/` to the daemon running on `localhost:9090`.

**For Nginx:**

Add the following `location` block to your ownCloud server block configuration:

```nginx
location /apps/ownshell/ws/ {
    proxy_pass http://127.0.0.1:9090/;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "upgrade";
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
}
```

**For Apache:**

Ensure you have `mod_proxy` and `mod_proxy_wstunnel` enabled. Then, add the following to your ownCloud virtual host configuration:

```apache
ProxyPass /apps/ownshell/ws/ ws://127.0.0.1:9090/
ProxyPassReverse /apps/ownshell/ws/ ws://127.0.0.1:9090/
```

After modifying your web server configuration, **restart the web server**.

### Step 4: Final ownCloud Configuration

1.  **Create User Group**: As an ownCloud admin, navigate to **Users** and create a new group named `ssh_users`. Add any users you want to grant SSH access to this group.
2.  **Configure Allowed Hosts**: Navigate to **Settings -> Admin -> ownShell**.
    *   In the "Allowed Hosts" text area, enter a comma-separated list of hostnames or IP addresses that users can connect to (e.g., `server1.example.com, 192.168.1.100`).
    *   Ensure the "JWT Secret Key" matches the secret you have in `ssh-ws-daemon.php` (if you changed it from the default).
    *   Click **Save**.

The installation is now complete. Authorized users will see the "ownShell" icon in the main navigation menu and can start using the terminal.
