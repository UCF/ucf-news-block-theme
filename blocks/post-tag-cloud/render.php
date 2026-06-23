<?php
/**
 * Server-side render for the ucf-today/tag-cloud block.
 *
 * Outputs the post's tags as a tag cloud. Renders nothing when the Display Tag
 * Cloud field is disabled or no tags are set.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

$ucf_tag_cloud_post_id = $block->context['postId'] ?? get_the_ID();

if ( ! $ucf_tag_cloud_post_id || ! function_exists( 'get_field' ) ) {
	return;
}

$ucf_display = (bool) get_field( 'post_display_tag_cloud', $ucf_tag_cloud_post_id );

if ( ! $ucf_display ) {
	return;
}

$ucf_count = absint( get_field( 'post_tag_cloud_count', $ucf_tag_cloud_post_id ) );
$ucf_count = $ucf_count ?: 5;

$ucf_tags = get_the_tags( $ucf_tag_cloud_post_id );

if ( empty( $ucf_tags ) ) {
	return;
}

$ucf_tags = array_slice( $ucf_tags, 0, $ucf_count );

$ucf_wrapper = get_block_wrapper_attributes( array( 'class' => 'today-tag-cloud' ) );
?>
<div <?php echo $ucf_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() is already escaped. ?>>
	<h2><?php echo esc_html__( 'More Topics', 'ucf-news-block-theme' ); ?></h2>
	<?php foreach ( $ucf_tags as $ucf_tag ) : ?>
		<?php $ucf_tag_link = get_term_link( $ucf_tag ); ?>
		<?php if ( ! is_wp_error( $ucf_tag_link ) ) : ?>
			<a href="<?php echo esc_url( $ucf_tag_link ); ?>"><?php echo esc_html( $ucf_tag->name ); ?></a>
		<?php endif; ?>
	<?php endforeach; ?>
</div>
