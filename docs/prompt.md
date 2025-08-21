# Prompt for Jules: Build CloudShell-ownCloud

You are building a GitHub project called **CloudShell-ownCloud**. Your task is to scaffold an ownCloud app that embeds a secure, web-based SSH terminal. Follow these steps precisely:

## 1. Project Structure
- Create a folder `CloudShell-ownCloud`
- Inside, create subfolders: `appinfo`, `controller`, `templates`, `docs`, `css`, `js`

## 2. App Info
- In `appinfo/info.xml`, define the app ID `cloudshell`, name, summary, description, version `0.1.0`, MIT license, author, and namespace.
- In `appinfo/routes.php`, define a route `/terminal` mapped to `TerminalController#index`.

## 3. Controller
- In `controller/TerminalController.php`, create `TerminalController` that extends `OCP\AppFramework\Controller`.
- Implement an `index()` method that returns a `TemplateResponse` for the terminal template.

## 4. Template
- In `templates/terminal.php`, create a full-height iframe pointing to a placeholder backend terminal URL (e.g., `/apps/cloudshell/backend/`).

## 5. Documentation
- `README.md`: include project overview, planned features, roadmap, co-development note with Jules, and MIT license.
- `docs/KICKSTARTER_PITCH.md`: outline the problem, solution, roadmap, and why users should back the project.

## 6. Placeholders
- Add empty `css` and `js` folders for future UI enhancements.

## 7. Output
- Produce the full folder structure and all files ready to be zipped and uploaded to GitHub.
- Do not implement the SSH backend yet; focus only on scaffolding, templates, and documentation for the MVP.

> **Important:** Output all files with exact content, including directory paths, so that if someone runs it, they get a complete GitHub-ready scaffold.
