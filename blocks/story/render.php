<?php
/**
 * Server-side render for the ucf-today/story block.
 *
 * Renders a post as a story card in one of four layout variants:
 *
 *   - feature       Full-width, image left / text right (collapses on mobile).
 *   - stacked       Image on top, then title, then excerpt.
 *   - stacked-title Image on top, then title only.
 *   - inline        Small thumbnail left, category + title right.
 *
 * The post is resolved in priority order: the Query Loop context, then a
 * hand-picked `postId` attribute, then the current post. Each variant emits a
 * single <article> with exactly one linked element (the title), per the
 * theme's semantic-markup rule.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

// Resolve the post to display. Inside a Query Loop the loop supplies the post
// via context (queryId is the reliable "in a loop" signal — a bare singular
// page also injects its own postId into context, which must NOT win over a
// hand-picked story). Outside a loop, prefer the picked postId attribute.
if ( isset( $block->context['queryId'] ) ) {
	$ucf_story_post_id = $block->context['postId'] ?? null;
} else {
	$ucf_story_post_id = ! empty( $attributes['postId'] ) ? $attributes['postId'] : null;

	// During a ServerSideRender preview in the editor there is no loop
	// context, so honor the post_id passed by the edit component. Gated on the
	// edit_posts capability so arbitrary REST callers can't steer output via
	// ?post_id= on public endpoints.
	if ( ! $ucf_story_post_id && defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_GET['post_id'] ) && current_user_can( 'edit_posts' ) ) {
		$ucf_story_post_id = absint( wp_unslash( $_GET['post_id'] ) );
	}

	if ( ! $ucf_story_post_id ) {
		$ucf_story_post_id = get_the_ID();
	}
}

if ( ! $ucf_story_post_id || ! function_exists( 'ucf_today_get_story_data' ) ) {
	return;
}

$ucf_story = ucf_today_get_story_data( $ucf_story_post_id );

if ( ! $ucf_story['post_id'] ) {
	return;
}

$ucf_story_variants = array( 'feature', 'stacked', 'stacked-title', 'inline' );
$ucf_story_variant  = $attributes['variant'] ?? 'stacked';
if ( ! in_array( $ucf_story_variant, $ucf_story_variants, true ) ) {
	$ucf_story_variant = 'stacked';
}

// Per-variant image size and responsive `sizes` hint. We reuse WordPress core
// sizes (already generated on every upload) rather than registering custom
// crops, which would require regenerating all existing thumbnails. Header
// images are required to be 1200x800 (3:2), so the max-bound core sizes come
// out at 3:2: large = 1024x683, medium_large = 768x512, medium = 300x200.
$ucf_story_image_map = array(
	'feature'       => array( 'size' => 'large', 'sizes' => '(min-width: 768px) 50vw, 100vw' ),
	'stacked'       => array( 'size' => 'medium_large', 'sizes' => '(min-width: 768px) 33vw, 100vw' ),
	'stacked-title' => array( 'size' => 'medium', 'sizes' => '(min-width: 768px) 25vw, 50vw' ),
	'inline'        => array( 'size' => 'medium', 'sizes' => '(min-width: 768px) 200px, 33vw' ),
);
$ucf_story_image_args = $ucf_story_image_map[ $ucf_story_variant ];

// Inline stories sit within article body copy, so their title is a lower-level
// heading than the standalone/list variants.
$ucf_story_heading = ( 'inline' === $ucf_story_variant ) ? 'h3' : 'h2';

$ucf_story_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'story story--' . $ucf_story_variant )
);

/**
 * Renders the story thumbnail as a linked figure, or nothing when no image.
 */
$ucf_story_render_image = static function () use ( $ucf_story, $ucf_story_image_args ) {
	if ( ! $ucf_story['image_id'] ) {
		return;
	}

	$image = wp_get_attachment_image(
		$ucf_story['image_id'],
		$ucf_story_image_args['size'],
		false,
		array(
			'class'    => 'story__image',
			'decoding' => 'async',
			'loading'  => 'lazy',
			'sizes'    => $ucf_story_image_args['sizes'],
			'alt'      => '',
		)
	);

	if ( ! $image ) {
		return;
	}
	?>
	<a class="story__media" href="<?php echo esc_url( $ucf_story['permalink'] ); ?>" tabindex="-1" aria-hidden="true">
		<?php echo $image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() returns safe markup. ?>
	</a>
	<?php
};
?>
<article <?php echo $ucf_story_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() is already escaped. ?>>
	<?php $ucf_story_render_image(); ?>

	<div class="story__body">
		<?php if ( 'inline' === $ucf_story_variant && $ucf_story['category'] ) : ?>
			<p class="story__category"><?php echo esc_html( $ucf_story['category'] ); ?></p>
		<?php endif; ?>

		<<?php echo $ucf_story_heading; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- whitelisted heading tag. ?> class="story__title">
			<a href="<?php echo esc_url( $ucf_story['permalink'] ); ?>"><?php echo esc_html( $ucf_story['title'] ); ?></a>
		</<?php echo $ucf_story_heading; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- whitelisted heading tag. ?>>

		<?php if ( in_array( $ucf_story_variant, array( 'feature', 'stacked' ), true ) && $ucf_story['excerpt'] ) : ?>
			<p class="story__excerpt"><?php echo esc_html( $ucf_story['excerpt'] ); ?></p>
		<?php endif; ?>

		<?php if ( 'feature' === $ucf_story_variant && $ucf_story['date_iso'] ) : ?>
			<time class="story__date" datetime="<?php echo esc_attr( $ucf_story['date_iso'] ); ?>">
				<?php echo esc_html( $ucf_story['date_label'] ); ?>
			</time>
		<?php endif; ?>
	</div>
</article>
