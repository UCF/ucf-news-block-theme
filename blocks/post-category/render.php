<?php
/**
 * Server-side render for the ucf-today/post-category block.
 *
 * Outputs the post's primary category name as plain, semantic text — a
 * paragraph rather than the linked term list that core/post-terms produces.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

$ucf_category_post_id = $block->context['postId'] ?? get_the_ID();

if ( ! $ucf_category_post_id ) {
	return;
}

$ucf_category_terms = get_the_category( $ucf_category_post_id );

// Nothing to render without a category.
if ( empty( $ucf_category_terms ) ) {
	return;
}

$ucf_category_name    = $ucf_category_terms[0]->name;
$ucf_category_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'post-header__category' )
);
?>
<p <?php echo wp_kses_data( $ucf_category_wrapper ); ?>><?php echo esc_html( $ucf_category_name ); ?></p>
