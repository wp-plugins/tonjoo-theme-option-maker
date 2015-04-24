<?php 

namespace Tonjoo\TOM;

use Tonjoo\TOM\Facade\TOMOptionFacade as TOMOptionFacade;

class TOMShortcode
{	

	function __construct() {
		add_shortcode( 'tom', array($this, 'tom_shortcode') );
	}

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

	     $data  = is_string($data) && is_object(json_decode($data )) ? json_decode($data,true ) : $data ;

	    $options = TOMOptionFacade::tom_options_fields();

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
				// $image = wp_get_attachment_image_src( $val, 'full' );
				$image = wp_get_attachment_url( $val );
				$value = ($image != false) ? $image : '';
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
}
?>