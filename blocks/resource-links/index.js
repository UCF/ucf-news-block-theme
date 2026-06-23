/**
 * Editor integration for the ucf-today/resource-links block.
 *
 * No build step: this file uses the global `wp.*` packages and
 * wp.element.createElement directly (no JSX). The server render in render.php
 * is authoritative — the editor shows a live ServerSideRender preview and
 * exposes two controls in the sidebar:
 *
 *   - Heading:           optional section heading text (blank hides it).
 *   - Number of links:   how many of the latest resource links to show.
 */
( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { createElement: el, Fragment } = wp.element;
	const { InspectorControls, useBlockProps } = wp.blockEditor;
	const { PanelBody, TextControl, RangeControl, SelectControl, ToggleControl } = wp.components;
	const { useSelect } = wp.data;
	const ServerSideRender = wp.serverSideRender;
	const { __ } = wp.i18n;
	const { decodeEntities } = wp.htmlEntities;

	registerBlockType( 'ucf-today/resource-links', {
		edit: function ( props ) {
			const { attributes, setAttributes } = props;

			// Populate the type filter from the resource_link_types taxonomy.
			const terms = useSelect( ( select ) =>
				select( 'core' ).getEntityRecords( 'taxonomy', 'resource_link_types', {
					per_page: 100,
					orderby: 'name',
					order: 'asc',
				} )
			, [] );

			const typeOptions = [
				{ label: __( 'All types', 'ucf-news-block-theme' ), value: '' },
				...( terms || [] ).map( ( term ) => ( {
					label: decodeEntities( term.name ),
					value: term.slug,
				} ) ),
			];

			// Keep the saved slug selectable while terms are still loading.
			if (
				attributes.resourceType &&
				! typeOptions.some( ( o ) => o.value === attributes.resourceType )
			) {
				typeOptions.push( {
					label: attributes.resourceType,
					value: attributes.resourceType,
				} );
			}

			const controls = el(
				InspectorControls,
				null,
				el(
					PanelBody,
					{ title: __( 'Resource links settings', 'ucf-news-block-theme' ), initialOpen: true },
					el( TextControl, {
						label: __( 'Heading', 'ucf-news-block-theme' ),
						help: __( 'Optional. Leave blank to hide the heading.', 'ucf-news-block-theme' ),
						value: attributes.heading,
						onChange: ( heading ) => setAttributes( { heading } ),
					} ),
					el( SelectControl, {
						label: __( 'Type', 'ucf-news-block-theme' ),
						help: __( 'Filter by resource link type.', 'ucf-news-block-theme' ),
						value: attributes.resourceType,
						options: typeOptions,
						onChange: ( resourceType ) => setAttributes( { resourceType } ),
					} ),
					el( RangeControl, {
						label: __( 'Number of links', 'ucf-news-block-theme' ),
						value: attributes.numberOfItems,
						min: 1,
						max: 12,
						onChange: ( numberOfItems ) =>
							setAttributes( { numberOfItems: numberOfItems || 1 } ),
					} ),
					el( ToggleControl, {
						label: __( 'Show description', 'ucf-news-block-theme' ),
						checked: attributes.showDescription,
						onChange: ( showDescription ) => setAttributes( { showDescription } ),
					} )
				)
			);

			const preview = el(
				'div',
				useBlockProps(),
				el( ServerSideRender, {
					block: 'ucf-today/resource-links',
					attributes,
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
