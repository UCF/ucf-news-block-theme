<?php
/**
 * Server-side render for the ucf-today/post-byline block.
 *
 * Mirrors the byline portion of the legacy Today-Child-Theme
 * `today_get_post_meta_info()` output: a "By {author}" line plus date(s).
 * When an original publish date is set (and differs from the post's published
 * date), both the "Originally Published" and "Updated on" dates are shown.
 * Otherwise a single published date is shown alongside the author.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

$ucf_byline_post_id = $block->context['postId'] ?? get_the_ID();

if ( ! $ucf_byline_post_id ) {
	return;
}

$ucf_byline       = ucf_today_get_post_byline_data( $ucf_byline_post_id );
$ucf_byline_name  = $ucf_byline['author'];
$ucf_byline_pub   = $ucf_byline['published_date'];
$ucf_byline_orig  = $ucf_byline['original_date'];

// Nothing meaningful to render without at least a date.
if ( ! $ucf_byline_pub ) {
	return;
}

$ucf_byline_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'post-header__byline' )
);

$ucf_byline_sep = '<span class="post-header__byline-sep" aria-hidden="true">|</span>';
?>
<div <?php echo $ucf_byline_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() is already escaped. ?>>
	<p class="post-header__byline-primary">
		<?php if ( $ucf_byline_name ) : ?>
			<span class="post-header__byline-author"><?php echo esc_html__( 'By', 'ucf-news-block-theme' ); ?> <?php echo esc_html( $ucf_byline_name ); ?></span>
			<?php
			// When there's a single date, show it inline next to the author.
			if ( ! $ucf_byline_orig ) {
				echo wp_kses_post( $ucf_byline_sep );
			}
			?>
		<?php endif; ?>

		<?php if ( ! $ucf_byline_orig ) : ?>
			<span class="post-header__byline-date"><?php echo esc_html( $ucf_byline_pub ); ?></span>
		<?php endif; ?>
	</p>

	<?php if ( $ucf_byline_orig ) : ?>
	<p class="post-header__byline-dates">
		<span class="post-header__byline-date"><strong><?php echo esc_html__( 'Originally Published', 'ucf-news-block-theme' ); ?></strong> <?php echo esc_html( $ucf_byline_orig ); ?></span>
		<?php echo wp_kses_post( $ucf_byline_sep ); ?>
		<span class="post-header__byline-date"><strong><?php echo esc_html__( 'Updated on', 'ucf-news-block-theme' ); ?></strong> <?php echo esc_html( $ucf_byline_pub ); ?></span>
	</p>
	<?php endif; ?>
</div>
