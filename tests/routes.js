/**
 * Shared route list + base URL for the accessibility suite.
 *
 * - TEST_BASE_URL overrides the target site (default: the wp-env instance).
 * - TEST_ROUTES_JSON (a JSON array of { name, path }) overrides the route list,
 *   handy for pointing the suite at a local install whose slugs/permalinks
 *   differ from the seeded wp-env content (see tests/seed.sh).
 *
 * Each route covers a distinct template: front page, basic page, single post,
 * category archive, tag archive, search results, and 404.
 */

const baseURL = process.env.TEST_BASE_URL || 'http://localhost:8888';

const defaultRoutes = [
	{ name: 'home', path: '/' },
	{ name: 'page-basic', path: '/sample-page/' },
	{ name: 'post-single', path: '/hello-world/' },
	{ name: 'archive-category', path: '/category/uncategorized/' },
	{ name: 'archive-tag', path: '/tag/featured/' },
	{ name: 'search', path: '/?s=hello' },
	{ name: 'notfound-404', path: '/this-page-does-not-exist-404/' },
];

let routes = defaultRoutes;
if (process.env.TEST_ROUTES_JSON) {
	try {
		routes = JSON.parse(process.env.TEST_ROUTES_JSON);
	} catch (e) {
		throw new Error(`TEST_ROUTES_JSON is not valid JSON: ${e.message}`);
	}
}

module.exports = { baseURL, routes };
