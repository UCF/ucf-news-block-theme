<?php
/**
 * Server-side render for the ucf-today/post-header-media block.
 *
 * Outputs the post's header media — an image or a video — based on the
 * `header_media_type` field. Adapted from the legacy Today-Child-Theme
 * `today_get_post_header_media()` output. Images render as a semantic
 * <figure> with an optional <figcaption>; videos render their embed markup.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

$ucf_media_post_id = $block->context['postId'] ?? get_the_ID();

if ( ! $ucf_media_post_id ) {
	return;
}

$ucf_media         = ucf_today_get_post_header_media_data( $ucf_media_post_id );
$ucf_media_caption = $ucf_media['caption'];

$ucf_media_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'post-header__media' )
);

if ( 'video' === $ucf_media['type'] ) {
	if ( ! $ucf_media['video'] ) {
		return;
	}
	?>
	<figure <?php echo wp_kses_data( $ucf_media_wrapper ); ?>>
		<div class="post-header__media-video">
			<?php echo $ucf_media['video']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- ACF oEmbed returns trusted embed markup. ?>
		</div>
	</figure>
	<?php
	return;
}

// Image (default).
if ( ! $ucf_media['image_id'] ) {
	return;
}

$ucf_media_image = wp_get_attachment_image(
	$ucf_media['image_id'],
	'large',
	false,
	array(
		'class'    => 'img-fluid post-header__media-image',
		'decoding' => 'async',
	)
);

if ( ! $ucf_media_image ) {
	return;
}
?>
<figure <?php echo wp_kses_data( $ucf_media_wrapper ); ?>>
	<?php echo $ucf_media_image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() returns safe markup. ?>

	<?php if ( $ucf_media_caption ) : ?>
	<figcaption class="figure-caption post-header__media-caption">
		<?php echo wp_kses_post( $ucf_media_caption ); ?>
	</figcaption>
	<?php endif; ?>
</figure>
