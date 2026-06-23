<?php
/**
 * Filters the Query Loop block's query vars for the Related Stories template part.
 *
 * Scopes results to posts sharing the current post's first tag, excludes the
 * current post, and orders by date descending — matching the previous theme's
 * related-stories behavior.
 *
 * @since 1.0.0
 *
 * @param array    $query_vars The query vars passed to WP_Query.
 * @param WP_Block $block      The Query Loop block instance.
 * @return array Filtered query vars, or the original if no post or tag is found.
 */
add_filter( 'query_loop_block_query_vars', function( $query_vars, $block ) {
    $post_id = get_queried_object_id();

    if ( ! $post_id ) {
        return $query_vars;
    }

    // Use only the first assigned tag to match the previous theme's behavior.
    $tags        = wp_get_post_tags( $post_id );
    $primary_tag = $tags[0] ?? null;

    if ( ! $primary_tag ) {
        return $query_vars;
    }

    $query_vars['post__not_in'] = [ $post_id ];
    $query_vars['tax_query']    = [
        [
            'taxonomy' => 'post_tag',
            'field'    => 'term_id',
            'terms'    => [ $primary_tag->term_id ],
        ],
    ];
    $query_vars['orderby'] = 'date';
    $query_vars['order']   = 'DESC';

    return $query_vars;
}, 10, 2 );
