<?php
/**
 * Title: Story Body: Image-Led
 * Slug: ucf-news-block-theme/story-body-visual
 * Categories: media, posts
 * Block Types: core/post-content
 * Post Types: post, page
 * Description: Starting layout for a visual story: a full-bleed cover with a headline over it, an edge-to-edge image, a two-up image pair, a jumbotron statement band, alternating image-and-text sections, and a photo grid. Replace the placeholder images and copy after inserting.
 *
 * `Block Types: core/post-content` makes this a starter pattern: it is offered
 * in the "Choose a pattern" modal when an author creates a new, empty post or
 * page, so a story can be started from a layout without touching a template.
 * It remains available from the normal inserter as well.
 *
 * Core blocks only — no new styles. Notes on the choices here:
 *
 * - Media is intentionally left unset so each block shows its own media
 *   placeholder in the editor; captions are kept as prompts and survive
 *   choosing an image.
 * - `align: full` is what produces the edge-to-edge treatment. _content.scss
 *   caps `.wp-block-image` at 1067px, but core emits
 *   `<layout-container> .alignfull{max-width:none}` — two classes to that
 *   rule's one — so full-aligned images outrank the cap and really do run the
 *   width of the viewport. Unaligned images still cap at 1067px.
 * - The two-up pair, image/text sections, and photo grid use `align: wide`
 *   (950px per theme.json) so they read as a band inside the text column
 *   rather than competing with the full-bleed moments above them.
 * - The alternating image/text sections are `core/columns` (image + text,
 *   image side flipped between them) rather than `core/media-text`: media-text
 *   stores its media as a first-class attribute, so a pattern can't seed an
 *   editable image placeholder in it and an unfilled media area renders blank.
 *   Columns let the media area be a real `wp:image` placeholder like the rest.
 * - The jumbotron is a full-bleed `core/group` on the `secondary` (black) token
 *   with `inverse` text and a `primary` (gold) button — a bold statement break
 *   between sections, using palette tokens rather than one-off colors.
 * - The photo grid is `core/gallery` (the native grid block); images are left
 *   unset so each cell shows a media placeholder.
 */

?>

<!-- wp:paragraph -->
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.</p>
<!-- /wp:paragraph -->

<!-- wp:image {"align":"full"} -->
<figure class="wp-block-image alignfull"><img alt=""/><figcaption class="wp-element-caption">An edge-to-edge image. Add a caption describing it.</figcaption></figure>
<!-- /wp:image -->

<!-- wp:paragraph -->
<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>
<!-- /wp:paragraph -->

<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:image -->
<figure class="wp-block-image"><img alt=""/><figcaption class="wp-element-caption">Left of the pair.</figcaption></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:image -->
<figure class="wp-block-image"><img alt=""/><figcaption class="wp-element-caption">Right of the pair.</figcaption></figure>
<!-- /wp:image -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:group {"align":"full","backgroundColor":"secondary","textColor":"inverse","style":{"spacing":{"padding":{"top":"var:preset|spacing|xx-large","bottom":"var:preset|spacing|xx-large","left":"var:preset|spacing|large","right":"var:preset|spacing|large"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-inverse-color has-secondary-background-color has-text-color has-background" style="padding-top:var(--wp--preset--spacing--xx-large);padding-right:var(--wp--preset--spacing--large);padding-bottom:var(--wp--preset--spacing--xx-large);padding-left:var(--wp--preset--spacing--large)"><!-- wp:heading {"textAlign":"center","level":2,"fontSize":"heading-1"} -->
<h2 class="wp-block-heading has-text-align-center has-heading-1-font-size">A statement that anchors the story</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","className":"lead"} -->
<p class="has-text-align-center lead">One or two sentences that carry the weight of this section — a pull-forward idea, a key stat, or the turn in the narrative.</p>
<!-- /wp:paragraph -->

</div>
<!-- /wp:group -->

<!-- wp:columns {"align":"wide","verticalAlignment":"center"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:image -->
<figure class="wp-block-image"><img alt=""/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">A moment in the story</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:columns {"align":"wide","verticalAlignment":"center"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">And the moment after it</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:image -->
<figure class="wp-block-image"><img alt=""/></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:cover {"dimRatio":40,"minHeight":70,"minHeightUnit":"vh","align":"full"} -->
<div class="wp-block-cover alignfull" style="min-height:70vh"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-40 has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:heading {"textAlign":"center","level":2,"textColor":"inverse"} -->
<h2 class="wp-block-heading has-text-align-center has-inverse-color has-text-color">A headline that sits over the image</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","textColor":"inverse"} -->
<p class="has-text-align-center has-inverse-color has-text-color">A short line of supporting text.</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:cover -->

<!-- wp:gallery {"columns":3,"linkTo":"none","align":"wide"} -->
<figure class="wp-block-gallery has-nested-images columns-3 is-cropped alignwide"><!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img alt=""/></figure>
<!-- /wp:image -->

<!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img alt=""/></figure>
<!-- /wp:image -->

<!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img alt=""/></figure>
<!-- /wp:image -->

<!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img alt=""/></figure>
<!-- /wp:image -->

<!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img alt=""/></figure>
<!-- /wp:image -->

<!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img alt=""/></figure>
<!-- /wp:image --></figure>
<!-- /wp:gallery -->

<!-- wp:paragraph -->
<p>Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus.</p>
<!-- /wp:paragraph -->
