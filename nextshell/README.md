# NextShell - A Web-Based SSH Terminal for Nextcloud

NextShell is a Nextcloud application that provides a simple, web-based SSH terminal, allowing users to connect to SSH servers directly from the Nextcloud interface. The entire application is managed from the Nextcloud Web UI, with no command-line interaction required for day-to-day use after initial installation.

## Features

- **Web-based SSH:** Access your SSH servers from anywhere, using only your web browser.
- **UI-Driven Management:** The WebSocket daemon that powers the terminal is started, stopped, and configured entirely from the Nextcloud Admin settings.
- **Resilient by Design:** The application includes a background job to automatically restart the daemon if it crashes or after a server reboot.
- **Configurable:** Set the WebSocket port, session timeouts, and optional outbound proxy settings.

---

## For Nextcloud Administrators: Installation and Usage

These instructions assume you have received the `nextshell-1.0.0.zip` package file.

### Installation

Installing NextShell involves manually placing the application code into your Nextcloud server's `apps` directory.

1.  Download the latest release package (e.g., `nextshell-1.0.0.zip`).
2.  Access your Nextcloud server's command line.
3.  Navigate to your Nextcloud installation's root directory.
4.  Extract the contents of the zip file into the `apps/` subdirectory. For example: `unzip /path/to/nextshell-1.0.0.zip -d /path/to/nextcloud/apps/`
5.  This may create a directory named `nextshell-1.0.0`. If so, rename it to `nextshell`. The final path should be `nextcloud/apps/nextshell`.
6.  Ensure the file permissions are correct. The web server user (e.g., `www-data`) must have read access to the app files. You can set this by running `chown -R www-data:www-data /path/to/nextcloud/apps/nextshell`.
7.  In your Nextcloud UI, navigate to the **Apps** page.
8.  Go to the **App bundles** section, find **NextShell**, and click **Enable**.

### First-Time Setup

1.  After enabling the app, navigate to **Administration Settings**.
2.  In the left-hand navigation pane, find the **NextShell** entry under the "Administration" section.
3.  On the NextShell settings page, review the default settings (like the WebSocket port).
4.  Click the **Start Daemon** button. The status indicator should change to "Running".
5.  **Disclaimer:** The WebSocket daemon process is managed by a Nextcloud background job. While it will attempt to restart on failure or reboot, its operation depends on your server's Nextcloud cron job being configured and running correctly.

### Using the Terminal

1.  Once the daemon is running, a new **SSH Terminal** icon will appear in your main Nextcloud navigation bar at the top of the page.
2.  Click this icon to open the terminal.
3.  You will be prompted to enter a connection string in the format `user@hostname`.
4.  You will then be prompted for your password.
5.  You are now connected!

---

## For Developers: Packaging the Application

These instructions are for developers who have cloned the source code and need to build the final `nextshell-1.0.0.zip` package.

### Prerequisites

You must have the following command-line tools installed and available in your system's PATH:
- `php` (version 8.1 or newer)
- `composer` (PHP dependency manager)
- `zip` (for creating the archive)

### Building the Package

1.  Clone this repository to your local machine.
2.  Open a terminal and navigate to the root directory of the cloned repository.
3.  Make the packaging script executable: `chmod +x package.sh`
4.  Run the script: `./package.sh`

The script will perform the following actions:
- Install all necessary PHP dependencies using Composer into the `nextshell/vendor` directory.
- Create the final `nextshell-1.0.0.zip` archive in the repository root.

You can now distribute this zip file for installation on a Nextcloud server.
