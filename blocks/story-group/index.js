/**
 * Editor integration for the ucf-today/story-group block.
 *
 * No build step: uses global `wp.*` packages and wp.element.createElement
 * directly. The server render in render.php is authoritative.
 */
( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { createElement: el, Fragment, useState, useEffect } = wp.element;
	const { InspectorControls, useBlockProps } = wp.blockEditor;
	const { PanelBody, SelectControl, RangeControl, Notice, CheckboxControl, ComboboxControl, BaseControl, Spinner } = wp.components;
	const { useSelect } = wp.data;
	const ServerSideRender = wp.serverSideRender;
	const { __ } = wp.i18n;
	const { decodeEntities } = wp.htmlEntities;

	const QUERY_MODE_OPTIONS = [
		{ label: __( 'Latest posts', 'ucf-news-block-theme' ), value: 'latest' },
		{ label: __( 'Categories', 'ucf-news-block-theme' ), value: 'category' },
		{ label: __( 'Tags', 'ucf-news-block-theme' ), value: 'tags' },
		{ label: __( 'Primary tag (current post)', 'ucf-news-block-theme' ), value: 'primary_tag' },
	];

	const TAXONOMY_BY_MODE = {
		category: 'category',
		tags: 'post_tag',
	};

	const VARIANT_OPTIONS = [
		{ label: __( 'Feature (image left, text right)', 'ucf-news-block-theme' ), value: 'feature' },
		{ label: __( 'Card (image, title, excerpt)', 'ucf-news-block-theme' ), value: 'stacked' },
		{ label: __( 'Card — title only', 'ucf-news-block-theme' ), value: 'stacked-title' },
		{ label: __( 'Inline (thumbnail + title)', 'ucf-news-block-theme' ), value: 'inline' },
	];

	const ORDERBY_OPTIONS = [
		{ label: __( 'Date', 'ucf-news-block-theme' ), value: 'date' },
		{ label: __( 'Title', 'ucf-news-block-theme' ), value: 'title' },
		{ label: __( 'Last modified', 'ucf-news-block-theme' ), value: 'modified' },
		{ label: __( 'Random', 'ucf-news-block-theme' ), value: 'rand' },
	];

	const ORDER_OPTIONS = [
		{ label: __( 'Newest first', 'ucf-news-block-theme' ), value: 'desc' },
		{ label: __( 'Oldest first', 'ucf-news-block-theme' ), value: 'asc' },
	];

	const TERM_LIST_SCROLL_STYLE = {
		maxHeight: '16rem',
		overflowY: 'auto',
	};

	/**
	 * Renders a scrollable list of term checkboxes inside a BaseControl field.
	 */
	function TermCheckboxList( { terms, keyPrefix, checked, onToggle } ) {
		return el(
			'div',
			{ style: TERM_LIST_SCROLL_STYLE },
			terms.map( ( term ) =>
				el( CheckboxControl, {
					key: `${ keyPrefix }-${ term.id }`,
					label: term.label,
					checked,
					onChange: ( isChecked ) => onToggle( term.id, isChecked ),
				} )
			)
		);
	}

	/**
	 * Debounces a value so rapid input (e.g. search typing) does not trigger a
	 * network request on every keystroke.
	 */
	function useDebouncedValue( value, delayMs ) {
		const [ debounced, setDebounced ] = useState( value );

		useEffect( () => {
			const timer = setTimeout( () => setDebounced( value ), delayMs );
			return () => clearTimeout( timer );
		}, [ value, delayMs ] );

		return debounced;
	}

	/**
	 * Searchable multi-select term picker. Suggestions are ordered by usage
	 * count (most common first). Uses ComboboxControl for search so the filter
	 * input keeps focus while results load.
	 */
	function TermPicker( { taxonomy, label, termIds, onChange } ) {
		const [ filterValue, setFilterValue ] = useState( '' );
		const debouncedFilter = useDebouncedValue( filterValue, 250 );
		const selectedIds = termIds || [];
		const isCategory = 'category' === taxonomy;

		const { terms, isResolving } = useSelect(
			( select ) => {
				const core = select( 'core' );
				const query = {
					per_page: 100,
					hide_empty: ! isCategory,
					orderby: isCategory ? 'name' : 'count',
					order: isCategory ? 'asc' : 'desc',
				};

				if ( debouncedFilter ) {
					query.search = debouncedFilter;
				}

				return {
					terms: core.getEntityRecords( 'taxonomy', taxonomy, query ),
					isResolving: core.isResolving( 'getEntityRecords', [
						'taxonomy',
						taxonomy,
						query,
					] ),
				};
			},
			[ taxonomy, debouncedFilter, isCategory ]
		);

		const selectedTerms = useSelect(
			( select ) => {
				const ids = termIds || [];

				if ( ! ids.length ) {
					return [];
				}

				const core = select( 'core' );
				return ids
					.map( ( id ) => core.getEntityRecord( 'taxonomy', taxonomy, id ) )
					.filter( Boolean )
					.sort( ( a, b ) => b.count - a.count );
			},
			[ taxonomy, termIds ]
		);

		const formatTermLabel = ( term ) => {
			const name = decodeEntities( term.name );
			const countLabel =
				typeof term.count === 'number'
					? ` (${ term.count })`
					: '';
			return `${ name }${ countLabel }`;
		};

		const suggestionTerms = ( terms || [] ).filter(
			( term ) => ! selectedIds.includes( term.id )
		);

		const comboboxOptions = suggestionTerms.map( ( term ) => ( {
			value: String( term.id ),
			label: formatTermLabel( term ),
		} ) );

		const toggleTerm = ( termId, checked ) => {
			const next = checked
				? [ ...selectedIds, termId ]
				: selectedIds.filter( ( id ) => id !== termId );
			onChange( next );
		};

		const addTerm = ( value ) => {
			const termId = Number( value );
			if ( ! termId || selectedIds.includes( termId ) ) {
				return;
			}
			onChange( [ ...selectedIds, termId ] );
			setFilterValue( '' );
		};

		const availableTerms = suggestionTerms.map( ( term ) => ( {
			id: term.id,
			label: formatTermLabel( term ),
		} ) );

		const selectedTermItems = selectedTerms.map( ( term ) => ( {
			id: term.id,
			label: formatTermLabel( term ),
		} ) );

		return el(
			Fragment,
			null,
			selectedTermItems.length > 0 &&
				el(
					BaseControl,
					{
						label: __( 'Selected', 'ucf-news-block-theme' ),
						id: 'story-group-term-picker-selected',
					},
					el( TermCheckboxList, {
						terms: selectedTermItems,
						keyPrefix: 'selected',
						checked: true,
						onToggle: toggleTerm,
					} )
				),
			el( ComboboxControl, {
				label,
				help: isCategory
					? __( 'Search or select from the list below. Child categories are included when querying posts.', 'ucf-news-block-theme' )
					: __( 'Search by name to add items. Suggestions are ordered by popularity.', 'ucf-news-block-theme' ),
				value: null,
				options: comboboxOptions,
				onChange: addTerm,
				onFilterValueChange: setFilterValue,
				allowReset: false,
			} ),
			isResolving && el( Spinner, null ),
			! isResolving &&
				! debouncedFilter &&
				availableTerms.length > 0 &&
				el(
					BaseControl,
					{
						label: isCategory
							? __( 'All categories', 'ucf-news-block-theme' )
							: __( 'Popular tags', 'ucf-news-block-theme' ),
						id: 'story-group-term-picker-suggestions',
					},
					el( TermCheckboxList, {
						terms: availableTerms,
						keyPrefix: 'popular',
						checked: false,
						onToggle: toggleTerm,
					} )
				),
			! isResolving &&
				debouncedFilter &&
				! suggestionTerms.length &&
				el(
					Notice,
					{ status: 'info', isDismissible: false },
					__( 'No matching items found.', 'ucf-news-block-theme' )
				)
		);
	}

	registerBlockType( 'ucf-today/story-group', {
		edit: function ( props ) {
			const { attributes, setAttributes, context } = props;
			const queryMode = attributes.queryMode || 'latest';
			const taxonomy = TAXONOMY_BY_MODE[ queryMode ];
			const needsTerms = 'category' === queryMode || 'tags' === queryMode;
			const needsPostContext = 'primary_tag' === queryMode;
			const hasTerms = ( attributes.termIds || [] ).length > 0;
			const hasPostContext = !! ( context && context.postId );

			const controls = el(
				InspectorControls,
				null,
				el(
					PanelBody,
					{ title: __( 'Query', 'ucf-news-block-theme' ), initialOpen: true },
					el( SelectControl, {
						label: __( 'Source', 'ucf-news-block-theme' ),
						value: queryMode,
						options: QUERY_MODE_OPTIONS,
						onChange: ( value ) =>
							setAttributes( {
								queryMode: value,
								termIds: [],
								excludeContextPost: 'primary_tag' === value,
							} ),
					} ),
					needsPostContext &&
						! hasPostContext &&
						el(
							Notice,
							{ status: 'warning', isDismissible: false },
							__(
								'Primary tag mode uses the current post. Place this block on a single post or inside a Query Loop.',
								'ucf-news-block-theme'
							)
						),
					needsTerms &&
						! hasTerms &&
						el(
							Notice,
							{ status: 'warning', isDismissible: false },
							__(
								'Select at least one category or tag to show stories.',
								'ucf-news-block-theme'
							)
						),
					needsTerms &&
						taxonomy &&
						el( TermPicker, {
							taxonomy,
							label:
								'category' === queryMode
									? __( 'Categories', 'ucf-news-block-theme' )
									: __( 'Tags', 'ucf-news-block-theme' ),
							termIds: attributes.termIds,
							onChange: ( termIds ) => setAttributes( { termIds } ),
						} ),
					el( RangeControl, {
						label: __( 'Number of stories', 'ucf-news-block-theme' ),
						value: attributes.perPage,
						onChange: ( perPage ) => setAttributes( { perPage } ),
						min: 1,
						max: 12,
					} ),
					el( SelectControl, {
						label: __( 'Order by', 'ucf-news-block-theme' ),
						value: attributes.orderBy,
						options: ORDERBY_OPTIONS,
						onChange: ( orderBy ) => setAttributes( { orderBy } ),
					} ),
					attributes.orderBy !== 'rand' &&
						el( SelectControl, {
							label: __( 'Order', 'ucf-news-block-theme' ),
							value: attributes.order,
							options: ORDER_OPTIONS,
							onChange: ( order ) => setAttributes( { order } ),
						} )
				),
				el(
					PanelBody,
					{ title: __( 'Layout', 'ucf-news-block-theme' ), initialOpen: true },
					el( RangeControl, {
						label: __( 'Columns', 'ucf-news-block-theme' ),
						value: attributes.columns,
						onChange: ( columns ) => setAttributes( { columns } ),
						min: 1,
						max: 6,
					} ),
					el( SelectControl, {
						label: __( 'Story layout', 'ucf-news-block-theme' ),
						value: attributes.variant,
						options: VARIANT_OPTIONS,
						onChange: ( variant ) => setAttributes( { variant } ),
					} )
				)
			);

			const preview = el(
				'div',
				useBlockProps(),
				el( ServerSideRender, {
					block: 'ucf-today/story-group',
					attributes,
					urlQueryArgs:
						context && context.postId ? { post_id: context.postId } : undefined,
				} )
			);

			return el( Fragment, null, controls, preview );
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp );
