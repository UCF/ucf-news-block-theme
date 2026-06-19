<?php
/**
 * Functions for resolving a post's highlights.
 *
 * Adapted from the Today-Child-Theme `today_get_post_highlights()` helper
 * by Jim Barnes.
 */

if ( ! function_exists( 'ucf_today_get_post_highlights' ) ) {
	/**
	 * Returns the post's highlight entries.
	 *
	 * Highlights are stored in the `post_highlights` ACF repeater, each row
	 * holding a `highlight_text` WYSIWYG value.
	 *
	 * @since 1.0.0
	 *
	 * @param int|WP_Post $post A post ID or WP_Post object.
	 * @return string[] Array of highlight HTML strings (empty when none set).
	 */
	function ucf_today_get_post_highlights( $post ) {
		if ( is_numeric( $post ) ) {
			$post = get_post( $post );
		}

		if ( ! $post || ! function_exists( 'get_field' ) ) {
			return array();
		}

		$highlights = get_field( 'post_highlights', $post );

		if ( empty( $highlights ) || ! is_array( $highlights ) ) {
			return array();
		}

		$items = array();

		foreach ( $highlights as $highlight ) {
			$text = trim( (string) ( $highlight['highlight_text'] ?? '' ) );

			if ( '' !== $text ) {
				$items[] = $text;
			}
		}

		return $items;
	}
}
