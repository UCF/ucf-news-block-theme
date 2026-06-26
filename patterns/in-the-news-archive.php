<?php
/**
 * Title: In the News Archive
 * Slug: ucf-news-block-theme/in-the-news-archive
 * Categories: query
 * Block Types: core/query
 * Description: A Query Loop of Resource Links ("In the News") — each item shows the title (linking to the external source URL), the source, and the description. Drop onto the in-the-news page.
 *
 * A ready-to-use Query Loop configured for the `ucf_resource_link` post type.
 * Each item is rendered with the theme's context-aware Resource Link blocks
 * (title / source / description), which read the per-post `postId` the Query
 * Loop provides and emit semantic, correctly-linked markup.
 */
?>
<!-- wp:query {"queryId":0,"query":{"perPage":10,"pages":0,"offset":0,"postType":"ucf_resource_link","order":"desc","orderBy":"date","inherit":false},"className":"in-the-news"} -->
<div class="wp-block-query in-the-news">
	<!-- wp:post-template {"className":"in-the-news__list"} -->
		<!-- wp:group {"tagName":"article","className":"resource-link","layout":{"type":"constrained"}} -->
		<article class="wp-block-group resource-link">
			<!-- wp:ucf-today/resource-link-title {"level":3} /-->
			<!-- wp:ucf-today/resource-link-source /-->
			<!-- wp:ucf-today/resource-link-description /-->
		</article>
		<!-- /wp:group -->
	<!-- /wp:post-template -->

	<!-- wp:query-pagination {"layout":{"type":"flex","justifyContent":"space-between"}} -->
		<!-- wp:query-pagination-previous /-->
		<!-- wp:query-pagination-numbers /-->
		<!-- wp:query-pagination-next /-->
	<!-- /wp:query-pagination -->

	<!-- wp:query-no-results -->
		<!-- wp:paragraph -->
		<p><?php echo esc_html__( 'No news coverage to show right now. Please check back soon.', 'ucf-news-block-theme' ); ?></p>
		<!-- /wp:paragraph -->
	<!-- /wp:query-no-results -->
</div>
<!-- /wp:query -->
