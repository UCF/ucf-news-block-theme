<?php
/**
 * Server-side render for the ucf-today/story-group block.
 *
 * Resolves a post query from block attributes, then renders a grid of
 * ucf-today/story blocks. Filtered modes render nothing when no matching
 * terms can be resolved.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

if ( ! function_exists( 'ucf_today_query_story_group_posts' ) ) {
	return;
}

$ucf_story_group_context_post_id = absint( $attributes['contextPostId'] ?? 0 );

if ( ! $ucf_story_group_context_post_id ) {
	$ucf_story_group_context_post_id = absint( $block->context['postId'] ?? 0 );
}

if ( ! $ucf_story_group_context_post_id ) {
	$ucf_story_group_context_post_id = absint( get_the_ID() );
}

if ( ! $ucf_story_group_context_post_id && defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_GET['post_id'] ) && current_user_can( 'edit_posts' ) ) {
	$ucf_story_group_context_post_id = absint( wp_unslash( $_GET['post_id'] ) );
}

$ucf_story_group_query = ucf_today_query_story_group_posts( $attributes, $ucf_story_group_context_post_id );

if ( ! $ucf_story_group_query ) {
	return;
}

$ucf_story_group_columns = max( 1, min( 6, (int) ( $attributes['columns'] ?? 3 ) ) );
$ucf_story_group_variant = $attributes['variant'] ?? 'stacked';

$ucf_story_group_variants = array( 'feature', 'stacked', 'stacked-title', 'inline' );
if ( ! in_array( $ucf_story_group_variant, $ucf_story_group_variants, true ) ) {
	$ucf_story_group_variant = 'stacked';
}

$ucf_story_group_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'story-group story-group--cols-' . $ucf_story_group_columns )
);
?>
<section <?php echo $ucf_story_group_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() is already escaped. ?>>

	<div class="story-group__grid">
		<?php
		while ( $ucf_story_group_query->have_posts() ) :
			$ucf_story_group_query->the_post();

			echo render_block(
				array(
					'blockName' => 'ucf-today/story',
					'attrs'     => array(
						'variant' => $ucf_story_group_variant,
						'postId'  => get_the_ID(),
					),
				)
			);
		endwhile;
		?>
	</div>

</section>
<?php
wp_reset_postdata();
