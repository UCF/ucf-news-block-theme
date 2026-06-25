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

if ( ! function_exists( 'ucf_today_extract_video_url_from_embed' ) ) {
	/**
	 * Extracts a video URL from raw oEmbed data or iframe markup.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Raw ACF oEmbed value (URL or embed HTML).
	 * @return string Video URL, or an empty string when none is found.
	 */
	function ucf_today_extract_video_url_from_embed( $value ) {
		$value = trim( (string) $value );

		if ( '' === $value ) {
			return '';
		}

		if ( filter_var( $value, FILTER_VALIDATE_URL ) ) {
			return $value;
		}

		if ( preg_match( '/src=["\']([^"\']+)["\']/', $value, $matches ) ) {
			return html_entity_decode( $matches[1], ENT_QUOTES, 'UTF-8' );
		}

		return '';
	}
}

if ( ! function_exists( 'ucf_today_get_oembed_thumbnail_url' ) ) {
	/**
	 * Fetches a poster image URL for a supported oEmbed video URL.
	 *
	 * Results are cached in a transient to avoid repeated remote requests.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url Video URL.
	 * @return string Thumbnail URL, or an empty string when unavailable.
	 */
	function ucf_today_get_oembed_thumbnail_url( $url ) {
		$url = esc_url_raw( trim( (string) $url ) );

		if ( '' === $url ) {
			return '';
		}

		$cache_key = 'ucf_oembed_thumb_' . md5( $url );
		$cached    = get_transient( $cache_key );

		if ( false !== $cached ) {
			return (string) $cached;
		}

		$thumbnail_url = '';

		if ( function_exists( '_wp_oembed_get_object' ) ) {
			$oembed   = _wp_oembed_get_object();
			$provider = $oembed->get_provider( $url, array( 'discover' => true ) );

			if ( $provider ) {
				$data = $oembed->fetch( $provider, $url, array( 'discover' => true ) );

				if ( $data && ! empty( $data->thumbnail_url ) ) {
					$thumbnail_url = (string) $data->thumbnail_url;
				}
			}
		}

		set_transient( $cache_key, $thumbnail_url, DAY_IN_SECONDS );

		return $thumbnail_url;
	}
}

if ( ! function_exists( 'ucf_today_get_post_header_thumbnail_data' ) ) {
	/**
	 * Resolves the list/thumbnail image for a post.
	 *
	 * Image headers use the header image. Video headers prefer the optional
	 * header/thumbnail image field; when none is set, an oEmbed poster URL is
	 * fetched from the header video URL.
	 *
	 * @since 1.0.0
	 *
	 * @param int|WP_Post $post A post ID or WP_Post object.
	 * @return array {
	 *     @type int    $image_id  Attachment ID for the thumbnail (0 if none).
	 *     @type string $image_url External thumbnail URL (for oEmbed posters).
	 * }
	 */
	function ucf_today_get_post_header_thumbnail_data( $post ) {
		if ( is_numeric( $post ) ) {
			$post = get_post( $post );
		}

		$thumbnail = array(
			'image_id'  => 0,
			'image_url' => '',
		);

		if ( ! $post || ! function_exists( 'get_field' ) ) {
			return $thumbnail;
		}

		$img = get_field( 'post_header_image', $post );

		if ( $img && ! empty( $img['ID'] ) ) {
			$thumbnail['image_id'] = (int) $img['ID'];
			return $thumbnail;
		}

		$header_type = get_field( 'header_media_type', $post );
		if ( 'video' !== $header_type ) {
			return $thumbnail;
		}

		$video_url = ucf_today_extract_video_url_from_embed(
			(string) get_field( 'post_header_video_url', $post, false )
		);

		if ( '' === $video_url ) {
			$video_url = ucf_today_extract_video_url_from_embed(
				(string) get_field( 'post_header_video_url', $post )
			);
		}

		if ( '' !== $video_url ) {
			$thumbnail['image_url'] = ucf_today_get_oembed_thumbnail_url( $video_url );
		}

		return $thumbnail;
	}
}
