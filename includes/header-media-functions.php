<?php
/**
 * Functions for resolving a post's header media (image or video).
 *
 * Adapted from the Today-Child-Theme `today_get_post_header_media()` helper
 * by Jo Dickson.
 */

if ( ! function_exists( 'ucf_today_get_post_header_media_data' ) ) {
	/**
	 * Returns the resolved header media data for a post.
	 *
	 * A post's header is either an image or a video, controlled by the
	 * `header_media_type` ACF field (defaulting to image). Only the data for
	 * the selected, populated media type is returned.
	 *
	 * @since 1.0.0
	 *
	 * @param int|WP_Post $post A post ID or WP_Post object.
	 * @return array {
	 *     @type string $type     Either 'image' or 'video'.
	 *     @type int    $image_id Attachment ID for the header image (0 if none).
	 *     @type string $video    Embed markup for the header video ('' if none).
	 *     @type string $caption  Caption text for the media ('' if none).
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

		$header_type = get_field( 'header_media_type', $post );
		$header_type = ( 'video' === $header_type ) ? 'video' : 'image';
		$media['type'] = $header_type;
		if ( 'video' === $header_type ) {
			// ACF oEmbed fields return ready-to-print embed markup.
			$media['video'] = (string) get_field( 'post_header_video_url', $post );
		} else {
			$img = get_field( 'post_header_image', $post );

			if ( $img && ! empty( $img['ID'] ) ) {
				$media['image_id'] = (int) $img['ID'];
				$media['caption']  = wptexturize( (string) ( $img['caption'] ?? '' ) );
			}
		}

		return $media;
	}
}
