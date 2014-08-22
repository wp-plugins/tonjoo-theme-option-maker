<?php

function tonjoo_tom_config($config) {

/*
 * $config['mode'] = 'lite' or 'full'
 * lite : User cannot create new options from the wp-admin
 */
#	$config['mode'] 			= 'lite';
#   $config['page_title'] 		= 'Your Theme Option';
#   $config['page_desc'] 		= 'Your custom descriptions';
#	$config['menu_title'] 		= 'Your Custom Title';
#	$config['capability'] 		= 'edit_theme_options';
#	$config['menu_slug'] 		= '';
#   $config['icon_url']			= 'dashicons-editor-paste-text';
#   $config['position'] 		= '61';
#   $config['sub_page_title'] 	= 'Your Sub Page Title';
#   $config['sub_page_desc'] 	= 'Your custom descriptions';
#	$config['sub_menu_title'] 	= 'Your Sub Page Title';
#	$config['sub_capability'] 	= '';
#	$config['sub_menu_slug'] 	= '';
#	$config['ads_enabled']	= true;
#	$config['ads_title']	= 'Your Ads Header Title';
#	$config['ads_endpoint']	= 'http://your_endpoint_ads.com/ads/?type=json';

	return $config;
}

function tonjoo_tom_default($default) {

	$default = array(
		'font-size' => range( 9, 71 ),
		'font-face' => array(
			'arial'     => 'Arial',
			'verdana'   => 'Verdana, Geneva',
		),
		'font-style' => array(
			'normal'      => 'Normal',
			'italic'      => 'Italic',
			'bold'        => 'Bold',
			'bold italic' => 'Bold Italic',
		),
		/*********************************************************
		* @ http://codex.wordpress.org/Function_Reference/wp_editor
		**********************************************************/
		'editor-settings' => array(
			'media_buttons' => true, /* Set true to display media button */
			'textarea_rows' => 5,
			'tinymce' => array( 'plugins' => 'wordpress' )
		)
	);

	return $default;
}

function tonjoo_tom_options() {

	/* Sample Array for options */
	$sample_array = array(
		'satu'	=> 'Satu',
		'dua' 	=> 'Dua',
		'tiga' 	=> 'Tiga',
		'empat' => 'Empat',
		'lima' 	=> 'Lima',
	);

	/* Sample default value for typography */
	$typography_defaults = array(
	 	'size' => '15px',
	 	'face' => 'georgia',
	 	'style' => 'bold',
	 	'color' => '#bada55' );

	
	$options_categories = array();
	$options_categories_obj = get_categories();
	foreach ($options_categories_obj as $category) {
		$options_categories[$category->cat_ID] = $category->cat_name;
	}

	$options_tags = array();
	$options_tags_obj = get_tags();
	foreach ( $options_tags_obj as $tag ) {
		$options_tags[$tag->term_id] = $tag->name;
	}

	$options_pages = array();
	$options_pages_obj = get_pages('sort_column=post_parent,menu_order');
	$options_pages[''] = 'Select a page:';
	foreach ($options_pages_obj as $page) {
		$options_pages[$page->ID] = $page->post_title;
	}

	$imagepath =  get_template_directory_uri() . '/images/';

	$options = array();

	/**********
	Tab Homepage
	***********/
	$options['homepage'] = array(
		'name' => 'Homepage',
		'type' => 'heading',
		'desc' => 'Test description homepage'); 
	
	$options['sample-text'] = array(
		'name' => 'Input Text',
		'required' => '1', // 1 to set required
		'desc' => 'Sample input text',
		'type' => 'text',
		'default' => 'Sample default value for text');

	$options['sample-textarea'] = array(
		'name' => 'Textarea',
		'required' => '1', // 1 to set required
		'desc' => 'Sample textarea',
		'type' => 'textarea',
		'default' => 'Sample default value for textarea');
	
	$options['sample-select'] = array(
		'name' => 'Select',
		'desc' => 'Sample Select',
		'type' => 'select',
		'options' => $sample_array,
		'default' => 'dua');

	$options['sample-select-page'] = array(
		'name' => 'Select Page',
		'desc' => 'Sample select page',
		'type' => 'select',
		'options' => $options_pages);

	$options['sample-select-cat'] = array(
		'name' => 'Select Category',
		'desc' => 'Sample select category',
		'type' => 'select',
		'options' => $options_categories);

	if ( $options_tags ) {
	$options['sample-select-tag'] = array(
		'name' => 'Select Tag',
		'desc' => 'Sample select tag',
		'type' => 'select',
		'options' => $options_tags);
	}

	$options['sample-radio'] = array(
		'name' => 'Input Radio',
		'desc' => 'Sample input radio',
		'type' => 'radio',
		'options' => $sample_array,
		'default' => 'dua' );

	$options['sample-checkbox'] = array(
		'name' => 'Input Checkbox',
		'desc' => 'Sample input checkbox',
		'type' => 'checkbox',
		'default' => '1' ); /* 1 = Checked */


	/**********
	Tab About
	***********/
	 $options['about'] = array(
	 	'name' => 'About',
	 	'type' => 'heading',
	 	// if desc not set, the name of heading will be use
	 	);

	 $options['sample-upload'] = array(
	 	'name' => 'Image',
	 	'desc' => 'Sample image upload',
	 	'type' => 'upload');

	 $options['sample-select-image'] = array(
	 	'name' => "Example Image Select",
	 	'desc' => "Images for layout.",
	 	'type' => "select-image",
	 	'options' => array(
	 		'1' => $imagepath . '1col.png',
	 		'2' => $imagepath . '2cl.png',
	 		'3' => $imagepath . '2cr.png'
	 		),
	 	'default' => "2"
	 );

	 $options['sample-multicheck'] = array(
	 	'name' => 'Multicheck',
	 	'desc' => 'Sample multicheck',
	 	'type' => 'multicheck',
	 	'options' => $sample_array,
	 	'default' => '');

	 $options['sample-color'] = array(
	 	'name' => 'Color picker',
	 	'desc' => 'Sample color picker',
	 	'type' => 'color',
	 	'default' => '' );

	/*********************
	Tab Sample Text Editor
	*********************/
	 $options['sample-text-tab'] = array(
	 	'name' => 'Sample Text Editor',
	 	'type' => 'heading',
	 	'desc' => 'Media button can be configure from file');

	 $options['sample-text-editor'] = array(
	 	'name' => 'Text editor',
	 	'desc' => 'Sample text editor',
	 	'type' => 'editor');

	 $options['sample-typography'] = array( 
	 	'name' => 'Typography',
	 	'desc' => 'Sample typography',
	 	'type' => 'typography',
	 	'default' => $typography_defaults );

	return $options;
}