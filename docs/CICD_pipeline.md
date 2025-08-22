Jules, your task is to fully implement and execute a CI/CD pipeline for the "ownShell" ownCloud plugin. This is not just a preparation; you must run it end-to-end and ensure it is operational. Follow these steps and complete each one:

1. **Environment Setup**
   - Ensure a PHP 8.2+ environment with Composer installed.
   - Ensure Node.js 18+ environment is ready for frontend tooling.

2. **Install Dependencies**
   - PHP: Run `composer install --no-dev --prefer-dist` inside `apps/ownShell/`.
   - JS: Run `npm install` inside the frontend directory.

3. **Linting & Formatting**
   - PHP:
     - Run `phpcs --standard=PSR12 apps/ownShell/` and fix errors.
     - Run `php-cs-fixer fix --config=.php_cs.dist`.
   - JS:
     - Run `npx eslint js/` and fix errors.
     - Run `npx prettier --write js/`.

4. **Static Analysis**
   - Run `vendor/bin/phpstan analyse apps/ownShell/ --level=max` and resolve all issues.
   - Run `vendor/bin/psalm` and fix warnings/errors.

5. **Unit & Integration Tests**
   - PHP: Execute `vendor/bin/phpunit --configuration phpunit.xml` and ensure all tests pass.
   - JS: Execute `npm test` (Jest or Cypress) and ensure all tests pass.

6. **Security Audits**
   - PHP: Run `composer audit` and fix any high or moderate severity issues.
   - JS: Run `npm audit --audit-level=moderate` and fix vulnerabilities.

7. **Build & Package**
   - Package the plugin into `ownShell.tar.gz`.
   - Ensure systemd unit file and WebSocket daemon bootstrap script are included.
   - Verify package integrity.

8. **Deployment**
   - Copy `ownShell.tar.gz` to the ownCloud server.
   - Enable the app with `occ app:enable ownShell`.
   - Start and enable the WebSocket daemon service: `systemctl enable --now owncloud-ssh-ws.service`.
   - Verify the daemon is running and listening on the configured WebSocket port.

9. **Verification**
   - Open the ownCloud UI, confirm the SSH terminal is available for allowed users.
   - Connect to a whitelisted host and ensure the terminal works correctly.
   - Check logs to confirm sessions are being recorded.

10. **Reporting**
   - Provide a final report with:
     - Linting and formatting fixes applied.
     - Tests executed and passed.
     - Security issues found and fixed.
     - Deployment verification results.

Execute all steps yourself. Do not leave anything in “preparation” state — the pipeline must be fully functional, the plugin installed, and the WebSocket daemon operational.
