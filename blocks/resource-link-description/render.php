<?php
/**
 * Server-side render for the ucf-today/resource-link-description block.
 *
 * Outputs a Resource Link's description (the `ucf_resource_link_description`
 * ACF field) as a plain paragraph. Designed to run inside a core Query Loop of
 * `ucf_resource_link` posts, reading the current post from the `postId`
 * context.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

if ( ! function_exists( 'ucf_today_get_resource_link_data' ) ) {
	return;
}

$ucf_rld_post_id = $block->context['postId'] ?? get_the_ID();

if ( ! $ucf_rld_post_id ) {
	return;
}

$ucf_rld = ucf_today_get_resource_link_data( $ucf_rld_post_id );

if ( ! $ucf_rld['post_id'] || '' === $ucf_rld['description'] ) {
	return;
}

$ucf_rld_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'resource-link__description' )
);
?>
<p <?php echo $ucf_rld_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() is already escaped. ?>><?php echo esc_html( $ucf_rld['description'] ); ?></p>
