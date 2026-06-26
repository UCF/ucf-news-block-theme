/**
 * Editor integration for the ucf-today/resource-link-title block.
 *
 * No build step: uses the global `wp.*` packages and createElement (no JSX).
 * The server render in render.php is authoritative — the editor shows a live
 * ServerSideRender preview, forwarding the Query Loop `postId` context so each
 * item previews its own resource link. A sidebar control sets the heading
 * level.
 */
( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { createElement: el, Fragment } = wp.element;
	const { InspectorControls, useBlockProps } = wp.blockEditor;
	const { PanelBody, SelectControl } = wp.components;
	const ServerSideRender = wp.serverSideRender;
	const { __ } = wp.i18n;

	registerBlockType( 'ucf-today/resource-link-title', {
		edit: function ( props ) {
			const { attributes, setAttributes, context } = props;

			const controls = el(
				InspectorControls,
				null,
				el(
					PanelBody,
					{ title: __( 'Title settings', 'ucf-news-block-theme' ), initialOpen: true },
					el( SelectControl, {
						label: __( 'Heading level', 'ucf-news-block-theme' ),
						value: String( attributes.level ),
						options: [ 1, 2, 3, 4, 5, 6 ].map( ( n ) => ( {
							label: 'H' + n,
							value: String( n ),
						} ) ),
						onChange: ( level ) => setAttributes( { level: parseInt( level, 10 ) } ),
					} )
				)
			);

			// Forward the Query Loop's current post to the block-renderer
			// endpoint so each item previews its own resource link rather than
			// the page being edited.
			const urlQueryArgs = context && context.postId ? { post_id: context.postId } : undefined;

			const preview = el(
				'div',
				useBlockProps(),
				el( ServerSideRender, {
					block: 'ucf-today/resource-link-title',
					attributes,
					urlQueryArgs,
				} )
			);

			return el( Fragment, null, controls, preview );
		},
		save: function () {
			// Server-rendered block; nothing is saved to post content.
			return null;
		},
	} );
} )( window.wp );
