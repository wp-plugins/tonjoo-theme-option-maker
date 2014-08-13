<?php
/*
 *	Plugin Name: Theme Options Maker
 *	Plugin URI: https://tonjoo.com/addons/hide-show-comment
 *	Description: Theme options framework and generator for WordPress Theme. Available as a plugin or library
 *	Author:  tonjoo
 *	Version: 1.0.1
 *	Author URI: https://tonjoo.com
 *  Contributor: Todi Adiyatmo Wijoyo, Lafif Astahdziq
 */

function tonjoo_tom_init() {

	/* Load Core Files */
	require plugin_dir_path( __FILE__ ) . 'includes/class.tom-options.php';
	require plugin_dir_path( __FILE__ ) . 'includes/class.tom-generate.php';

	/* If file config exist */
	if ( file_exists( get_template_directory() . "/tonjoo_options.php" ) ) {
	    require_once( get_template_directory() . "/tonjoo_options.php" );
		
		/* Insert value through filter */
		if ( function_exists('tonjoo_tom_config') ) {
			add_filter( 'tom_config', 'tonjoo_tom_config');
		}

		if ( function_exists('tonjoo_tom_default') ) {
			add_filter( 'tom_default', 'tonjoo_tom_default');
		}

		if ( function_exists( 'tonjoo_tom_options' ) ) {
			add_filter( 'tom_options', 'tonjoo_tom_options');
		}
	} 


	// Instantiate plugin core.
	$tom = new tomOptions;
	$tom->init();

}
add_action( 'init', 'tonjoo_tom_init', 20 );


/**************
* SHORTCODE 
**************/
function tom_shortcode( $atts = NULL ) {
    $param = shortcode_atts( array(
        'id' => '',
        'default' => '',
    ), $atts );

   	$id = $param['id'];

    $data = get_option( 'tom_data' );
    $options = tomOptions::tom_options_fields();

	$type = @$options[$id]['type'];
	$val = @$data[$id];
	$default = @$param['default'];

	$type = (!empty($type)) ? $type : '';
	$val = (!empty($val)) ? $val : '';
   	$default = (!empty($default)) ? $default : @$options[$id]['default'];
   	
	/* Switch option type for special handling */
	switch ($type) {
		case 'multicheck':
			$value = serialize($val);
			break;

		case 'upload':
			$image = wp_get_attachment_image_src( $val, 'full' );
			$value = (is_numeric($val)) ? $image[0] : $val;
			break;

		case 'typography':
			$value = serialize($val);
			break;

		default:
			$value =  $val;
			break;
	}
	// print_r($value); exit();

    /* If value empty try to get default value from shortcode */
	$tom_data = (!empty($value)) ? $value : $default;

	/* If SSL Enabled use https replace */
	$tom_data = (is_ssl()) ? tom_https_link($tom_data) : $tom_data;

    return $tom_data;
}

add_shortcode( 'tom', 'tom_shortcode' );

/* Replace url to https */
function tom_https_link($url){
	/* Validate value is URL */
	if(filter_var($url, FILTER_VALIDATE_URL)) {
		// Parse to get domain from url
		$parse_base = parse_url(get_site_url());
		$parse_url = parse_url($url);

		if ($parse_url['host'] == $parse_base['host']) {
			$url = str_replace('http://', 'https://', $url );
		}
	}
	return $url;
}
?>