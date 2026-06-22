<?php
/**
 * Server-side render for the ucf-today/post-deck block.
 *
 * Outputs the post's deck (the `post_header_deck` WYSIWYG field) inside a div
 * with the lead text treatment. Using a div wrapper lets the WYSIWYG value keep
 * its native block-level markup (paragraphs, inline elements) without nesting.
 * Renders nothing when no deck is set.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

$ucf_deck_post_id = $block->context['postId'] ?? get_the_ID();

if ( ! $ucf_deck_post_id || ! function_exists( 'get_field' ) ) {
	return;
}

$ucf_deck = trim( (string) get_field( 'post_header_deck', $ucf_deck_post_id ) );

if ( '' === $ucf_deck ) {
	return;
}

$ucf_deck_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'post-header__deck u-text-lead' )
);
?>
<div <?php echo $ucf_deck_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() is already escaped. ?>>
	<?php echo wp_kses_post( $ucf_deck ); ?>
</div>
