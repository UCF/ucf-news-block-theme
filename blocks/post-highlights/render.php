<?php
/**
 * Server-side render for the ucf-today/post-highlights block.
 *
 * Outputs an "Highlights" heading (uppercase, underlined) followed by an
 * unordered list of the post's highlights. Adapted from the legacy
 * Today-Child-Theme `today_get_post_highlights()` output. Renders nothing
 * when no highlights are set.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

$ucf_highlights_post_id = $block->context['postId'] ?? get_the_ID();

if ( ! $ucf_highlights_post_id ) {
	return;
}

$ucf_highlights = ucf_today_get_post_highlights( $ucf_highlights_post_id );

if ( empty( $ucf_highlights ) ) {
	return;
}

$ucf_highlights_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'post-highlights' )
);
?>
<div <?php echo $ucf_highlights_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() is already escaped. ?>>
	<h2 class="post-highlights__heading heading-underline u-uppercase"><?php echo esc_html__( 'Highlights', 'ucf-news-block-theme' ); ?></h2>
	<ul class="post-highlights__list">
		<?php foreach ( $ucf_highlights as $ucf_highlight ) : ?>
		<li class="post-highlights__item"><?php echo wp_kses_post( $ucf_highlight ); ?></li>
		<?php endforeach; ?>
	</ul>
</div>
