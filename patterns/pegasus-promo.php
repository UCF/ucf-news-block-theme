<?php
/**
 * Title: Pegasus Magazine Promo
 * Slug: ucf-news-block-theme/pegasus-promo
 * Categories: featured, call-to-action
 * Description: Sidebar promo highlighting the latest Pegasus Magazine issue and its featured story. All content (cover image, issue title, featured story, excerpt, and links) is edited manually.
 *
 * Static, editorially-managed pattern. Built from core blocks so content
 * authors can update the issue and featured story each cycle without code
 * changes. Replace the placeholder cover image, titles, excerpt, and the two
 * links (the issue link and the featured-story link) after inserting.
 */
?>
<!-- wp:group {"tagName":"section","className":"pegasus-promo","layout":{"type":"constrained"}} -->
<section class="wp-block-group pegasus-promo">
	<!-- wp:heading {"className":"pegasus-promo__heading u-uppercase u-tracking-wide","style":{"elements":{"link":{"color":{"text":"var:preset|color|muted"}}}},"textColor":"muted","fontSize":"medium"} -->
	<h2 class="wp-block-heading pegasus-promo__heading u-uppercase u-tracking-wide has-muted-color has-text-color has-link-color has-medium-font-size">Pegasus Magazine</h2>
	<!-- /wp:heading -->

	<!-- wp:group {"className":"pegasus-promo__issue","layout":{"type":"default"}} -->
	<div class="wp-block-group pegasus-promo__issue">
		<!-- wp:image {"sizeSlug":"large","linkDestination":"custom","className":"pegasus-promo__cover"} -->
		<figure class="wp-block-image size-large pegasus-promo__cover"><a href="https://www.ucf.edu/pegasus/spring-2026/"><img src="https://www.ucf.edu/wp-content/blogs.dir/4/files/2026/04/Cover-Autry-Pegasus-Spr26-Cover-320x412-1.jpg" alt="Spring 2026"/></a></figure>
		<!-- /wp:image -->

		<!-- wp:heading {"level":3,"className":"pegasus-promo__issue-title","style":{"elements":{"link":{"color":{"text":"var:preset|color|secondary"}}}},"textColor":"secondary","fontSize":"medium"} -->
		<h3 class="wp-block-heading pegasus-promo__issue-title has-secondary-color has-text-color has-link-color has-medium-font-size"><a href="https://www.ucf.edu/pegasus/spring-2026/">Spring 2026</a></h3>
		<!-- /wp:heading -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"className":"pegasus-promo__featured","layout":{"type":"default"}} -->
	<div class="wp-block-group pegasus-promo__featured">
		<!-- wp:paragraph {"className":"pegasus-promo__eyebrow u-uppercase u-tracking-wide","textColor":"muted","fontSize":"small"} -->
		<p class="pegasus-promo__eyebrow u-uppercase u-tracking-wide has-muted-color has-text-color has-small-font-size">Featured Story</p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"level":3,"className":"pegasus-promo__story-title","style":{"elements":{"link":{"color":{"text":"var:preset|color|secondary"}}}},"textColor":"secondary","fontSize":"heading-5"} -->
		<h3 class="wp-block-heading pegasus-promo__story-title has-secondary-color has-text-color has-link-color has-heading-5-font-size"><a href="https://www.ucf.edu/pegasus/prepared-to-protect/">Prepared to Protect</a></h3>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"className":"pegasus-promo__excerpt","fontSize":"small"} -->
		<p class="pegasus-promo__excerpt has-small-font-size">As relentless disasters test communities nationwide, UCF leads the way in shaping strategies and preparing a workforce to keep people safe and systems strong.</p>
		<!-- /wp:paragraph -->

		<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"right"}} -->
		<div class="wp-block-buttons">
			<!-- wp:button {"backgroundColor":"primary","textColor":"secondary","className":"pegasus-promo__cta","style":{"border":{"radius":{"topLeft":"0px","topRight":"0px","bottomLeft":"0px","bottomRight":"0px"}},"typography":{"textTransform":"uppercase","fontStyle":"normal","fontWeight":"600"},"spacing":{"padding":{"left":"var:preset|spacing|medium","right":"var:preset|spacing|medium","top":"var:preset|spacing|small","bottom":"var:preset|spacing|small"}}}} -->
			<div class="wp-block-button pegasus-promo__cta"><a class="wp-block-button__link has-secondary-color has-primary-background-color has-text-color has-background wp-element-button" href="https://www.ucf.edu/pegasus/prepared-to-protect/" style="border-top-left-radius:0px;border-top-right-radius:0px;border-bottom-left-radius:0px;border-bottom-right-radius:0px;padding-top:var(--wp--preset--spacing--small);padding-right:var(--wp--preset--spacing--medium);padding-bottom:var(--wp--preset--spacing--small);padding-left:var(--wp--preset--spacing--medium);font-style:normal;font-weight:600;text-transform:uppercase">Read More</a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
