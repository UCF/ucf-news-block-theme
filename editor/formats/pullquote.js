/**
 * Inline pullquote rich-text format for core/paragraph.
 *
 * Wraps the selected text in <span class="ucf-pullquote"> so it can float
 * inside the paragraph with theme CSS (legacy span + float behavior).
 */
( function ( wp ) {
	const { registerFormatType, toggleFormat } = wp.richText;
	const { RichTextToolbarButton } = wp.blockEditor;
	const { createElement: el } = wp.element;
	const { __ } = wp.i18n;
	const { useSelect } = wp.data;

	const FORMAT_NAME = 'ucf-today/pullquote';

	function PullquoteFormatButton( props ) {
		const { isActive, value, onChange, onFocus } = props;

		const blockName = useSelect( function ( select ) {
			const block = select( 'core/block-editor' ).getSelectedBlock();
			return block ? block.name : null;
		}, [] );

		if ( blockName !== 'core/paragraph' ) {
			return null;
		}

		return el( RichTextToolbarButton, {
			icon: 'format-quote',
			title: __( 'Pullquote', 'ucf-news-block-theme' ),
			onClick: function () {
				onChange(
					toggleFormat( value, {
						type: FORMAT_NAME,
					} )
				);
			},
			isActive: isActive,
			onFocus: onFocus,
		} );
	}

	registerFormatType( FORMAT_NAME, {
		title: __( 'Pullquote', 'ucf-news-block-theme' ),
		tagName: 'span',
		className: 'ucf-pullquote',
		edit: PullquoteFormatButton,
	} );
} )( window.wp );
