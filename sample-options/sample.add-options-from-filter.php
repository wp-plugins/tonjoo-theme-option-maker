<?php 
/**
 * Sample script to add/change config, setting, and options through filter
 * you can place this script on theme or child theme
 * 
 * @tom_config : filter to add tom configurations
 * @tom_default : filter to add tom default option settings
 * @tom_options : filter to add tom options
 *
 * Please visit plugin website to see how to use
 */


function new_tonjoo_tom_config($config) {

	/*
	 * $config['mode'] = 'lite' or 'full'
	 * lite : User cannot create new options from the wp-admin
	 */
	$config['mode'] 			= 'full';
	$config['page_title'] 		= 'New Title';

	return $config;
}
add_filter( 'tom_config', 'new_tonjoo_tom_config');



function new_tonjoo_tom_default($default) {

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
			'media_buttons' => false, /* Set true to display media button */
			'textarea_rows' => 5,
			'tinymce' => array( 'plugins' => 'wordpress' )
		)
	);

	return $default;
}
add_filter( 'tom_default', 'new_tonjoo_tom_default');


function new_tonjoo_tom_options($options) {

	/*********************
	Tab Sample From Child Theme
	*********************/
	 $options['from-child'] = array(
	 	'name' => 'From Child',
	 	'type' => 'heading',
	 	'desc' => 'Media button can be configure from file');

	 $options['from-child-text-editor'] = array(
	 	'name' => 'Text editor',
	 	'desc' => 'Sample text editor',
	 	'type' => 'editor');

	return $options;
}
add_filter( 'tom_options', 'new_tonjoo_tom_options', 1, 8);