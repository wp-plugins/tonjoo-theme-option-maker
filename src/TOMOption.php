<?php

namespace Tonjoo\TOM;

class TOMOption
{
	public $options; 

	public function __construct($container)
	{		

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
		
		$this->tom_options_fields();
		// add_action( 'admin_init', array( $this, 'tom_settings_init' ) );

		$page = (isset($_GET['page'])) ? $_GET['page'] : ''; 
		$config = $this->tom_configs();

		if ($page == $config['menu_slug'] || $page == $config['sub_menu_slug']) {
			/* Load Styles and Scripts */
			add_action( 'admin_enqueue_scripts', array( $this, 'tom_enqueue_admin_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'tom_enqueue_admin_scripts' ) );
		}

		// add_action( 'admin_menu', array( $this, 'tom_admin_page' ) );

		/* Ajax */
		add_action( 'wp_ajax_tom_options', array( $this, 'tom_options_callback' ) );
	}

	function tom_options_callback() {
		global $wpdb;
		$optionsId = $_POST['options'];
		$id = $_POST['id'];

		/* parse form data */
		$formData = array();
 		parse_str($_POST['form_data'], $formData);

		update_option( $optionsId, $formData['tom_options'] );
		$data = get_option( 'tom_options' );

		$data = array(
			'data' => $data[$id] , 
			'message' => '<div id="setting-error-save_options" class="updated fade settings-error below-h2"> 
							<p><strong>Options saved.</strong></p></div>'
			);
		echo json_encode($data);
		die();
	}

	function tom_enqueue_admin_styles() {

		wp_enqueue_style( 'tonjoo-tom', plugin_dir_url( dirname(__FILE__) ) . 'assets/css/style.css', array() );
		wp_enqueue_style( 'wp-color-picker' );
	}
	
	function tom_enqueue_admin_scripts() {

			// Enqueue custom option panel JS
			wp_enqueue_script( 'nestable', plugin_dir_url( dirname(__FILE__) ) . 'assets/js/jquery.nestable.js', array('jquery'));
			wp_enqueue_script( 'zclip', plugin_dir_url( dirname(__FILE__) ) . 'assets/js/ZeroClipboard.min.js', array( 'jquery' ) );
			wp_enqueue_script( 'tonjoo-script', plugin_dir_url( dirname(__FILE__) ) . 'assets/js/script.js', array( 'jquery','wp-color-picker' ) );
			
			/* Media Uploader */
			if(function_exists('wp_enqueue_media')) {
	            wp_enqueue_media();
	        } else { /* If user use old wordpress */
	            wp_enqueue_script('media-upload');
	            wp_enqueue_script('thickbox');
	            wp_enqueue_style('thickbox');
	        }
			
			/* Custom variable TTOM */
			$config = $this->tom_configs();
			$dir = plugin_dir_url( dirname(__FILE__) );
			echo '<script type="text/javascript">
					var tomMode = "'.$config['mode'].'",
						tomCreatePage = "' . get_admin_url( null, 'admin.php?page=' . $config['sub_menu_slug'] ) .'",
						pluginDir = "' . $dir .'",
						tomAdsEnabled = "' . $config['ads_enabled'] . '",
						tomAdsEndpoint = "' . $config['ads_endpoint'] . '",
						adminUrl = "' . get_admin_url() . '";
				  </script>';
	}

	function tom_configs() {

		$config_default = array(

			/* Default cofigurations */
            'mode' => 'full',

            'page_title' => 'Tonjoo Theme Options Maker (TTOM)',
            'page_desc' => "Customize your theme options!, you can add, edit, or delete easily here. Don't forget to save your changes or you will lose it",
            'page_manual' => '<a href="https://forum.tonjoo.com/thread-category/tonjoo-tom/" target="_blank">Support Forum</a> |<a href="https://tonjoo.com/addons/tonjoo-tom/#manual" target="_blank">Read documentations</a> |<a href="http://wordpress.org/support/view/plugin-reviews/tonjoo-theme-options-maker?rate=5#postform" target="_blank" style="margin-left:10px;">Enjoy with the plugin?, rate us!</a>',
			'menu_title' => 'TTOM Options',
			'capability' => 'edit_theme_options',
			'menu_slug' => 'tonjoo-tom',
            'icon_url' => 'dashicons-editor-paste-text',
            'position' => '61',

            /* for sub menu */
            'sub_page_title' => 'Tonjoo Theme Options Maker (TTOM) Settings',
            'sub_page_desc' => "Customize your theme options!, you can add, edit, or delete easily here. Don't forget to save your changes or you will lose it",
            'sub_page_manual' => '<a href="https://forum.tonjoo.com/thread-category/tonjoo-tom/" target="_blank">Support Forum</a> <a href="https://tonjoo.com/addons/tonjoo-tom/#manual" target="_blank">Read documentations</a> |<a href="http://wordpress.org/support/view/plugin-reviews/tonjoo-theme-options-maker?rate=5#postform" target="_blank" style="margin-left:10px;">Enjoy with the plugin?, rate us!</a>',
			'sub_menu_title' => 'Create Options',
			'sub_capability' => 'manage_options',
			'sub_menu_slug' => 'create-options',
			'type-options' => array(
								'text' => 'Text',
								'url' => 'URL',
								'number' => 'Number',
								'textarea' => 'Textarea',
								'select' => 'Select',
								'radio' => 'Radio',
								'checkbox' => 'Checkbox',
								'multicheck' => 'Multicheck',
								'upload' => 'Image Upload',
								'select-image' => 'Image Select',
								'color' => 'Color Picker',
								'editor' => 'Text Editor',
								'typography' => 'Typography'
								),
			'ads_enabled' => false,
			'ads_title' => '',
			'ads_endpoint' => '',
		);

		/* Get configurations from file if exist */
		$config_from_file = apply_filters( 'tom_config', $config_default );
		$configs = array_merge($config_default, $config_from_file);
		
		return $configs;
	}

	function tom_default_options() {

		$opt_default = array(
			'font-size' => range( 9, 71 ),
			'font-face' => array(
				'arial'     => 'Arial',
				'verdana'   => 'Verdana, Geneva',
				'trebuchet' => 'Trebuchet',
				'georgia'   => 'Georgia',
				'times'     => 'Times New Roman',
				'tahoma'    => 'Tahoma, Geneva',
				'palatino'  => 'Palatino',
				'helvetica' => 'Helvetica'
			),
			'font-style' => array(
				'normal'      => 'Normal',
				'italic'      => 'Italic',
				'bold'        => 'Bold',
				'bold italic' => 'Bold Italic',
			),
			'editor-settings' => array(
				'media_buttons' => true,
				'textarea_rows' => 5,
				'tinymce' => array( 'plugins' => 'wordpress' )
			)
		);

		/* Get default options from file if exist */
		$opt_from_file = apply_filters( 'tom_default', $opt_default );
		$default = array_merge($opt_default, $opt_from_file);

		return $default;
	}


	function tom_options_fields() {
		$options = get_option( 'tom_options' );

		$options  = is_string($options) && is_object(json_decode($options )) ? json_decode($options,true ) : $options ;
		
		if ( !empty( $options )) {
			$options_from_db = $options;
		} else {
			$options_from_db = array();
		}
		/* Get options from filter */
		$options_from_file = apply_filters( 'tom_options', $options_from_db );

		// $xx = apply_filters( 'tom_options','' );
		// echo "<pre>";
		// print_r($xx);
		// exit();
		
		/* Merge filter with options from database */
		$options = array_merge($options_from_db, $options_from_file);

		return $options;
	}

	function tom_get_default_values() {
		$output = array();
		$config = $this->tom_options_fields();
		foreach ( (array) $config as $option ) {
			if ( ! isset( $option['id'] ) ) {
				continue;
			}
			if ( ! isset( $option['default'] ) ) {
				continue;
			}
			if ( ! isset( $option['type'] ) ) {
				continue;
			}
		}
		return $output;
	}

}