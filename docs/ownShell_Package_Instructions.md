Package ownShell for Marketplace

# ownShell Packaging & Marketplace Preparation

Jules, your task is to **package the ownShell plugin** for ownCloud and make it ready for marketplace submission. Execute each step fully; do not leave anything unprepared.

---

## 1. Verify Folder Structure

Ensure the plugin follows this structure:

ownShell/
├── appinfo/
│ ├── info.xml
│ └── app.php
├── bin/
│ └── ssh-ws-daemon.php
├── css/
├── js/
│ └── terminal.js
├── lib/
│ ├── Controller/
│ ├── Service/
│ └── WebSocket/
├── templates/
│ └── main.php
├── docs/
│ ├── INSTALL.md
│ ├── USAGE.md
│ └── ADMIN.md
├── tests/
│ ├── phpunit/
│ └── jest/
└── composer.json


- Fix any missing files or misplacements.
- Ensure all directories/files are production-ready; remove dev/test artifacts.

---

## 2. Include Documentation

- `docs/INSTALL.md`: Installation instructions.
- `docs/USAGE.md`: User manual.
- `docs/ADMIN.md`: Admin/configuration manual.
- Verify files contain correct instructions for:
  - Installing app
  - WebSocket daemon setup (systemd)
  - Access control and host whitelist
  - Security/logging instructions

---

## 3. Verify Metadata

- Check `appinfo/info.xml` includes:
  - App ID: `ownShell`
  - Name, version, description
  - Author
  - ownCloud compatibility (10.0 - 10.11)
- Correct any errors.

---

## 4. Clean & Lint Code

- Remove any debug code or placeholders.
- Ensure PHP and JS pass linting and formatting checks:
  - PHP: `phpcs --standard=PSR12` + `php-cs-fixer fix`
  - JS: `npx eslint` + `npx prettier --write`

---

## 5. Build the Package

1. From the root of the repository, run:
```bash
tar -czf ownShell-1.0.0.tar.gz ownShell/

    Confirm the tarball includes all files in the structure above.

6. Test Installation

    Copy the tarball to a clean ownCloud instance /apps/.

    Enable the plugin:

occ app:enable ownShell

    Start the WebSocket daemon via systemd:

systemctl enable --now owncloud-ssh-ws.service

    Verify:

        The plugin appears in the Admin and sidebar UI.

        Users can connect to whitelisted hosts via the SSH terminal.

        Logs are correctly recorded.

7. Final Delivery

    Provide a verified ownShell-1.0.0.tar.gz ready for marketplace submission.

    Include docs/ and appinfo/info.xml in the tarball.

    Confirm the package works on a fresh ownCloud install.

Execute all steps yourself. The deliverable must be a fully functional, publishable plugin tarball with complete documentation.


---

You can save this as `ownShell_Package_Instructions.md` and feed it to Jules. He can **follow it step by step or use a script wrapper** to automate the packaging and verification.  

If you want, I can also **convert this prompt into a bash script** that Jules could literally execute to generate the `.tar.gz` and verify installation automatically. Do you want me to do that next?


