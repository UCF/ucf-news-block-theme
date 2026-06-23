<?php
/**
 * Server-side render for the ucf-today/resource-links block.
 *
 * Lists the most recently published Resource Links (external news coverage of
 * UCF), newest first. Each link is a single <article> whose only linked
 * element is its title (pointing at the external URL), followed by the link
 * description and the source name — mirroring how the legacy theme surfaced
 * external stories while keeping the markup semantic per CLAUDE.md.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

if ( ! function_exists( 'ucf_today_get_latest_resource_links' ) ) {
	return;
}

$ucf_rl_count = isset( $attributes['numberOfItems'] ) ? max( 1, (int) $attributes['numberOfItems'] ) : 4;
$ucf_rl_type  = isset( $attributes['resourceType'] ) ? (string) $attributes['resourceType'] : '';
$ucf_rl_show_description = ! isset( $attributes['showDescription'] ) || (bool) $attributes['showDescription'];
$ucf_rl_posts = ucf_today_get_latest_resource_links( $ucf_rl_count, $ucf_rl_type );

if ( empty( $ucf_rl_posts ) ) {
	return;
}

$ucf_rl_heading = isset( $attributes['heading'] ) ? trim( (string) $attributes['heading'] ) : '';

$ucf_rl_wrapper = get_block_wrapper_attributes( array( 'class' => 'resource-links' ) );
?>
<section <?php echo $ucf_rl_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() is already escaped. ?>>
	<?php if ( '' !== $ucf_rl_heading ) : ?>
		<h2 class="resource-links__heading"><?php echo esc_html( $ucf_rl_heading ); ?></h2>
	<?php endif; ?>

	<ul class="resource-links__list">
		<?php
		foreach ( $ucf_rl_posts as $ucf_rl_post ) :
			$ucf_rl = ucf_today_get_resource_link_data( $ucf_rl_post );

			if ( ! $ucf_rl['post_id'] || '' === $ucf_rl['title'] ) {
				continue;
			}
			?>
			<li class="resource-links__item">
				<article class="resource-link">
					<h3 class="resource-link__title">
						<a href="<?php echo esc_url( $ucf_rl['url'] ); ?>" rel="external noopener"><?php echo esc_html( $ucf_rl['title'] ); ?></a>
					</h3>

					<?php if ( $ucf_rl_show_description && '' !== $ucf_rl['description'] ) : ?>
						<p class="resource-link__description"><?php echo esc_html( $ucf_rl['description'] ); ?></p>
					<?php endif; ?>

					<?php if ( '' !== $ucf_rl['source'] ) : ?>
						<p class="resource-link__source">
							<?php if ( '' !== $ucf_rl['source_icon'] ) : ?>
								<img class="resource-link__source-icon" src="<?php echo esc_url( $ucf_rl['source_icon'] ); ?>" alt="" width="20" height="20" decoding="async" loading="lazy">
							<?php endif; ?>
							<span class="resource-link__source-name"><?php echo esc_html( $ucf_rl['source'] ); ?></span>
						</p>
					<?php endif; ?>
				</article>
			</li>
		<?php endforeach; ?>
	</ul>
</section>
