/**
 * Editor integration for the ucf-today/resource-link-description block.
 *
 * No build step: uses the global `wp.*` packages and createElement (no JSX).
 * The server render in render.php is authoritative — the editor shows a live
 * ServerSideRender preview, forwarding the Query Loop `postId` context so each
 * item previews its own description. The block has no settings.
 */
( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { createElement: el } = wp.element;
	const { useBlockProps } = wp.blockEditor;
	const ServerSideRender = wp.serverSideRender;

	registerBlockType( 'ucf-today/resource-link-description', {
		edit: function ( props ) {
			const { attributes, context } = props;

			// Forward the Query Loop's current post to the block-renderer
			// endpoint so each item previews its own description.
			const urlQueryArgs = context && context.postId ? { post_id: context.postId } : undefined;

			return el(
				'div',
				useBlockProps(),
				el( ServerSideRender, {
					block: 'ucf-today/resource-link-description',
					attributes,
					urlQueryArgs,
				} )
			);
		},
		save: function () {
			// Server-rendered block; nothing is saved to post content.
			return null;
		},
	} );
} )( window.wp );
