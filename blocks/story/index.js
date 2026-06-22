/**
 * Editor integration for the ucf-today/story block.
 *
 * No build step: this file uses the global `wp.*` packages and
 * wp.element.createElement directly (no JSX). The server render in render.php
 * is authoritative — the editor shows a live ServerSideRender preview and
 * exposes two controls in the sidebar:
 *
 *   - Layout: the display variant (feature / stacked / card-title / inline).
 *   - Story:  a post search/picker, shown only when the block is NOT inside a
 *             Query Loop (a loop supplies the post via context instead).
 */
( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { createElement: el, Fragment, useState } = wp.element;
	const { InspectorControls, useBlockProps } = wp.blockEditor;
	const { PanelBody, SelectControl, ComboboxControl, Notice } = wp.components;
	const { useSelect } = wp.data;
	const ServerSideRender = wp.serverSideRender;
	const { __ } = wp.i18n;

	const VARIANT_OPTIONS = [
		{ label: __( 'Feature (image left, text right)', 'ucf-news-block-theme' ), value: 'feature' },
		{ label: __( 'Card (image, title, excerpt)', 'ucf-news-block-theme' ), value: 'stacked' },
		{ label: __( 'Card — title only', 'ucf-news-block-theme' ), value: 'stacked-title' },
		{ label: __( 'Inline (thumbnail + title)', 'ucf-news-block-theme' ), value: 'inline' },
	];

	/**
	 * Sidebar control for hand-picking a post. Searches published posts and
	 * stores the chosen post's ID on the `postId` attribute.
	 */
	function StoryPicker( { postId, onChange } ) {
		const [ search, setSearch ] = useState( '' );

		const { posts, selected } = useSelect(
			( select ) => {
				const core = select( 'core' );
				return {
					posts: core.getEntityRecords( 'postType', 'post', {
						per_page: 20,
						search: search || undefined,
						status: 'publish',
						orderby: search ? 'relevance' : 'date',
					} ),
					selected: postId ? core.getEntityRecord( 'postType', 'post', postId ) : null,
				};
			},
			[ search, postId ]
		);

		const options = ( posts || [] ).map( ( post ) => ( {
			value: post.id,
			label: post.title.rendered || __( '(no title)', 'ucf-news-block-theme' ),
		} ) );

		// Ensure the currently selected post is always present as an option.
		if ( selected && ! options.some( ( o ) => o.value === selected.id ) ) {
			options.unshift( {
				value: selected.id,
				label: selected.title.rendered || __( '(no title)', 'ucf-news-block-theme' ),
			} );
		}

		return el( ComboboxControl, {
			label: __( 'Story', 'ucf-news-block-theme' ),
			help: __( 'Search and select the post to display.', 'ucf-news-block-theme' ),
			value: postId || null,
			options,
			onFilterValueChange: ( value ) => setSearch( value ),
			onChange: ( value ) => onChange( value ? Number( value ) : 0 ),
		} );
	}

	registerBlockType( 'ucf-today/story', {
		edit: function ( props ) {
			const { attributes, setAttributes, context } = props;
			// A bare singular page/post injects its own postId into block
			// context, so postId alone does NOT mean we're in a loop. The
			// reliable Query Loop signal is queryId.
			const inLoop = !! ( context && context.queryId );

			const controls = el(
				InspectorControls,
				null,
				el(
					PanelBody,
					{ title: __( 'Story settings', 'ucf-news-block-theme' ), initialOpen: true },
					el( SelectControl, {
						label: __( 'Layout', 'ucf-news-block-theme' ),
						value: attributes.variant,
						options: VARIANT_OPTIONS,
						onChange: ( variant ) => setAttributes( { variant } ),
					} ),
					inLoop
						? el(
								Notice,
								{ status: 'info', isDismissible: false },
								__(
									'This block is inside a Query Loop, so it automatically shows each post in the loop.',
									'ucf-news-block-theme'
								)
						  )
						: el( StoryPicker, {
								postId: attributes.postId,
								onChange: ( postId ) => setAttributes( { postId } ),
						  } )
				)
			);

			const needsPost = ! inLoop && ! attributes.postId;

			const preview = needsPost
				? el(
						'p',
						useBlockProps( { className: 'story story--placeholder' } ),
						__( 'Select a story in the block settings to display it.', 'ucf-news-block-theme' )
				  )
				: el(
						'div',
						useBlockProps(),
						el( ServerSideRender, {
							block: 'ucf-today/story',
							attributes,
							// Give the server render the loop's post when present.
							urlQueryArgs: context && context.postId ? { post_id: context.postId } : undefined,
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
