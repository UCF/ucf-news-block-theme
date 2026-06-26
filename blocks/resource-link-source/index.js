/**
 * Editor integration for the ucf-today/resource-link-source block.
 *
 * No build step: uses the global `wp.*` packages and createElement (no JSX).
 * The server render in render.php is authoritative — the editor shows a live
 * ServerSideRender preview, forwarding the Query Loop `postId` context so each
 * item previews its own source. A sidebar toggle controls the source icon.
 */
( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { createElement: el, Fragment } = wp.element;
	const { InspectorControls, useBlockProps } = wp.blockEditor;
	const { PanelBody, ToggleControl } = wp.components;
	const ServerSideRender = wp.serverSideRender;
	const { __ } = wp.i18n;

	registerBlockType( 'ucf-today/resource-link-source', {
		edit: function ( props ) {
			const { attributes, setAttributes, context } = props;

			const controls = el(
				InspectorControls,
				null,
				el(
					PanelBody,
					{ title: __( 'Source settings', 'ucf-news-block-theme' ), initialOpen: true },
					el( ToggleControl, {
						label: __( 'Show source icon', 'ucf-news-block-theme' ),
						checked: attributes.showIcon,
						onChange: ( showIcon ) => setAttributes( { showIcon } ),
					} )
				)
			);

			// Forward the Query Loop's current post to the block-renderer
			// endpoint so each item previews its own source.
			const urlQueryArgs = context && context.postId ? { post_id: context.postId } : undefined;

			const preview = el(
				'div',
				useBlockProps(),
				el( ServerSideRender, {
					block: 'ucf-today/resource-link-source',
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
