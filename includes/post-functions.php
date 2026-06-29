<?php
/**
 * Stores functions related to post custom fields, including registering the fields
 */

/**
 * Adds the ACF Post Custom Fields field group
 * and associated fields.
 *
 * Ported from the Today-Child-Theme plugin by Cadie Stockman, 2018.
 * Updated and maintained by the UCF Web Communications team.
 *
 * @since 1.0.0
 * @author Jim Barnes
 */
function ucf_today_add_post_custom_fields() {
	if ( function_exists( 'acf_add_local_field_group' ) ) {

		// Create the array to add the fields to
		$fields = array();

		// Adds Header Content tab
		$fields[] = array(
			'key'               => 'field_5c813914b0cd8',
			'label'             => 'Header Content',
			'type'              => 'tab',
		);

		// Adds Original Publish Date read only field
		$fields[] = array(
			'key'               => 'field_5c813a34c81af',
			'label'             => 'Original Publish Date',
			'name'              => 'post_header_publish_date',
			'type'              => 'read_only',
			'instructions'      => 'The date the post was originally published. Changing the <a href="#submitdiv">\'Published on\' date</a> above will update the dates listed in the story’s header and bump this story to the top of lists where it\'s referenced.',
			'display_type'      => 'text',
		);

		// Adds Deck field
		$fields[] = array(
			'key'               => 'field_5c813eaac81b1',
			'label'             => 'Deck',
			'name'              => 'post_header_deck',
			'type'              => 'wysiwyg',
			'instructions'      => 'Appears below the title on a single post. Is also used as excerpt text within lists of posts.',
			'toolbar'           => 'inline_text',
			'media_upload'      => 0,
		);

		// Adds Header Media tab
		$fields[] = array(
			'key'               => 'field_5c81401dc81ba',
			'label'             => 'Header Media',
			'type'              => 'tab',
		);

		// Adds Header Media Type field
		$fields[] = array(
			'key'               => 'field_5c813fb7c81b9',
			'label'             => 'Header Media Type',
			'name'              => 'header_media_type',
			'type'              => 'radio',
			'instructions'      => 'Select the type of header for this story.',
			'choices'           => array(
				'image' => 'Image',
				'video' => 'Video',
			),
			'default_value'     => 'image',
		);

		// Adds Header Video field
		$fields[] = array(
			'key'               => 'field_5c814048c81bb',
			'label'             => 'Header Video',
			'name'              => 'post_header_video_url',
			'type'              => 'oembed',
			'instructions'      => 'Paste in a video URL from YouTube, Vimeo, etc. to display in place of a header image.',
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_5c813fb7c81b9',
						'operator' => '==',
						'value'    => 'video',
					),
				),
			),
		);

		// Adds Header/Thumbnail Image field
		$fields[] = array(
			'key'               => 'field_5c813f8ac81b8',
			'label'             => 'Header/Thumbnail Image',
			'name'              => 'post_header_image',
			'type'              => 'image',
			'instructions'      => 'Select or upload an image with dimensions of 1200x800 for this story. The image file size must be less than 800KB.<br><br>When the "Header Media Type" value is set to "Image", this image will be used as the header image on the story, as well as the thumbnail image when this story is displayed in lists of stories.<br><br>When the "Header Media Type" value is set to "Video", if an image is provided, the image will be used as the thumbnail when this story is displayed in lists of stories.	If no image is provided, WordPress will attempt to fetch and use a poster image dynamically based on the "Header Video" URL provided.',
			'max_width'         => 1200,
			'max_height'        => 800,
			'mime_types'        => 'jpg, jpeg',
		);

		// Adds Author tab
		$fields[] = array(
			'key'               => 'field_5c813f04c81b3',
			'label'             => 'Author',
			'type'              => 'tab',
		);

		// Adds Author Type field
		$fields[] = array(
			'key'               => 'field_60351738e9e93',
			'label'             => 'Author Type',
			'name'              => 'post_author_type',
			'type'              => 'select',
			'instructions'      => 'Choose whether to reference an existing Author\'s information, or define one-off custom information for this post.',
			'choices'           => array(
				'custom' => 'Custom',
				'term'   => 'Existing Author',
			),
			'default_value'     => false,
		);

		// Adds Author Byline field
		$fields[] = array(
			'key'               => 'field_5c813f0fc81b4',
			'label'             => 'Author Byline',
			'name'              => 'post_author_byline',
			'type'              => 'text',
			'instructions'      => 'Appears in place of post author\'s name.',
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_60351738e9e93',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
		);

		// Adds Author Title field
		$fields[] = array(
			'key'               => 'field_5c813ebec81b2',
			'label'             => 'Author Title',
			'name'              => 'post_author_title',
			'type'              => 'text',
			'instructions'      => 'Appears under the author\'s name/byline below the story\'s content, <b>only if the Author Bio is set</b>.',
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_60351738e9e93',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
		);

		// Adds Author Photo field
		$fields[] = array(
			'key'               => 'field_602fef6061511',
			'label'             => 'Author Photo',
			'name'              => 'post_author_photo',
			'type'              => 'image',
			'instructions'      => 'Appears below the story\'s content.',
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_60351738e9e93',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'wrapper'           => array(
				'width' => '35',
			),
		);

		// Adds Author Bio field
		$fields[] = array(
			'key'               => 'field_5c813f22c81b5',
			'label'             => 'Author Bio',
			'name'              => 'post_author_bio',
			'type'              => 'wysiwyg',
			'instructions'      => 'Appears below the story\'s content.<br><br>This field must be set in order to display any author information (Author Name, Title, Bio, Photo) below the story\'s content.',
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_60351738e9e93',
						'operator' => '==',
						'value'    => 'custom',
					),
				),
			),
			'wrapper'           => array(
				'width' => '65',
			),
			'tabs'              => 'text',
			'media_upload'      => 0,
		);

		// Adds Existing Author field
		$fields[] = array(
			'key'               => 'field_6035185de9e94',
			'label'             => 'Existing Author',
			'name'              => 'post_author_term',
			'type'              => 'taxonomy',
			'instructions'      => 'Choose an existing Author to assign to this post.	Name, title, photo and bio information will be referenced from the Author that\'s selected.',
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_60351738e9e93',
						'operator' => '==',
						'value'    => 'term',
					),
				),
			),
			'taxonomy'          => 'tu_author',
			'field_type'        => 'select',
			'save_terms'        => 1,
			'load_terms'        => 1,
		);

		$fields[] = array(
			'key'               => 'field_post_highlights_tab',
			'label'             => 'Highlights',
			'type'              => 'tab',
		);

		$fields[] = array(
			'key'               => 'field_post_highlights_repeater',
			'label'             => 'Highlights',
			'name'              => 'post_highlights',
			'type'              => 'repeater',
			'instructions'      => 'Add a series of highlights to be present at the beginning of the story',
			'required'          => 0,
			'layout'            => 'row',
			'button_label'      => 'Add Highlight',
			'sub_fields'        => array(
				array(
					'key'             => 'field_highlight_text',
					'label'           => 'Highlight Text',
					'name'            => 'highlight_text',
					'type'            => 'wysiwyg',
					'tabs'            => 'visual',
					'toolbar'         => 'inline_text',
					'media_upload'    => 0,
					'required'        => 0,
					'parent_repeater' => 'field_post_highlights_repeater'
				)
			)
		);

		// Adds Primary Tag tab
		$fields[] = array(
			'key'               => 'field_5c814082c81bc',
			'label'             => 'Primary Tag',
			'type'              => 'tab',
		);

		// Adds Primary Tag field
		$fields[] = array(
			'key'               => 'field_5c8140a1c81bd',
			'label'             => 'Primary Tag',
			'name'              => 'post_primary_tag',
			'type'              => 'taxonomy',
			'instructions'      => 'Select the primary tag that will be used to populate the Related Stories section.',
			'taxonomy'          => 'post_tag',
			'field_type'        => 'select',
			'add_term'          => 0,
			'allow_null'        => 1,
			'return_format'     => 'object',
			'show_in_rest'      => 1,
		);

		// Adds Source tab
		$fields[] = array(
			'key'               => 'field_5c8140dec81be',
			'label'             => 'Source',
			'type'              => 'tab',
		);

		// Adds Source field
		$fields[] = array(
			'key'               => 'field_5c8140e7c81bf',
			'label'             => 'Source',
			'name'              => 'post_source',
			'type'              => 'textarea',
			'instructions'      => 'Appears below the story content (and below the Author Bio if set).',
		);

		// Adds Tag Cloud tab
		$fields[] = array(
			'key'               => 'field_5c98fd5aeb16b',
			'label'             => 'Tag Cloud',
			'type'              => 'tab',
		);

		// Adds Display Tag Cloud field
		$fields[] = array(
			'key'               => 'field_5c98fd68eb16c',
			'label'             => 'Display Tag Cloud',
			'name'              => 'post_display_tag_cloud',
			'type'              => 'true_false',
			'instructions'      => 'Display a tag cloud under the post\'s content or source. Defaults to true.',
			'default_value'     => 1,
			'ui'                => 1,
		);

		// Adds Tag Cloud Count field
		$fields[] = array(
			'key'               => 'field_5c98fdc5eb16d',
			'label'             => 'Tag Cloud Count',
			'name'              => 'post_tag_cloud_count',
			'type'              => 'number',
			'instructions'      => 'The number of tags to show in the tag cloud. Defaults to 5.',
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_5c98fd68eb16c',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'default_value'     => 5,
			'min'               => 1,
			'step'              => 1,
		);

		// Defines Post Custom Fields field group
		$field_group = array(
			'key'                   => 'group_5c813326f2f21',
			'title'                 => 'Post Custom Fields',
			'fields'                => $fields,
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'post',
					),
				),
			),
		);

		acf_add_local_field_group( $field_group );
	}
}

add_action( 'acf/init', 'ucf_today_add_post_custom_fields' );


/**
 * Adds the ACF Main Site News field group
 * and associated fields.
 *
 * Ported from the Today-Child-Theme plugin by Cadie Stockman, 2018.
 * Updated and maintained by the UCF Web Communications team.
 *
 * @since 1.0.0
 * @author Jim Barnes
 */
function ucf_today_add_main_site_news_fields() {
	if ( function_exists( 'acf_add_local_field_group' ) ) {

		// Create the array to add the fields to
		$fields = array();

		// Adds Promote on Main Site field
		$fields[] = array(
			'key'               => 'field_5c9e1c1c15df3',
			'label'             => 'Promote on Main Site',
			'name'              => 'post_main_site_story',
			'type'              => 'true_false',
			'instructions'      => 'When checked, the story will appear in the news feed on UCF.edu.',
			'ui'                => 1,
		);

		// Defines Main Site News field group
		$field_group = array(
			'key'                   => 'group_5c9e1c1417520',
			'title'                 => 'Main Site News',
			'fields'                => $fields,
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'post',
					),
					array(
						'param'    => 'current_user_role',
						'operator' => '==',
						'value'    => 'administrator',
					),
				),
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'post',
					),
					array(
						'param'    => 'current_user_role',
						'operator' => '==',
						'value'    => 'super_admin',
					),
				),
			),
			'position'              => 'side',
		);

		acf_add_local_field_group( $field_group );
	}
}

add_action( 'acf/init', 'ucf_today_add_main_site_news_fields' );
