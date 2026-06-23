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
include_once get_template_directory() . '/includes/resource-link-functions.php';

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

		register_block_type( get_template_directory() . '/blocks/post-category' );
		register_block_type( get_template_directory() . '/blocks/post-deck' );
		register_block_type( get_template_directory() . '/blocks/post-byline' );
		register_block_type( get_template_directory() . '/blocks/post-header-media' );
		register_block_type( get_template_directory() . '/blocks/post-highlights' );
		register_block_type( get_template_directory() . '/blocks/story' );
		register_block_type( get_template_directory() . '/blocks/resource-links' );
	}
}
add_action( 'init', 'ucf_today_register_blocks' );


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
