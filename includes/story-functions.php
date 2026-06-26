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
	 * thumbnail prefers the header/thumbnail image field, then the post's
	 * featured image.
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

		if ( function_exists( 'ucf_today_get_post_header_thumbnail_data' ) ) {
			$thumbnail        = ucf_today_get_post_header_thumbnail_data( $post->ID );
			$data['image_id'] = (int) $thumbnail['image_id'];
		}
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

if ( ! function_exists( 'ucf_today_get_post_primary_tag' ) ) {
	/**
	 * Returns the editorial primary tag for a post.
	 *
	 * Prefers the ACF `post_primary_tag` field. Does not fall back to other
	 * assigned tags; broader related-story matching is handled by the
	 * story-group block's `primary_tag` query cascade.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post ID.
	 * @return WP_Term|null Primary tag term, or null when none is available.
	 */
	function ucf_today_get_post_primary_tag( $post_id ) {
		$post_id = absint( $post_id );

		if ( ! $post_id || 'post' !== get_post_type( $post_id ) ) {
			return null;
		}

		$tag = null;

		if ( function_exists( 'get_field' ) ) {
			$tag = get_field( 'post_primary_tag', $post_id );
		}

		return ( $tag instanceof WP_Term ) ? $tag : null;
	}
}

if ( ! function_exists( 'ucf_today_get_story_group_primary_tag_fallback_sets' ) ) {
	/**
	 * Returns tag ID sets to try for related stories, in priority order.
	 *
	 * 1. The editorial primary tag (ACF), when set.
	 * 2. All other tags assigned to the post. When no primary tag is set,
	 *    all assigned tags are used in a single query.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post ID.
	 * @return int[][] Ordered tag ID sets for successive queries.
	 */
	function ucf_today_get_story_group_primary_tag_fallback_sets( $post_id ) {
		$post_id = absint( $post_id );

		if ( ! $post_id || 'post' !== get_post_type( $post_id ) ) {
			return array();
		}

		$sets       = array();
		$primary_id = 0;

		if ( function_exists( 'get_field' ) ) {
			$primary = get_field( 'post_primary_tag', $post_id );
			if ( $primary instanceof WP_Term ) {
				$primary_id = (int) $primary->term_id;
				$sets[]     = array( $primary_id );
			}
		}

		$all_tag_ids = wp_get_post_tags( $post_id, array( 'fields' => 'ids' ) );
		$all_tag_ids = array_values( array_filter( array_map( 'intval', (array) $all_tag_ids ) ) );

		if ( empty( $all_tag_ids ) ) {
			return $sets;
		}

		$other_tag_ids = $primary_id
			? array_values( array_diff( $all_tag_ids, array( $primary_id ) ) )
			: $all_tag_ids;

		if ( ! empty( $other_tag_ids ) && ( empty( $sets ) || $other_tag_ids !== $sets[0] ) ) {
			$sets[] = $other_tag_ids;
		}

		return $sets;
	}
}

if ( ! function_exists( 'ucf_today_query_story_group_posts' ) ) {
	/**
	 * Runs the story-group query, with fallbacks for `primary_tag` mode.
	 *
	 * Tries, in order: primary tag, other tags, the post's categories, then
	 * the latest posts when no tag matches are available.
	 *
	 * @since 1.0.0
	 *
	 * @param array $attributes      Story-group block attributes.
	 * @param int   $context_post_id Post ID for context-driven modes.
	 * @return WP_Query|null Query with results, or null when nothing matches.
	 */
	function ucf_today_query_story_group_posts( $attributes, $context_post_id = 0 ) {
		$query_mode = (string) ( $attributes['queryMode'] ?? 'latest' );

		if ( 'primary_tag' !== $query_mode ) {
			$args = ucf_today_get_story_group_query_args( $attributes, $context_post_id );
			if ( ! $args ) {
				return null;
			}

			$query = new WP_Query( $args );
			return $query->have_posts() ? $query : null;
		}

		$tag_sets = ucf_today_get_story_group_primary_tag_fallback_sets( $context_post_id );

		foreach ( $tag_sets as $tag_ids ) {
			$args = ucf_today_get_story_group_query_args( $attributes, $context_post_id, $tag_ids );
			if ( ! $args ) {
				continue;
			}

			$query = new WP_Query( $args );
			if ( $query->have_posts() ) {
				return $query;
			}
		}

		$categories = get_the_category( $context_post_id );
		if ( ! empty( $categories ) ) {
			$args = ucf_today_get_story_group_query_args(
				$attributes,
				$context_post_id,
				null,
				array_map( 'intval', wp_list_pluck( $categories, 'term_id' ) )
			);

			if ( $args ) {
				$query = new WP_Query( $args );
				if ( $query->have_posts() ) {
					return $query;
				}
			}
		}

		$latest_args = ucf_today_get_story_group_query_args(
			array_merge( $attributes, array( 'queryMode' => 'latest' ) ),
			$context_post_id
		);

		if ( ! $latest_args ) {
			return null;
		}

		$query = new WP_Query( $latest_args );
		return $query->have_posts() ? $query : null;
	}
}

if ( ! function_exists( 'ucf_today_get_story_group_query_args' ) ) {
	/**
	 * Builds WP_Query arguments for the story-group block.
	 *
	 * Query modes:
	 *   - latest       Most recent posts.
	 *   - category     Posts in editor-selected categories (`termIds`).
	 *   - tags         Posts with editor-selected tags (`termIds`).
	 *   - primary_tag  Posts sharing the context post's primary tag (ACF), then
	 *                  any of its other tags, then its categories, then the
	 *                  latest posts when no tag matches are available. Use
	 *                  `ucf_today_query_story_group_posts()` to run the full cascade.
	 *
	 * Filtered modes return null when no terms can be resolved.
	 *
	 * @since 1.0.0
	 *
	 * @param array      $attributes        Story-group block attributes.
	 * @param int        $context_post_id   Post ID for `primary_tag` mode.
	 * @param int[]|null $tag_ids           Optional tag IDs for `primary_tag` mode.
	 * @param int[]|null $category_ids      Optional category IDs for `primary_tag` mode.
	 * @return array|null WP_Query arguments, or null when the query cannot run.
	 */
	function ucf_today_get_story_group_query_args( $attributes, $context_post_id = 0, $tag_ids = null, $category_ids = null ) {
		$attributes = wp_parse_args(
			$attributes,
			array(
				'queryMode'          => 'latest',
				'termIds'            => array(),
				'perPage'            => 4,
				'orderBy'            => 'date',
				'order'              => 'desc',
				'excludeContextPost' => false,
			)
		);

		$context_post_id = absint( $context_post_id );
		$query_mode      = (string) $attributes['queryMode'];
		$tax_query       = array();

		switch ( $query_mode ) {
			case 'primary_tag':
				if ( null !== $category_ids ) {
					$term_ids = array_values( array_filter( array_map( 'intval', (array) $category_ids ) ) );
					if ( ! empty( $term_ids ) ) {
						$tax_query[] = array(
							'taxonomy'         => 'category',
							'field'            => 'term_id',
							'terms'            => $term_ids,
							'include_children' => true,
							'operator'         => 'IN',
						);
					}
				} elseif ( null !== $tag_ids ) {
					$term_ids = array_values( array_filter( array_map( 'intval', (array) $tag_ids ) ) );
					if ( ! empty( $term_ids ) ) {
						$tax_query[] = array(
							'taxonomy' => 'post_tag',
							'field'    => 'term_id',
							'terms'    => $term_ids,
							'operator' => 'IN',
						);
					}
				} else {
					$tag      = ucf_today_get_post_primary_tag( $context_post_id );
					$term_ids = $tag ? array( (int) $tag->term_id ) : array();
					if ( ! empty( $term_ids ) ) {
						$tax_query[] = array(
							'taxonomy' => 'post_tag',
							'field'    => 'term_id',
							'terms'    => $term_ids,
							'operator' => 'IN',
						);
					}
				}
				break;

			case 'category':
				$term_ids = array_filter( array_map( 'intval', (array) $attributes['termIds'] ) );
				if ( ! empty( $term_ids ) ) {
					$tax_query[] = array(
						'taxonomy'         => 'category',
						'field'            => 'term_id',
						'terms'            => $term_ids,
						'include_children' => true,
					);
				}
				break;

			case 'tags':
				$term_ids = array_filter( array_map( 'intval', (array) $attributes['termIds'] ) );
				if ( ! empty( $term_ids ) ) {
					$tax_query[] = array(
						'taxonomy' => 'post_tag',
						'field'    => 'term_id',
						'terms'    => $term_ids,
					);
				}
				break;

			case 'latest':
			default:
				break;
		}

		if ( empty( $tax_query ) && in_array( $query_mode, array( 'category', 'tags', 'primary_tag' ), true ) ) {
			return null;
		}

		$orderby_whitelist = array( 'date', 'title', 'modified', 'rand' );
		$order_whitelist   = array( 'ASC', 'DESC' );
		$orderby           = in_array( $attributes['orderBy'], $orderby_whitelist, true ) ? $attributes['orderBy'] : 'date';
		$order             = strtoupper( (string) $attributes['order'] );
		$order             = in_array( $order, $order_whitelist, true ) ? $order : 'DESC';

		$args = array(
			'post_type'           => 'post',
			'posts_per_page'      => max( 1, (int) $attributes['perPage'] ),
			'orderby'             => $orderby,
			'order'               => $order,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		);

		if ( ! empty( $attributes['excludeContextPost'] ) && $context_post_id && 'post' === get_post_type( $context_post_id ) ) {
			$args['post__not_in'] = array( $context_post_id );
		}

		if ( ! empty( $tax_query ) ) {
			$args['tax_query'] = $tax_query;
		}

		return $args;
	}
}
