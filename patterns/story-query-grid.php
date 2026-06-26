<?php
/**
 * Title: Story Grid
 * Slug: ucf-news-block-theme/story-query-grid
 * Categories: posts, query
 * Description: A Query Loop grid of story cards (thumbnail and title). Adjust query settings and story variant as needed.
 * Block Types: core/query
 */
?>
<!-- wp:query {"queryId":2,"query":{"perPage":8,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date"},"layout":{"type":"default"},"className":"story-query"} -->
<div class="wp-block-query story-query">
<!-- wp:post-template {"layout":{"type":"grid","columnCount":4}} -->
<!-- wp:ucf-today/story {"variant":"stacked-title"} /-->
<!-- /wp:post-template -->

<!-- wp:query-no-results -->
<!-- wp:paragraph -->
<p>No stories were found.</p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results -->

</div>
<!-- /wp:query -->
