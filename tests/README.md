# Automated accessibility tests

`a11y.spec.js` runs [axe-core](https://github.com/dequelabs/axe-core) (WCAG
2.0/2.1 A + AA) with Playwright against a live WordPress instance, across every
route in `routes.js` at three viewports (desktop / tablet 780px / mobile 360px).

The suite fails on axe **violations**. axe **incomplete** results ("needs
review", e.g. text over a background image where contrast can't be computed
automatically) are logged as annotations without failing the build.

Routes (one per template — front page, basic page, single post, category
archive, tag archive, search, 404) live in `routes.js`.

## Running against wp-env (the supported path)

```bash
npm install                 # installs wp-env + Playwright into node_modules
npm run build               # compile CSS first
npm run env:start           # boot wp-env (needs Docker running)
npm run env:seed            # create deterministic content (tests/seed.sh)
npx playwright install chromium

npm run test:a11y
```

`TEST_BASE_URL` defaults to `http://localhost:8888` (wp-env).

### Requirements / gotchas

- **Docker must be running.** wp-env runs WordPress in Docker.
- **The checkout must live in a Docker-shared path.** Docker Desktop shares your
  home directory by default but *not* arbitrary locations. Clone the theme
  somewhere under your home folder (or add the path under Docker → Settings →
  Resources → File Sharing). Otherwise `wp-env start` fails with a "mounts
  denied" error.
- **No global installs / PATH edits needed.** The `npm run *` scripts put
  `./node_modules/.bin` on PATH, so the local `wp-env`/`playwright` binaries are
  used. Run `env:seed` via `npm run env:seed` (or from the repo root after
  `npm install`) so `seed.sh` finds the local `wp-env`.

## Running against another install (e.g. local MAMP)

Point the suite anywhere and override the routes to match that site's
slugs/permalinks:

```bash
PW_CHANNEL=chrome \
TEST_BASE_URL='http://localhost/wordpress/site/' \
TEST_ROUTES_JSON='[{"name":"home","path":""},{"name":"post-single","path":"hello-world/"}]' \
npx playwright test tests/a11y.spec.js
```

`PW_CHANNEL=chrome` drives a locally-installed Chrome (no Playwright browser
download). Use a trailing slash on `TEST_BASE_URL` and non-leading-slash paths
when the site lives in a subdirectory.

## CI

`.github/workflows/ci.yml` builds CSS, starts wp-env, seeds content, installs the
Playwright Chromium build, then runs the accessibility suite and uploads the
report on failure.
