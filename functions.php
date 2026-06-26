<?php
/**
 * Theme setup.
 *
 * @package UCF_Today_Block_Theme
 */

include_once get_template_directory() . '/includes/post-functions.php';
include_once get_template_directory() . '/includes/author-functions.php';
include_once get_template_directory() . '/includes/header-media-functions.php';
include_once get_template_directory() . '/includes/highlights-functions.php';
include_once get_template_directory() . '/includes/story-functions.php';
include_once get_template_directory() . '/includes/related-stories-functions.php';
include_once get_template_directory() . '/includes/resource-link-functions.php';
include_once get_template_directory() . '/includes/weather-layouts.php';

if ( ! function_exists( 'ucf_today_register_blocks' ) ) {
	/**
	 * Registers the theme's custom blocks from their block.json metadata.
	 */
	function ucf_today_register_blocks() {
		// Register the Story block's editor script with explicit dependencies
		// (no build step, so there is no generated asset manifest). The handle
		// matches the `editorScript` value in blocks/story/block.json.
		$ucf_story_editor      = '/blocks/story/index.js';
		$ucf_story_editor_path = get_template_directory() . $ucf_story_editor;
		wp_register_script(
			'ucf-today-story-editor',
			get_template_directory_uri() . $ucf_story_editor,
			array(
				'wp-blocks',
				'wp-block-editor',
				'wp-components',
				'wp-data',
				'wp-element',
				'wp-html-entities',
				'wp-i18n',
				'wp-server-side-render',
			),
			file_exists( $ucf_story_editor_path ) ? (string) filemtime( $ucf_story_editor_path ) : null,
			true
		);

		// Register the Resource Links block's editor script. The handle matches
		// the `editorScript` value in blocks/resource-links/block.json.
		$ucf_rl_editor      = '/blocks/resource-links/index.js';
		$ucf_rl_editor_path = get_template_directory() . $ucf_rl_editor;
		wp_register_script(
			'ucf-today-resource-links-editor',
			get_template_directory_uri() . $ucf_rl_editor,
			array(
				'wp-blocks',
				'wp-block-editor',
				'wp-components',
				'wp-data',
				'wp-element',
				'wp-html-entities',
				'wp-i18n',
				'wp-server-side-render',
			),
			file_exists( $ucf_rl_editor_path ) ? (string) filemtime( $ucf_rl_editor_path ) : null,
			true
		);

		// Register the editor scripts for the per-item Resource Link blocks used
		// inside a Query Loop (title / source / description). Each handle matches
		// the `editorScript` value in its block.json; all share the same
		// dependencies and render a ServerSideRender preview.
		$ucf_rl_field_blocks = array(
			'ucf-today-resource-link-title-editor'       => '/blocks/resource-link-title/index.js',
			'ucf-today-resource-link-source-editor'      => '/blocks/resource-link-source/index.js',
			'ucf-today-resource-link-description-editor' => '/blocks/resource-link-description/index.js',
		);
		foreach ( $ucf_rl_field_blocks as $ucf_rl_field_handle => $ucf_rl_field_src ) {
			$ucf_rl_field_path = get_template_directory() . $ucf_rl_field_src;
			wp_register_script(
				$ucf_rl_field_handle,
				get_template_directory_uri() . $ucf_rl_field_src,
				array(
					'wp-blocks',
					'wp-block-editor',
					'wp-components',
					'wp-element',
					'wp-i18n',
					'wp-server-side-render',
				),
				file_exists( $ucf_rl_field_path ) ? (string) filemtime( $ucf_rl_field_path ) : null,
				true
			);
		}

		register_block_type( get_template_directory() . '/blocks/post-category' );
		register_block_type( get_template_directory() . '/blocks/post-deck' );
		register_block_type( get_template_directory() . '/blocks/post-byline' );
		register_block_type( get_template_directory() . '/blocks/post-header-media' );
		register_block_type( get_template_directory() . '/blocks/post-highlights' );
		register_block_type( get_template_directory() . '/blocks/story' );
		register_block_type( get_template_directory() . '/blocks/post-tag-cloud' );

		if ( ucf_today_resource_plugins_installed() ) {
			register_block_type( get_template_directory() . '/blocks/resource-links' );
			register_block_type( get_template_directory() . '/blocks/resource-link-title' );
			register_block_type( get_template_directory() . '/blocks/resource-link-source' );
			register_block_type( get_template_directory() . '/blocks/resource-link-description' );
		}
	}
}
add_action( 'init', 'ucf_today_register_blocks' );


if ( ! function_exists( 'ucf_today_enable_resource_link_rest' ) ) {
	/**
	 * Exposes the `ucf_resource_link` post type to the REST API.
	 *
	 * The post type is registered by the UCF Resource Search plugin. Rather than
	 * patch the plugin, the theme opts it into REST here so it appears in the
	 * core Query Loop's post-type picker and can be queried by the editor — a
	 * requirement for the "In the News" archive built from Resource Link blocks.
	 *
	 * Filters `register_post_type_args`, which runs for every post type as it is
	 * registered, so this must short-circuit for everything else.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $args      Arguments passed to register_post_type().
	 * @param string $post_type Post type key being registered.
	 * @return array Possibly-modified arguments.
	 */
	function ucf_today_enable_resource_link_rest( $args, $post_type ) {
		if ( 'ucf_resource_link' !== $post_type ) {
			return $args;
		}

		$args['show_in_rest'] = true;

		// Provide a stable REST base only if the plugin hasn't set one, so we
		// don't override an intentional value upstream.
		if ( empty( $args['rest_base'] ) ) {
			$args['rest_base'] = 'resource-links';
		}

		return $args;
	}
}
add_filter( 'register_post_type_args', 'ucf_today_enable_resource_link_rest', 10, 2 );


if ( ! function_exists( 'ucf_today_block_theme_setup' ) ) {
	/**
	 * Registers basic theme supports for block-theme behavior.
	 */
	function ucf_today_block_theme_setup() {
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'editor-styles' );
		add_editor_style( 'assets/css/main.css' );
	}
}
add_action( 'after_setup_theme', 'ucf_today_block_theme_setup' );

if ( ! function_exists( 'ucf_today_block_theme_enqueue_assets' ) ) {
	/**
	 * Enqueues compiled front-end stylesheet when available.
	 */
	function ucf_today_block_theme_enqueue_assets() {
		$relative_path = '/assets/css/main.css';
		$full_path     = get_template_directory() . $relative_path;

		if ( ! file_exists( $full_path ) ) {
			return;
		}

		wp_enqueue_style(
			'ucf-today-block-theme-main',
			get_template_directory_uri() . $relative_path,
			array(),
			(string) filemtime( $full_path )
		);
	}
}
add_action( 'wp_enqueue_scripts', 'ucf_today_block_theme_enqueue_assets' );

if ( ! function_exists( 'ucf_today_scope_archive_query_loops' ) ) {
	/**
	 * Scopes the custom Query Loops in the category/tag archive templates to the
	 * current term.
	 *
	 * The archive templates split posts into a lead story and a 9-up grid, which
	 * requires custom queries (perPage/offset). A custom Query Loop does not
	 * inherit the archive term, so without this filter it returns all posts.
	 * We target only our loops by their custom `namespace` attribute (set in the
	 * category/tag templates) — `queryId` is only unique per editing context and
	 * could collide with other loops — and inject the queried term so the
	 * lead/grid split is preserved while staying scoped to the archive.
	 *
	 * @param array    $query Arguments for WP_Query, as built from the block.
	 * @param WP_Block $block The block instance.
	 * @return array Filtered query args.
	 */
	function ucf_today_scope_archive_query_loops( $query, $block ) {
		$namespace = $block->attributes['namespace'] ?? '';

		if ( ! in_array( $namespace, array( 'ucf-today/archive-lead', 'ucf-today/archive-grid' ), true ) ) {
			return $query;
		}

		$term = get_queried_object();
		if ( ! $term instanceof WP_Term ) {
			return $query;
		}

		if ( is_category() ) {
			$query['cat'] = $term->term_id;
		} elseif ( is_tag() ) {
			$query['tag_id'] = $term->term_id;
		}

		return $query;
	}
}
add_filter( 'query_loop_block_query_vars', 'ucf_today_scope_archive_query_loops', 10, 2 );
