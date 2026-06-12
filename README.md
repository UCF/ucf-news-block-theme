# UCF Today Block Theme

A WordPress block theme for UCF Today (https://www.ucf.edu/news).

- Contributors: UCF Web Communications
- Requires at least: WordPress 7.0
- Tested up to: 7.0
- Requires PHP: 8.1
- License: GPL-3.0-or-later

## Purpose

This project provides a clean foundation for the UCF Today website's editorial storytelling experience.

The theme focuses on WordPress block-theme architecture and reusable content-building primitives so teams can create creative, engaging story layouts in the editor.

This initial scaffold intentionally does not define visual styles yet. It sets up only the minimum required files and documentation needed to start implementation.

## What's Included

- Block theme metadata and registration files
- Basic template and template parts
- Minimal `theme.json` configuration
- Contributor documentation and working notes

## Project Structure

```text
.
├── .wp-env.json
├── CLAUDE.md
├── CONTRIBUTING.md
├── LICENSE
├── README.md
├── assets/
│   └── css/
│       └── main.css
├── functions.php
├── index.php
├── package.json
├── parts/
│   ├── footer.html
│   └── header.html
├── src/
│   └── scss/
│       └── style.scss
├── templates/
│   └── index.html
├── theme.json
└── style.css
```

## Local Development

1. Place this directory in your WordPress install under `wp-content/themes/ucf-news-block-theme`.
2. Activate "UCF Today Block Theme" in Appearance > Themes.
3. Install Node dependencies with `npm install`.
4. Build theme styles with `npm run build`.
5. Use `npm run watch` during development to rebuild on file changes.
6. Open the Site Editor to begin creating templates, patterns, and style definitions.

## Build Commands

- `npm run build`: Compile `src/scss/style.scss` to `assets/css/main.css`.
- `npm run watch`: Watch and recompile styles during development.
- `npm run env:start`: Start a local WordPress test environment via `wp-env`.
- `npm run env:stop`: Stop the local `wp-env` environment.
- `npm run env:clean`: Reset the `wp-env` environment.
- `npm version patch|minor|major`: Bump version in `package.json`/`package-lock.json` and sync the `Version:` header in `style.css`.

## Versioning Process

Use npm versioning as the single source of truth for release version updates.

Standard workflow (creates a version commit and git tag):

1. Ensure your branch is clean and up to date.
2. Choose one command based on release type:
	- `npm version patch` for bugfix releases (e.g. `0.1.0` -> `0.1.1`)
	- `npm version minor` for backward-compatible feature releases (e.g. `0.1.0` -> `0.2.0`)
	- `npm version major` for breaking changes (e.g. `0.1.0` -> `1.0.0`)
3. Confirm updated files:
	- `package.json`
	- `package-lock.json`
	- `style.css` (theme header `Version:` line)
4. Push branch and tags:
	- `git push`
	- `git push --tags`

No-tag workflow (useful for PR preparation):

1. Run `npm version patch --no-git-tag-version` (or `minor`/`major`).
2. Commit the resulting file updates in your branch.

Implementation note:

- The sync step is handled by `npm run version:sync`, which is called automatically via npm's `version` lifecycle hook.

## Roadmap

Near-term work for this project:

1. Define UCF Today design tokens in `theme.json`.
2. Add reusable pattern and block-style primitives for storytelling components.
3. Add automated quality checks when implementation requirements are defined.

## License

This project is released under the GNU General Public License v3.0 or later. See `LICENSE`.
