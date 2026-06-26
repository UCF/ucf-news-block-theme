<?php
/**
 * Functions for resolving the data a Story block needs to render a post.
 *
 * A "Story" is any post surfaced in a list (home page, archives, inline
 * related content). This helper normalizes the fields the ucf-today/story
 * block uses across all of its layout variants so the render template stays
 * free of data-resolution logic.
 */

if ( ! function_exists( 'ucf_today_get_story_data' ) ) {
	/**
	 * Returns normalized display data for a single story (post).
	 *
	 * The excerpt prefers the editorial deck (`post_header_deck` ACF field) and
	 * falls back to the post's standard excerpt when no deck is set. The
	 * thumbnail reuses the `post_header_image` ACF field via the header media
	 * helper — including when the header is a video — then the featured image.
	 * The Story block renders a blank placeholder when neither is available.
	 *
	 * @since 1.0.0
	 *
	 * @param int|WP_Post $post A post ID or WP_Post object.
	 * @return array {
	 *     @type int    $post_id   The resolved post ID (0 if unresolved).
	 *     @type string $title     The post title.
	 *     @type string $permalink The post permalink.
	 *     @type string $excerpt   Deck text, falling back to the post excerpt.
	 *     @type int    $image_id  Attachment ID for the thumbnail (0 if none).
	 *     @type string $category  First category name ('' if none).
	 *     @type string $date_iso  Published date in ISO 8601 (for <time datetime>).
	 *     @type string $date_label Human-readable published date.
	 * }
	 */
	function ucf_today_get_story_data( $post ) {
		if ( is_numeric( $post ) ) {
			$post = get_post( $post );
		}

		$data = array(
			'post_id'    => 0,
			'title'      => '',
			'permalink'  => '',
			'excerpt'    => '',
			'image_id'   => 0,
			'category'   => '',
			'date_iso'   => '',
			'date_label' => '',
		);

		if ( ! $post instanceof WP_Post ) {
			return $data;
		}

		$data['post_id']   = $post->ID;
		$data['title']     = get_the_title( $post );
		$data['permalink'] = (string) get_permalink( $post );

		// Excerpt: prefer the editorial deck, fall back to the post excerpt.
		$deck = '';
		if ( function_exists( 'get_field' ) ) {
			$deck = trim( wp_strip_all_tags( (string) get_field( 'post_header_deck', $post->ID ) ) );
		}
		$data['excerpt'] = ( '' !== $deck ) ? $deck : trim( get_the_excerpt( $post ) );

		// Thumbnail: reuse the resolved header image when available.
		if ( function_exists( 'ucf_today_get_post_header_media_data' ) ) {
			$media            = ucf_today_get_post_header_media_data( $post->ID );
			$data['image_id'] = (int) $media['image_id'];
		}
		// Fall back to the core featured image if no ACF header image is set.
		if ( ! $data['image_id'] && has_post_thumbnail( $post->ID ) ) {
			$data['image_id'] = (int) get_post_thumbnail_id( $post->ID );
		}

		$categories = get_the_category( $post->ID );
		if ( ! empty( $categories ) ) {
			$data['category'] = $categories[0]->name;
		}

		$data['date_iso']   = (string) get_the_date( 'c', $post );
		$data['date_label'] = (string) get_the_date( '', $post );

		return $data;
	}
}
