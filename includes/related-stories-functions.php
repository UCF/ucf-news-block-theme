<?php
/**
 * Related Stories query helpers for the core Query Loop pattern.
 *
 * The Related Stories pattern uses a real `core/query` block. Per-post tag
 * matching (primary tag, then first assigned tag) is applied at runtime via
 * `query_loop_block_query_vars` because Query Loop attributes are static.
 */

if ( ! function_exists( 'ucf_today_normalize_tag_term' ) ) {
	/**
	 * Normalizes an ACF taxonomy value to a WP_Term.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $tag Raw field value.
	 * @return WP_Term|null
	 */
	function ucf_today_normalize_tag_term( $tag ) {
		if ( $tag instanceof WP_Term ) {
			return $tag;
		}

		if ( is_numeric( $tag ) ) {
			$term = get_term( (int) $tag, 'post_tag' );
			return ( $term instanceof WP_Term && ! is_wp_error( $term ) ) ? $term : null;
		}

		if ( is_array( $tag ) ) {
			if ( isset( $tag['term_id'] ) ) {
				return ucf_today_normalize_tag_term( $tag['term_id'] );
			}

			if ( isset( $tag['ID'] ) ) {
				return ucf_today_normalize_tag_term( $tag['ID'] );
			}
		}

		return null;
	}
}

if ( ! function_exists( 'ucf_today_get_related_stories_context_post_id' ) ) {
	/**
	 * Resolves the single-post context ID for related stories queries.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	function ucf_today_get_related_stories_context_post_id() {
		if ( is_singular( 'post' ) ) {
			$post_id = get_queried_object_id();
			if ( $post_id ) {
				return $post_id;
			}
		}

		global $post;

		if ( $post instanceof WP_Post && 'post' === $post->post_type ) {
			return (int) $post->ID;
		}

		return 0;
	}
}

if ( ! function_exists( 'ucf_today_get_related_stories_tag' ) ) {
	/**
	 * Resolves the tag used to query related stories for a post.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post ID.
	 * @return WP_Term|null Tag term, or null when none is available.
	 */
	function ucf_today_get_related_stories_tag( $post_id ) {
		$post_id = absint( $post_id );

		if ( ! $post_id || 'post' !== get_post_type( $post_id ) ) {
			return null;
		}

		$tag = null;

		if ( function_exists( 'get_field' ) ) {
			$tag = ucf_today_normalize_tag_term( get_field( 'post_primary_tag', $post_id ) );
		}

		if ( ! $tag ) {
			$tags = wp_get_post_tags( $post_id );
			$tag  = $tags[0] ?? null;
		}

		return ( $tag instanceof WP_Term ) ? $tag : null;
	}
}

if ( ! function_exists( 'ucf_today_get_related_stories_query_args' ) ) {
	/**
	 * Builds WP_Query arguments for the Related Stories Query Loop.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Context post ID.
	 * @return array|null Query arguments, or null when no tag can be resolved.
	 */
	function ucf_today_get_related_stories_query_args( $post_id ) {
		$post_id = absint( $post_id );
		$tag     = ucf_today_get_related_stories_tag( $post_id );

		if ( ! $tag ) {
			return null;
		}

		return array(
			'post_type'           => 'post',
			'posts_per_page'      => 8,
			'orderby'             => 'date',
			'order'               => 'DESC',
			'post__not_in'        => array( $post_id ),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'tax_query'           => array(
				array(
					'taxonomy' => 'post_tag',
					'field'    => 'term_id',
					'terms'    => array( (int) $tag->term_id ),
				),
			),
		);
	}
}

if ( ! function_exists( 'ucf_today_related_stories_has_results' ) ) {
	/**
	 * Checks whether related stories exist for a post.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Context post ID.
	 * @return bool
	 */
	function ucf_today_related_stories_has_results( $post_id ) {
		$args = ucf_today_get_related_stories_query_args( $post_id );

		if ( ! $args ) {
			return false;
		}

		$query = new WP_Query( $args );

		return $query->have_posts();
	}
}

if ( ! function_exists( 'ucf_today_is_related_stories_query_context' ) ) {
	/**
	 * Whether a Query Loop block context is the Related Stories query.
	 *
	 * The filter runs against the post-template block, so the marker lives in
	 * the parent query block's `query` attribute — not on className.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Block $block Block instance.
	 * @return bool
	 */
	function ucf_today_is_related_stories_query_context( $block ) {
		if ( ! $block instanceof WP_Block ) {
			return false;
		}

		return ! empty( $block->context['query']['relatedStories'] );
	}
}

if ( ! function_exists( 'ucf_today_is_related_stories_section_block' ) ) {
	/**
	 * Whether a parsed block array is the Related Stories section group.
	 *
	 * @since 1.0.0
	 *
	 * @param array $block Parsed block data.
	 * @return bool
	 */
	function ucf_today_is_related_stories_section_block( $block ) {
		if ( empty( $block['blockName'] ) || 'core/group' !== $block['blockName'] ) {
			return false;
		}

		$class_name = (string) ( $block['attrs']['className'] ?? '' );

		return str_contains( $class_name, 'related-stories' );
	}
}

if ( ! function_exists( 'ucf_today_filter_related_stories_query_loop' ) ) {
	/**
	 * Applies per-post tag filtering to the Related Stories Query Loop.
	 *
	 * @since 1.0.0
	 *
	 * @param array    $query Query arguments.
	 * @param WP_Block $block Block instance.
	 * @param int      $page  Current page.
	 * @return array
	 */
	function ucf_today_filter_related_stories_query_loop( $query, $block, $page ) {
		unset( $page );

		if ( ! ucf_today_is_related_stories_query_context( $block ) || ! is_singular( 'post' ) ) {
			return $query;
		}

		$post_id = ucf_today_get_related_stories_context_post_id();
		$args    = ucf_today_get_related_stories_query_args( $post_id );

		if ( ! $args ) {
			$query['post__in'] = array( 0 );
			return $query;
		}

		return array_merge( $query, $args );
	}
}
add_filter( 'query_loop_block_query_vars', 'ucf_today_filter_related_stories_query_loop', 10, 3 );

if ( ! function_exists( 'ucf_today_maybe_hide_related_stories_section' ) ) {
	/**
	 * Hides the Related Stories section when no tag or no matches exist.
	 *
	 * @since 1.0.0
	 *
	 * @param string $block_content Rendered block HTML.
	 * @param array  $block         Parsed block data.
	 * @return string
	 */
	function ucf_today_maybe_hide_related_stories_section( $block_content, $block ) {
		if ( ! ucf_today_is_related_stories_section_block( $block ) || ! is_singular( 'post' ) ) {
			return $block_content;
		}

		if ( ! ucf_today_related_stories_has_results( ucf_today_get_related_stories_context_post_id() ) ) {
			return '';
		}

		return $block_content;
	}
}
add_filter( 'render_block', 'ucf_today_maybe_hide_related_stories_section', 10, 2 );
