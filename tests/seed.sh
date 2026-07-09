#!/usr/bin/env bash
#
# Seed a wp-env instance with deterministic content so the route list in
# tests/routes.js resolves. Run after `npx wp-env start`.
#
# Creates: postname permalinks, a static "Home" front page (whose content is the
# theme's story-query-grid pattern), a basic page (sample-page), a single post
# (hello-world) tagged "featured" in the default Uncategorized category, and a
# navigation menu so the header's Navigation block renders valid markup.
set -euo pipefail

THEME='ucf-news-block-theme'

# Use the project-local wp-env (installed by `npm install`); never fetch it from
# the registry. Run via `npm run env:seed`, or from the repo root after install.
if ! npx --no-install wp-env --version >/dev/null 2>&1; then
	echo "wp-env not found. Run 'npm install' first, then 'npm run env:seed'." >&2
	exit 1
fi

cli() { npx --no-install wp-env run cli wp "$@"; }

echo "Activating theme + permalinks..."
cli theme activate "$THEME"
cli rewrite structure '/%postname%/' --hard

echo "Static front page (from the story-query-grid pattern)..."
GRID_CONTENT=$(cli eval 'echo WP_Block_Patterns_Registry::get_instance()->get_registered("ucf-news-block-theme/story-query-grid")["content"];' || true)
HOME_ID=$(cli post create --post_type=page --post_status=publish \
	--post_title='Home' --post_name='home' --post_content="$GRID_CONTENT" --porcelain)
cli option update show_on_front 'page'
cli option update page_on_front "$HOME_ID"

echo "Basic page..."
cli post create --post_type=page --post_status=publish \
	--post_title='Sample Page' --post_name='sample-page' \
	--post_content='<!-- wp:paragraph --><p>Sample page content.</p><!-- /wp:paragraph -->'

echo "Single post (tagged 'featured', category Uncategorized)..."
cli post create --post_type=post --post_status=publish \
	--post_title='Hello World' --post_name='hello-world' \
	--post_category=1 --tags_input='featured' \
	--post_content='<!-- wp:paragraph --><p>Hello world post content.</p><!-- /wp:paragraph -->'

echo "Navigation menu..."
# Without an assigned menu, the core Navigation block falls back to a Page List
# that nests <ul> directly inside <ul> (invalid markup -> axe "list" violation).
# Seeding a real menu makes the nav render proper <li> items, matching a live
# site. The Navigation block (no ref) auto-selects this wp_navigation post.
NAV_CONTENT='<!-- wp:navigation-link {"label":"Home","url":"/","kind":"custom","isTopLevelLink":true} /--><!-- wp:navigation-link {"label":"Sample Page","url":"/sample-page/","kind":"custom","isTopLevelLink":true} /-->'
cli post create --post_type=wp_navigation --post_status=publish \
	--post_title='Primary' --post_content="$NAV_CONTENT"

echo "Flushing rewrite + pattern cache..."
cli rewrite flush --hard
cli eval 'wp_get_theme()->delete_pattern_cache();'

echo "Seed complete."
