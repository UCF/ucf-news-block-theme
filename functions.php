<?php
/**
 * Theme setup.
 *
 * @package UCF_Today_Block_Theme
 */

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
