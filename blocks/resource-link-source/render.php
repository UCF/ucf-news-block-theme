<?php
/**
 * Server-side render for the ucf-today/resource-link-source block.
 *
 * Outputs a Resource Link's source — the first term of the `sources`
 * taxonomy — as plain, semantic text rather than the linked term list
 * core/post-terms produces. Optionally precedes it with the source icon
 * (the term's `source_icon` ACF image). Designed to run inside a core Query
 * Loop of `ucf_resource_link` posts, reading the current post from the
 * `postId` context.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

if ( ! function_exists( 'ucf_today_get_resource_link_data' ) ) {
	return;
}

$ucf_rls_post_id = $block->context['postId'] ?? get_the_ID();

if ( ! $ucf_rls_post_id ) {
	return;
}

$ucf_rls = ucf_today_get_resource_link_data( $ucf_rls_post_id );

if ( ! $ucf_rls['post_id'] || '' === $ucf_rls['source'] ) {
	return;
}

$ucf_rls_show_icon = ! isset( $attributes['showIcon'] ) || (bool) $attributes['showIcon'];

$ucf_rls_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'resource-link__source' )
);
?>
<p <?php echo $ucf_rls_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() is already escaped. ?>>
	<?php if ( $ucf_rls_show_icon && '' !== $ucf_rls['source_icon'] ) : ?>
		<img class="resource-link__source-icon" src="<?php echo esc_url( $ucf_rls['source_icon'] ); ?>" alt="" width="20" height="20" decoding="async" loading="lazy">
	<?php endif; ?>
	<span class="resource-link__source-name"><?php echo esc_html( $ucf_rls['source'] ); ?></span>
</p>
