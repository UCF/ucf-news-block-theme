<?php
/**
 * Functions for resolving post author/byline information.
 *
 * Ported from the Today-Child-Theme `today_get_post_author_data()` helper by
 * Jo Dickson, adapted for this block theme.
 */

if ( ! function_exists( 'ucf_today_get_post_author_data' ) ) {
	/**
	 * Returns author display data for a post.
	 *
	 * Resolution order:
	 *   1. Custom author meta (when "Author Type" is "Custom" and a byline is set).
	 *   2. The post's `tu_author` term (when "Author Type" is "Existing Author").
	 *   3. The original publisher's display name (only when $publisher_fallback
	 *      is true and the post is a standard post).
	 *
	 * @since 1.0.0
	 *
	 * @param int|WP_Post $post               A post ID or WP_Post object.
	 * @param bool        $publisher_fallback Whether to fall back to the original
	 *                                        publisher's name when no author term
	 *                                        or custom author data is set.
	 * @return array {
	 *     @type WP_Term|null $term  The resolved author term, if any.
	 *     @type string       $name  The author display name.
	 *     @type string       $title The author title.
	 *     @type array|null   $photo The author photo field value.
	 *     @type string       $bio   The author bio.
	 * }
	 */
	function ucf_today_get_post_author_data( $post, $publisher_fallback = false ) {
		$author_data = array(
			'term'  => null,
			'name'  => '',
			'title' => '',
			'photo' => null,
			'bio'   => '',
		);

		if ( is_numeric( $post ) ) {
			$post = get_post( $post );
		}

		if ( ! $post ) {
			return $author_data;
		}

		if ( 'post' === $post->post_type && function_exists( 'get_field' ) ) {
			$post_author_type = get_field( 'post_author_type', $post ) ?: 'custom';

			if ( 'custom' === $post_author_type ) {
				$custom_author_name = get_field( 'post_author_byline', $post );

				// Require at least a name to proceed.
				if ( $custom_author_name ) {
					$author_data['term']  = null;
					$author_data['name']  = wptexturize( $custom_author_name );
					$author_data['title'] = wptexturize( (string) get_field( 'post_author_title', $post ) );
					$author_data['photo'] = get_field( 'post_author_photo', $post );
					$author_data['bio']   = get_field( 'post_author_bio', $post );
				}
			} else {
				$author_terms = wp_get_post_terms( $post->ID, 'tu_author' );

				if ( ! is_wp_error( $author_terms ) ) {
					$author_term = $author_terms[0] ?? null;

					// Require at least a name to proceed.
					if ( $author_term && $author_term->name ) {
						$author_data['term']  = $author_term;
						$author_data['name']  = wptexturize( $author_term->name );
						$author_data['title'] = wptexturize( (string) get_field( 'author_title', $author_term ) );
						$author_data['photo'] = get_field( 'author_photo', $author_term );
						$author_data['bio']   = get_field( 'author_bio', $author_term );
					}
				}
			}
		}

		if ( $publisher_fallback && ! $author_data['name'] && 'post' === $post->post_type ) {
			$original_publisher_name = get_the_author_meta( 'display_name', $post->post_author );

			if ( $original_publisher_name ) {
				$author_data['name'] = wptexturize( $original_publisher_name );
			}
		}

		return $author_data;
	}
}

if ( ! function_exists( 'ucf_today_get_post_byline_data' ) ) {
	/**
	 * Returns the resolved byline data for a post: author name and dates.
	 *
	 * Mirrors the date logic from the legacy Today-Child-Theme
	 * `today_get_post_meta_info()`: the post's published date is treated as the
	 * "updated" date, and the ACF `post_header_publish_date` field (when set)
	 * supplies the original publish date. The original date is only considered
	 * meaningful when it differs from the published date.
	 *
	 * @since 1.0.0
	 *
	 * @param int|WP_Post $post A post ID or WP_Post object.
	 * @return array {
	 *     @type string      $author         Resolved author name (may be empty).
	 *     @type string      $published_date Formatted post published date.
	 *     @type string|null $original_date  Formatted original publish date, or
	 *                                       null when none is set / it matches
	 *                                       the published date.
	 * }
	 */
	function ucf_today_get_post_byline_data( $post ) {
		if ( is_numeric( $post ) ) {
			$post = get_post( $post );
		}

		$byline_data = array(
			'author'         => '',
			'published_date' => '',
			'original_date'  => null,
		);

		if ( ! $post ) {
			return $byline_data;
		}

		$author_data           = ucf_today_get_post_author_data( $post, true );
		$byline_data['author'] = $author_data['name'] ?? '';

		$date_format    = 'F j, Y';
		$published_date = date_i18n( $date_format, strtotime( $post->post_date ) );

		$byline_data['published_date'] = $published_date;

		$orig_date_val = function_exists( 'get_field' ) ? get_field( 'post_header_publish_date', $post ) : '';

		if ( ! empty( $orig_date_val ) ) {
			$original_date = date_i18n( $date_format, strtotime( $orig_date_val ) );

			// Only meaningful when it differs from the published/updated date.
			if ( $original_date !== $published_date ) {
				$byline_data['original_date'] = $original_date;
			}
		}

		return $byline_data;
	}
}
