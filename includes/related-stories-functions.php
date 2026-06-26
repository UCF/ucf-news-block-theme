<?php
/**
 * Related Stories query helpers for the core Query Loop pattern.
 *
 * The Related Stories pattern uses a real `core/query` block. Per-post tag
 * matching (primary tag, then other assigned tags) is applied at runtime via
 * `query_loop_block_query_vars` because Query Loop attributes are static.
 */

if ( ! function_exists( 'ucf_today_bootstrap_related_stories_post_id' ) ) {
	/**
	 * Caches the main single-post ID before nested Query Loops run.
	 *
	 * @since 1.0.0
	 */
	function ucf_today_bootstrap_related_stories_post_id() {
		if ( ! is_singular( 'post' ) ) {
			return;
		}

		$post_id = get_queried_object_id();

		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		if ( $post_id ) {
			$GLOBALS['ucf_today_related_stories_post_id'] = (int) $post_id;
		}
	}
}
add_action( 'template_redirect', 'ucf_today_bootstrap_related_stories_post_id', 1 );
add_action( 'wp', 'ucf_today_bootstrap_related_stories_post_id' );

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

		if ( is_object( $tag ) && isset( $tag->term_id ) ) {
			$tag = $tag->term_id;
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

if ( ! function_exists( 'ucf_today_get_post_primary_tag_meta' ) ) {
	/**
	 * Reads the raw primary tag term ID from post meta.
	 *
	 * ACF stores taxonomy fields under the field name even when `get_field()`
	 * is unavailable or returns an unexpected shape.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post ID.
	 * @return int Term ID, or 0 when unset.
	 */
	function ucf_today_get_post_primary_tag_meta( $post_id ) {
		$raw = get_post_meta( absint( $post_id ), 'post_primary_tag', true );

		if ( is_numeric( $raw ) ) {
			return (int) $raw;
		}

		return 0;
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
		if ( ! empty( $GLOBALS['ucf_today_related_stories_post_id'] ) ) {
			return (int) $GLOBALS['ucf_today_related_stories_post_id'];
		}

		if ( is_singular( 'post' ) ) {
			$post_id = get_queried_object_id();
			if ( $post_id ) {
				return (int) $post_id;
			}
		}

		$post_id = get_the_ID();
		if ( $post_id && 'post' === get_post_type( $post_id ) ) {
			return (int) $post_id;
		}

		global $post;

		if ( $post instanceof WP_Post && 'post' === $post->post_type ) {
			return (int) $post->ID;
		}

		return 0;
	}
}

if ( ! function_exists( 'ucf_today_get_related_stories_primary_tag' ) ) {
	/**
	 * Resolves the editorial primary tag only (no assigned-tag fallback).
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post ID.
	 * @return WP_Term|null
	 */
	function ucf_today_get_related_stories_primary_tag( $post_id ) {
		$post_id = absint( $post_id );

		if ( ! $post_id || 'post' !== get_post_type( $post_id ) ) {
			return null;
		}

		$tag = null;

		if ( function_exists( 'get_field' ) ) {
			$tag = ucf_today_normalize_tag_term( get_field( 'post_primary_tag', $post_id ) );
		}

		if ( ! $tag ) {
			$meta_term_id = ucf_today_get_post_primary_tag_meta( $post_id );
			if ( $meta_term_id ) {
				$tag = ucf_today_normalize_tag_term( $meta_term_id );
			}
		}

		return ( $tag instanceof WP_Term ) ? $tag : null;
	}
}

if ( ! function_exists( 'ucf_today_get_related_stories_tag_term_sets' ) ) {
	/**
	 * Returns tag ID sets to try for related stories, in priority order.
	 *
	 * 1. Editorial primary tag, when set.
	 * 2. All other assigned tags on the post (or all tags when no primary).
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post ID.
	 * @return int[][]
	 */
	function ucf_today_get_related_stories_tag_term_sets( $post_id ) {
		$post_id = absint( $post_id );

		if ( ! $post_id || 'post' !== get_post_type( $post_id ) ) {
			return array();
		}

		$sets       = array();
		$primary    = ucf_today_get_related_stories_primary_tag( $post_id );
		$primary_id = $primary ? (int) $primary->term_id : 0;

		if ( $primary_id ) {
			$sets[] = array( $primary_id );
		}

		$all_tag_ids = wp_get_post_tags( $post_id, array( 'fields' => 'ids' ) );
		$all_tag_ids = array_values( array_filter( array_map( 'intval', (array) $all_tag_ids ) ) );

		if ( empty( $all_tag_ids ) ) {
			return $sets;
		}

		$other_tag_ids = $primary_id
			? array_values( array_diff( $all_tag_ids, array( $primary_id ) ) )
			: $all_tag_ids;

		if ( ! empty( $other_tag_ids ) ) {
			$sets[] = $other_tag_ids;
		}

		return $sets;
	}
}

if ( ! function_exists( 'ucf_today_build_related_stories_query_args' ) ) {
	/**
	 * Builds a WP_Query argument set for one tag ID group.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $post_id Post ID to exclude.
	 * @param int[] $tag_ids Tag term IDs.
	 * @return array
	 */
	function ucf_today_build_related_stories_query_args( $post_id, $tag_ids ) {
		$tag_ids = array_values( array_filter( array_map( 'intval', $tag_ids ) ) );

		return array(
			'post_type'           => 'post',
			'posts_per_page'      => 8,
			'orderby'             => 'date',
			'order'               => 'DESC',
			'post__not_in'        => array( absint( $post_id ) ),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'tax_query'           => array(
				array(
					'taxonomy'         => 'post_tag',
					'field'            => 'term_id',
					'terms'            => $tag_ids,
					'operator'         => 'IN',
					'include_children' => false,
				),
			),
		);
	}
}

if ( ! function_exists( 'ucf_today_get_related_stories_query_args' ) ) {
	/**
	 * Builds WP_Query arguments for the Related Stories Query Loop.
	 *
	 * Tries primary tag first, then falls back to the post's other assigned tags.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Context post ID.
	 * @return array|null Query arguments, or null when no matches exist.
	 */
	function ucf_today_get_related_stories_query_args( $post_id ) {
		static $cache = array();

		$post_id = absint( $post_id );

		if ( ! $post_id ) {
			return null;
		}

		if ( array_key_exists( $post_id, $cache ) ) {
			return $cache[ $post_id ];
		}

		$cache[ $post_id ] = null;

		foreach ( ucf_today_get_related_stories_tag_term_sets( $post_id ) as $tag_ids ) {
			$args  = ucf_today_build_related_stories_query_args( $post_id, $tag_ids );
			$query = new WP_Query( $args );

			if ( $query->have_posts() ) {
				wp_reset_postdata();
				$cache[ $post_id ] = $args;
				break;
			}

			wp_reset_postdata();
		}

		return $cache[ $post_id ];
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
		return null !== ucf_today_get_related_stories_query_args( $post_id );
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

if ( ! function_exists( 'ucf_today_related_stories_post_template_context' ) ) {
	/**
	 * Flags Related Stories queries on post-template context.
	 *
	 * Custom `query` keys can be dropped before they reach inner blocks; the
	 * parent query wrapper class is the reliable marker.
	 *
	 * @since 1.0.0
	 *
	 * @param array         $context      Block context.
	 * @param array         $parsed_block Parsed block.
	 * @param WP_Block|null $parent_block Parent block instance.
	 * @return array
	 */
	function ucf_today_related_stories_post_template_context( $context, $parsed_block, $parent_block ) {
		if ( empty( $parsed_block['blockName'] ) || 'core/post-template' !== $parsed_block['blockName'] ) {
			return $context;
		}

		if ( ! $parent_block instanceof WP_Block ) {
			return $context;
		}

		$class_name = (string) ( $parent_block->attributes['className'] ?? '' );

		if ( ! str_contains( $class_name, 'related-stories-query' ) ) {
			return $context;
		}

		if ( ! isset( $context['query'] ) || ! is_array( $context['query'] ) ) {
			$context['query'] = array();
		}

		$context['query']['relatedStories'] = true;

		return $context;
	}
}
add_filter( 'render_block_context', 'ucf_today_related_stories_post_template_context', 10, 3 );

if ( ! function_exists( 'ucf_today_ensure_related_stories_query_flag' ) ) {
	/**
	 * Ensures customized Query Loop markup keeps the relatedStories marker.
	 *
	 * Site Editor saves can drop unknown `query` keys; the className on the
	 * query wrapper is the fallback signal.
	 *
	 * @since 1.0.0
	 *
	 * @param array $block Parsed block data.
	 * @return array
	 */
	function ucf_today_ensure_related_stories_query_flag( $block ) {
		if ( 'core/query' !== ( $block['blockName'] ?? '' ) ) {
			return $block;
		}

		$class_name = (string) ( $block['attrs']['className'] ?? '' );

		if ( str_contains( $class_name, 'related-stories-query' ) ) {
			$block['attrs']['query']['relatedStories'] = true;
		}

		return $block;
	}
}
add_filter( 'render_block_data', 'ucf_today_ensure_related_stories_query_flag', 10, 1 );

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
		$classes    = preg_split( '/\s+/', trim( $class_name ) );

		return is_array( $classes ) && in_array( 'related-stories', $classes, true );
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
	 * Skips rendering the Related Stories section when no matches exist.
	 *
	 * @since 1.0.0
	 *
	 * @param string|null $pre_render Short-circuit return value.
	 * @param array       $block      Parsed block data.
	 * @return string|null
	 */
	function ucf_today_maybe_hide_related_stories_section( $pre_render, $block ) {
		if ( null !== $pre_render ) {
			return $pre_render;
		}

		if ( ! ucf_today_is_related_stories_section_block( $block ) || ! is_singular( 'post' ) ) {
			return null;
		}

		if ( ! ucf_today_related_stories_has_results( ucf_today_get_related_stories_context_post_id() ) ) {
			return '';
		}

		return null;
	}
}
add_filter( 'pre_render_block', 'ucf_today_maybe_hide_related_stories_section', 10, 2 );
