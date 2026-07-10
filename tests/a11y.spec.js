/**
 * Accessibility: runs axe-core (WCAG 2.0/2.1 A + AA) on every route.
 *
 * The suite FAILS only on axe *violations*. axe *incomplete* results
 * ("needs review" — e.g. text over a background image, where contrast can't be
 * computed automatically) are reported as annotations/log output but do NOT
 * fail the build. This is deliberate: the pa11y CLI conflates incomplete with
 * errors, which produces false positives on every hero image.
 */

const { test, expect } = require('@playwright/test');
const AxeBuilder = require('@axe-core/playwright').default;
const { routes } = require('./routes');

const WCAG_TAGS = ['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa'];

for (const route of routes) {
	test(`a11y: ${route.name}`, async ({ page }, testInfo) => {
		await page.goto(route.path, { waitUntil: 'networkidle' });

		const results = await new AxeBuilder({ page }).withTags(WCAG_TAGS).analyze();

		// Surface "needs review" items without failing.
		if (results.incomplete.length) {
			const summary = results.incomplete
				.map((i) => `${i.id} (${i.nodes.length})`)
				.join(', ');
			testInfo.annotations.push({ type: 'a11y-needs-review', description: summary });
			// eslint-disable-next-line no-console
			console.log(`[needs review] ${route.name}: ${summary}`);
		}

		const violationSummary = results.violations.map((v) => ({
			id: v.id,
			impact: v.impact,
			nodes: v.nodes.length,
			help: v.help,
		}));

		expect(
			results.violations,
			`Accessibility violations on ${route.name}:\n${JSON.stringify(violationSummary, null, 2)}`
		).toEqual([]);
	});
}
