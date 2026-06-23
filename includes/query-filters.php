<?php
/**
 * Related Stories – STABLE + MATCHES OLD THEME BEHAVIOR
 */

add_filter('query_loop_block_query_vars', function($query_vars, $block) {

	$post_id = get_queried_object_id();
	if (!$post_id) return $query_vars;

	// Get FIRST tag only (same as old theme)
	$tags = wp_get_post_tags($post_id);
	$primary_tag = $tags[0] ?? null;

	if (!$primary_tag) return $query_vars;

	$query_vars['post__not_in'] = [$post_id];

	$query_vars['tax_query'] = [
		[
			'taxonomy' => 'post_tag',
			'field'    => 'term_id',
			'terms'    => [$primary_tag->term_id],
		],
	];

	$query_vars['orderby'] = 'date';
	$query_vars['order']   = 'DESC';

	return $query_vars;

}, 10, 2);
