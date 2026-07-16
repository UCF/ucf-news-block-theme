<?php
/**
 * Classic (non-block) post content handling.
 *
 * @package UCF_Today_Block_Theme
 */

if ( ! function_exists( 'ucf_today_post_content_depth' ) ) {
	/**
	 * Tracks whether the Post Content block is mid-render.
	 *
	 * Classic content has to be recognized by the absence of a block name, but
	 * `the_content` runs in plenty of places besides Post Content — every ACF
	 * WYSIWYG field passes through it, including the post deck and the decks of
	 * every related story in the footer. Those all parse to the same nameless
	 * blocks, so the block name alone cannot tell them apart from the post body.
	 * Tracking the render depth of Post Content itself can.
	 *
	 * @param int $delta Amount to adjust the depth by.
	 * @return int Current depth; greater than zero while Post Content renders.
	 */
	function ucf_today_post_content_depth( $delta = 0 ) {
		static $depth = 0;

		$depth = max( 0, $depth + (int) $delta );

		return $depth;
	}
}

if ( ! function_exists( 'ucf_today_open_post_content_scope' ) ) {
	/**
	 * Opens the Post Content scope before the block renders.
	 *
	 * Only the block actually rendering opens a scope. Another filter returning
	 * a non-null value short-circuits `render_block()`, and the block's render
	 * never runs — so `render_block_core/post-content` never fires and the scope
	 * would never close, leaving every later nameless block on the page treated
	 * as post content. Hence the guard, and hence running last: at an earlier
	 * priority this would open the scope before a later filter had the chance to
	 * short-circuit, and the guard would never see it.
	 *
	 * @param string|null $pre_render   Pre-rendered content, or null to continue.
	 * @param array       $parsed_block Block about to render.
	 * @return string|null The unchanged $pre_render value.
	 */
	function ucf_today_open_post_content_scope( $pre_render, $parsed_block ) {
		if ( null !== $pre_render ) {
			return $pre_render;
		}

		if ( 'core/post-content' === ( $parsed_block['blockName'] ?? '' ) ) {
			ucf_today_post_content_depth( 1 );
		}

		return $pre_render;
	}
}

add_filter( 'pre_render_block', 'ucf_today_open_post_content_scope', PHP_INT_MAX, 2 );

if ( ! function_exists( 'ucf_today_close_post_content_scope' ) ) {
	/**
	 * Closes the Post Content scope once the block has rendered.
	 *
	 * @param string $block_content Rendered Post Content HTML.
	 * @return string The unchanged $block_content.
	 */
	function ucf_today_close_post_content_scope( $block_content ) {
		ucf_today_post_content_depth( -1 );

		return $block_content;
	}
}
add_filter( 'render_block_core/post-content', 'ucf_today_close_post_content_scope', 10, 1 );

if ( ! function_exists( 'ucf_today_wrap_classic_content' ) ) {
	/**
	 * Constrains classic-editor post content to the theme's `contentSize`.
	 *
	 * Content written in the classic editor carries no block delimiters, so
	 * `parse_blocks()` returns a single nameless block whose raw markup lands
	 * directly inside the Post Content block. Post Content is full-width so that
	 * blocks can align wide and full, which leaves that legacy markup resolving
	 * floats and percentage widths against the whole viewport rather than the
	 * text column it was authored for.
	 *
	 * Wrapping the markup restores a `contentSize`-wide containing block. The
	 * wrapper is deliberately a plain container rather than a layout one: core's
	 * constrained-layout rules force `margin-left`/`margin-right` to
	 * `auto !important` on a layout container's direct children, which would
	 * flatten the negative outdents the legacy float styles depend on.
	 *
	 * @param string $block_content Rendered HTML for the block.
	 * @param array  $block         Parsed block, including its `blockName`.
	 * @return string Block HTML, wrapped when the block holds classic content.
	 */
	function ucf_today_wrap_classic_content( $block_content, $block ) {
		if ( 0 === ucf_today_post_content_depth() ) {
			return $block_content;
		}

		$block_name = $block['blockName'] ?? null;

		if ( null !== $block_name && 'core/freeform' !== $block_name ) {
			return $block_content;
		}

		// The whitespace between blocks is nameless too; leave it alone.
		if ( '' === trim( $block_content ) ) {
			return $block_content;
		}

		return '<div class="entry-content__classic">' . $block_content . '</div>';
	}
}
add_filter( 'render_block', 'ucf_today_wrap_classic_content', 10, 2 );
