<?php
/**
 * Title: Site Wordmark
 * Slug: ucf-news-block-theme/site-wordmark
 * Inserter: no
 *
 * Two-tone "UCF Today" wordmark linking to the site home. Lives in a PHP
 * pattern (rather than inline in parts/header.html) so the home URL can be
 * resolved dynamically with home_url() — a template part can't run PHP.
 */
?>
<!-- wp:html -->
<a class="site-wordmark" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr__( 'UCF Today — home', 'ucf-news-block-theme' ); ?>"><span class="site-wordmark__lead">UCF</span><span class="site-wordmark__rest">Today</span></a>
<!-- /wp:html -->
