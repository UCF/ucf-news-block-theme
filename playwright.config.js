/**
 * Playwright config for the theme's automated accessibility suite.
 *
 * Runs every route (tests/routes.js) at three viewports matching the theme's
 * breakpoints (desktop / tablet 780px / mobile 360px).
 *
 * Browser: set PW_CHANNEL=chrome to drive a locally-installed Chrome (no
 * Playwright browser download — used for local runs). In CI, leave it unset and
 * `npx playwright install chromium` provides the browser.
 */

const { defineConfig } = require('@playwright/test');
const { baseURL } = require('./tests/routes');

const channel = process.env.PW_CHANNEL; // e.g. "chrome" locally

module.exports = defineConfig({
	testDir: './tests',
	fullyParallel: true,
	forbidOnly: !!process.env.CI,
	reporter: process.env.CI ? [['github'], ['list'], ['html', { open: 'never' }]] : [['list']],
		baseURL,
		headless: true,
		...(channel ? { channel } : {}),
	},
	projects: [
		{ name: 'desktop', use: { viewport: { width: 1280, height: 900 } } },
		{ name: 'tablet', use: { viewport: { width: 780, height: 1024 } } },
		{ name: 'mobile', use: { viewport: { width: 360, height: 740 } } },
	],
});
