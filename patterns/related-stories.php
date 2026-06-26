<?php
/**
 * Title: Related Stories
 * Slug: ucf-news-block-theme/related-stories
 * Categories: posts, query
 * Description: Related stories grid for single posts, filtered by the current post's primary tag via theme query logic.
 * Block Types: core/query
 */
?>
<!-- wp:group {"className":"related-stories","tagName":"section","layout":{"type":"constrained","contentSize":"950px"}} -->
<section class="wp-block-group related-stories">

<!-- wp:separator {"className":"is-style-wide"} -->
<hr class="wp-block-separator has-alpha-channel-opacity is-style-wide"/>
<!-- /wp:separator -->

<!-- wp:heading {"textAlign":"center","level":2} -->
<h2 class="wp-block-heading has-text-align-center">Related Stories</h2>
<!-- /wp:heading -->

<!-- wp:query {"queryId":1,"query":{"perPage":8,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","relatedStories":true},"className":"related-stories-query"} -->
<div class="wp-block-query related-stories-query">
<!-- wp:post-template {"layout":{"type":"grid","columnCount":4}} -->
<!-- wp:ucf-today/story {"variant":"stacked-title"} /-->
<!-- /wp:post-template -->

<!-- wp:query-no-results -->
<!-- /wp:query-no-results -->
</div>
<!-- /wp:query -->

</section>
<!-- /wp:group -->
