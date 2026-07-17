<?php
/**
 * Title: Story Body: Text with Pullquote
 * Slug: ucf-news-block-theme/story-body-text
 * Categories: text, posts
 * Block Types: core/post-content
 * Post Types: post, page
 * Description: Starting layout for a text-led story: a lead paragraph, body copy with an inline pullquote that floats alongside it, and a wide image with a caption. Replace the placeholder copy and image after inserting.
 *
 * `Block Types: core/post-content` makes this a starter pattern: it is offered
 * in the "Choose a pattern" modal when an author creates a new, empty post or
 * page, so a story can be started from a layout without touching a template.
 * It remains available from the normal inserter as well.
 *
 * Core blocks plus the theme's existing utilities only:
 *
 * - `.lead` (_utilities.scss) sizes the intro paragraph.
 * - The pullquote is the `ucf-today/pullquote` rich-text format registered in
 *   editor/formats/pullquote.js, which wraps a selection in
 *   `<span class="ucf-pullquote">`. _content.scss floats that span left and
 *   adds the rules above/below it at 754px and up, so it needs enough copy
 *   after it in the same paragraph to wrap around. Authors can re-apply it to
 *   any selection with the Pullquote button in the paragraph toolbar.
 * - The image is left unset so the editor shows the media placeholder; the
 *   caption is kept as a prompt and survives choosing an image.
 */

?>
<!-- wp:paragraph {"className":"lead"} -->
<p class="lead">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><span class="ucf-pullquote">Neque porro quisquam est qui dolorem ipsum quia dolor sit amet.</span>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus.</p>
<!-- /wp:paragraph -->

<!-- wp:image {"align":"wide"} -->
<figure class="wp-block-image alignwide"><img alt=""/><figcaption class="wp-element-caption">Add a caption describing this image.</figcaption></figure>
<!-- /wp:image -->

<!-- wp:paragraph -->
<p>Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
<!-- /wp:paragraph -->
