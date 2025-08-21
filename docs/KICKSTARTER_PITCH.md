# Kickstarter: ownShell for ownCloud

## The Problem
System administrators and developers who use ownCloud often need to switch between the web UI and a separate SSH terminal to manage their server. This context-switching is inefficient and breaks the workflow, especially for quick tasks like checking logs, restarting services, or managing files directly on the command line.

## The Solution: ownShell
**ownShell** brings the power of the command line directly into the ownCloud interface. It's a secure, web-based SSH terminal embedded seamlessly into the ownCloud app framework.

With ownShell, you can:
- **Stay in one place:** No more juggling windows. Manage your server from the same UI you manage your files.
- **Secure access:** Leverages ownCloud's authentication and permissions model to control who can access the terminal.
- **Increase productivity:** Run quick commands, edit configs, and monitor services without ever leaving your browser.

## Roadmap & Funding
We have a working MVP scaffold, but we need your help to make it a fully-featured tool.

- **Phase 1 (You are here):** Basic app structure, iframe-based terminal view.
- **Phase 2 (Our Goal):** Implement a real, secure SSH backend (using `ttyd` or similar), integrate ownCloud's authentication, and add an admin settings panel.
- **Phase 3 (Stretch Goal):** Support for multiple server profiles, session logging, and UI theme integration.

**Why back us?** Your support will fund the development time needed to build the secure backend, conduct security audits, and make ownShell a robust, must-have app for any serious ownCloud administrator. Join us in making ownCloud the ultimate all-in-one server management platform!
