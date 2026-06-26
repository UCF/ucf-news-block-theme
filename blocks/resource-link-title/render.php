<?php
/**
 * Server-side render for the ucf-today/resource-link-title block.
 *
 * Outputs a Resource Link's title as a heading whose only linked element points
 * at the external URL (`ucf_resource_link_url`) rather than the permalink —
 * the markup core/post-title cannot produce. Designed to run inside a core
 * Query Loop of `ucf_resource_link` posts, reading the current post from the
 * `postId` context.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

if ( ! function_exists( 'ucf_today_get_resource_link_data' ) ) {
	return;
}

$ucf_rlt_post_id = $block->context['postId'] ?? get_the_ID();

if ( ! $ucf_rlt_post_id ) {
	return;
}

$ucf_rlt = ucf_today_get_resource_link_data( $ucf_rlt_post_id );

if ( ! $ucf_rlt['post_id'] || '' === $ucf_rlt['title'] ) {
	return;
}

// Clamp the heading level to a valid h1–h6 range; default to h3.
$ucf_rlt_level = isset( $attributes['level'] ) ? (int) $attributes['level'] : 3;
$ucf_rlt_level = min( 6, max( 1, $ucf_rlt_level ) );
$ucf_rlt_tag   = 'h' . $ucf_rlt_level;

$ucf_rlt_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'resource-link__title' )
);
?>
<<?php echo esc_attr( $ucf_rlt_tag ); ?> <?php echo $ucf_rlt_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() is already escaped. ?>><a href="<?php echo esc_url( $ucf_rlt['url'] ); ?>" rel="external noopener"><?php echo esc_html( $ucf_rlt['title'] ); ?></a></<?php echo esc_attr( $ucf_rlt_tag ); ?>>
