<?php
/**
 * Functions for resolving Resource Link data and querying the latest links.
 *
 * A "Resource Link" (post type `ucf_resource_link`, provided by the UCF
 * Resource Search plugin) records an external news source that has covered
 * UCF. Each link carries:
 *
 *   - an external URL          (post meta `ucf_resource_link_url`)
 *   - a short description      (ACF field `ucf_resource_link_description`)
 *   - a source                 (first term of the `sources` taxonomy, which
 *                               itself carries a `source_icon` image via ACF)
 *
 * These helpers keep data resolution out of the block render template, mirror
 * how the legacy Today child theme surfaced external stories (title +
 * description + source name), and stay testable in `includes/`.
 */

if ( ! function_exists( 'ucf_today_get_resource_link_data' ) ) {
	/**
	 * Returns normalized display data for a single resource link.
	 *
	 * @since 1.0.0
	 *
	 * @param int|WP_Post $post A resource link post ID or WP_Post object.
	 * @return array {
	 *     @type int    $post_id      The resolved post ID (0 if unresolved).
	 *     @type string $title        The link title.
	 *     @type string $url          External URL, falling back to the permalink.
	 *     @type string $description  Short description ('' if none).
	 *     @type string $source       First source term name ('' if none).
	 *     @type string $source_icon  Source icon URL ('' if none).
	 *     @type string $date_iso     Published date in ISO 8601 (for <time>).
	 *     @type string $date_label   Human-readable published date.
	 * }
	 */
	function ucf_today_get_resource_link_data( $post ) {
		if ( is_numeric( $post ) ) {
			$post = get_post( $post );
		}

		$data = array(
			'post_id'     => 0,
			'title'       => '',
			'url'         => '',
			'description' => '',
			'source'      => '',
			'source_icon' => '',
			'date_iso'    => '',
			'date_label'  => '',
		);

		if ( ! $post instanceof WP_Post ) {
			return $data;
		}

		$data['post_id'] = $post->ID;
		$data['title']   = get_the_title( $post );

		// Resource links point at an external URL; fall back to the permalink
		// if the meta is somehow empty so the title is never an empty link.
		$external_url = esc_url_raw( (string) get_post_meta( $post->ID, 'ucf_resource_link_url', true ) );
		$data['url']  = ( '' !== $external_url ) ? $external_url : (string) get_permalink( $post );

		if ( function_exists( 'get_field' ) ) {
			$data['description'] = trim( wp_strip_all_tags( (string) get_field( 'ucf_resource_link_description', $post->ID ) ) );
		}

		// Source: first term of the `sources` taxonomy, plus its icon (ACF
		// image field set to return the URL).
		$sources = wp_get_post_terms( $post->ID, 'sources' );
		if ( ! is_wp_error( $sources ) && ! empty( $sources ) ) {
			$source            = $sources[0];
			$data['source']    = $source->name;

			if ( function_exists( 'get_field' ) ) {
				// Pass the term object so ACF resolves the field across its
				// supported term identifier formats.
				$icon = get_field( 'source_icon', $source );
				if ( $icon ) {
					$data['source_icon'] = esc_url_raw( (string) $icon );
				}
			}
		}

		$data['date_iso']   = (string) get_the_date( 'c', $post );
		$data['date_label'] = (string) get_the_date( '', $post );

		return $data;
	}
}

if ( ! function_exists( 'ucf_today_get_latest_resource_links' ) ) {
	/**
	 * Returns the most recently published resource links.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $count Maximum number of links to return.
	 * @param string $type  Optional `resource_link_types` term slug to filter
	 *                      by (e.g. 'external-story'). Empty for all types.
	 * @return WP_Post[] Resource link posts, newest first.
	 */
	function ucf_today_get_latest_resource_links( $count = 4, $type = '' ) {
		$count = max( 1, (int) $count );

		$args = array(
			'post_type'              => 'ucf_resource_link',
			'post_status'            => 'publish',
			'posts_per_page'         => $count,
			'orderby'                => 'date',
			'order'                  => 'DESC',
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_term_cache' => true,
		);

		$type = sanitize_title( (string) $type );
		if ( '' !== $type ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'resource_link_types',
					'field'    => 'slug',
					'terms'    => $type,
				),
			);
		}

		$query = new WP_Query( $args );

		return $query->posts;
	}
}

if ( ! function_exists( 'ucf_today_resource_plugins_installed' ) ) {
	/**
	 * Checks if the required resource plugins are installed and active.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if both plugins are active, false otherwise.
	 */
	function ucf_today_resource_plugins_installed() {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		if (
			(
				is_plugin_active( 'UCF-Resource-Search-Plugin/ucf-resource-search.php' ) ||
				is_plugin_active( 'UCF-Resource-Search/ucf-resource-search.php' ) // Legacy plugin slug
			) &&
			is_plugin_active( 'UCF-Source-Taxonomy-Plugin/ucf-sources-taxonomy.php' )
		) {
			return true;
		}

		return false;
	}
}
