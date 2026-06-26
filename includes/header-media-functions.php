<?php
/**
 * Functions for resolving a post's header media (image or video).
 *
 * Adapted from the Today-Child-Theme `today_get_post_header_media()` helper
 * by Jo Dickson.
 */

if ( ! function_exists( 'ucf_today_resolve_post_header_image_field' ) ) {
	/**
	 * Resolves the Header/Thumbnail Image ACF field to an attachment ID and caption.
	 *
	 * Used for image headers and as the list thumbnail when header media is video.
	 *
	 * @since 1.0.0
	 *
	 * @param int|WP_Post $post A post ID or WP_Post object.
	 * @return array {
	 *     @type int    $image_id Attachment ID (0 if none).
	 *     @type string $caption  Image caption text ('' if none).
	 * }
	 */
	function ucf_today_resolve_post_header_image_field( $post ) {
		$resolved = array(
			'image_id' => 0,
			'caption'  => '',
		);

		if ( is_numeric( $post ) ) {
			$post = get_post( $post );
		}

		if ( ! $post || ! function_exists( 'get_field' ) ) {
			return $resolved;
		}

		$img = get_field( 'post_header_image', $post );

		if ( is_numeric( $img ) ) {
			$resolved['image_id'] = (int) $img;
			return $resolved;
		}

		if ( is_array( $img ) && ! empty( $img['ID'] ) ) {
			$resolved['image_id'] = (int) $img['ID'];
			$resolved['caption']  = wptexturize( (string) ( $img['caption'] ?? '' ) );
		}

		return $resolved;
	}
}

if ( ! function_exists( 'ucf_today_get_post_header_media_data' ) ) {
	/**
	 * Returns the resolved header media data for a post.
	 *
	 * A post's header is either an image or a video, controlled by the
	 * `header_media_type` ACF field (defaulting to image). Video headers still
	 * expose `post_header_image` as `image_id` for story list thumbnails.
	 *
	 * @since 1.0.0
	 *
	 * @param int|WP_Post $post A post ID or WP_Post object.
	 * @return array {
	 *     @type string $type     Either 'image' or 'video'.
	 *     @type int    $image_id Attachment ID for the header/thumbnail image (0 if none).
	 *     @type string $video    Embed markup for the header video ('' if none).
	 *     @type string $caption  Caption text for the header image ('' if none).
	 * }
	 */
	function ucf_today_get_post_header_media_data( $post ) {
		if ( is_numeric( $post ) ) {
			$post = get_post( $post );
		}

		$media = array(
			'type'     => 'image',
			'image_id' => 0,
			'video'    => '',
			'caption'  => '',
		);

		if ( ! $post || ! function_exists( 'get_field' ) ) {
			return $media;
		}

		$header_type     = get_field( 'header_media_type', $post );
		$header_type     = ( 'video' === $header_type ) ? 'video' : 'image';
		$media['type']   = $header_type;
		$header_image    = ucf_today_resolve_post_header_image_field( $post );
		$media['image_id'] = $header_image['image_id'];

		if ( 'video' === $header_type ) {
			// ACF oEmbed fields return ready-to-print embed markup.
			$media['video'] = (string) get_field( 'post_header_video_url', $post );
		} else {
			$media['caption'] = $header_image['caption'];
		}

		return $media;
	}
}
