<?php

class tomOptions {

	public function init() {

		$this->tom_options_fields();
		add_action( 'admin_init', array( $this, 'tom_settings_init' ) );

		$page = (isset($_GET['page'])) ? $_GET['page'] : ''; 
		$config = $this->tom_configs();

		if ($page == $config['menu_slug'] || $page == $config['sub_menu_slug']) {
			/* Load Styles and Scripts */
			add_action( 'admin_enqueue_scripts', array( $this, 'tom_enqueue_admin_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'tom_enqueue_admin_scripts' ) );
		}

		add_action( 'admin_menu', array( $this, 'tom_admin_page' ) );

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

	function tom_settings_init() {

		/* Register TOM Settings */
		register_setting( 'tonjoo-tom', 'tom_data', array ( $this, 'tom_validate_options' ) );
		register_setting( 'tom_options', 'tom_options', array ( $this, 'tom_validate_create_options' ) );
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


	static function tom_options_fields() {
		$options = get_option( 'tom_options' );
		if ( !empty( $options )) {
			$options_from_db = $options;
		} else {
			$options_from_db = array();
		}
		/* Get options from filter */
		$options_from_file = apply_filters( 'tom_options', $options_from_db );
		/* Merge filter with options from database */
		$options = array_merge($options_from_db, $options_from_file);

		return $options;
	}

	
	static function tom_configs() {

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

	static function tom_default_options() {

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

	function tom_admin_page() {

		$config = $this->tom_configs();

        add_menu_page(
        	$config['page_title'],
        	$config['menu_title'],
        	$config['capability'],
        	$config['menu_slug'],
        	array( $this, 'tom_options_page' ),
        	$config['icon_url'],
        	$config['position']
        );

        if ($config['mode'] == 'full') {
        	add_submenu_page(
		    	// $config['parent_slug'],
		    	$config['menu_slug'],
		    	$config['sub_page_title'],
		    	$config['sub_menu_title'],
		    	$config['sub_capability'],
		    	$config['sub_menu_slug'],
		    	array( $this, 'tom_create_options_page' ) );
        }
	}

	/* Options Page */
	function tom_options_page() { ?>

		<div  class="wrap">
			<?php $config = $this->tom_configs(); ?>
			<h2><?php echo esc_html( $config['page_title'] ); ?></h2>
			<p><?php echo esc_html( $config['page_desc'] ); ?></p>
			<p><?php echo $config['page_manual']; ?></p>
			<?php 
			$updateNotice = '<div id="setting-error-save_options" class="updated fade settings-error below-h2"> 
				<p><strong>Options saved.</strong></p></div>';
			?>
		    <p id="tom-notification"><?php echo (isset($_GET['settings-updated'])) ? $updateNotice : ''; ?></p>

		    <h2 class="nav-tab-wrapper">
		        <?php echo tomGenerate::tom_tabs(); ?>
		    </h2>

		    <div id="tom-options-panel" class="metabox-holder metabox-main metabox-options">
			    <div id="tonjoo-tom" class="postbox">
					<form action="options.php" method="post">
					<?php settings_fields( 'tonjoo-tom' ); ?>
					<?php tomGenerate::tom_generate_options_fields(); /* Settings */ ?>
					</form>
				</div> <!-- / #container -->
			</div>
			<?php if ($config['ads_enabled'] == true) { ?>
			<!-- ADS -->
			<div id="tom-adds-panel" class="metabox-holder metabox-side">
			  <div class="form-wrap postbox">
			    <h3>
			      <?php echo (!empty($config['ads_title'])) ? esc_html( $config['ads_title'] ) : '&nbsp;'; ?>
			    </h3>
			 	<div style="text-align: center; padding: 20px;">
			 		<div id="promo_1" class="tom_banner">
			 			<a href="" target="_blank"><img src=""></a>
			 		</div>
			 		<div id="promo_2" class="tom_banner">
			 			<a href="" target="_blank"><img src=""></a>
			 		</div>
			 	</div>
			  </div>
			</div>
			<?php } ?>
		</div> 

	<?php
	}

	function tom_create_options_page() { ?>

	<div class="wrap">
		<?php $config = $this->tom_configs(); ?>
		<h2><?php echo esc_html( $config['sub_page_title'] ); ?></h2>
		<p><?php echo esc_html( $config['sub_page_desc'] ); ?></p>
		<p><?php echo $config['sub_page_manual']; ?></p>
		<?php 
		$updateNotice = '<div id="setting-error-save_options" class="updated fade settings-error below-h2"> 
			<p><strong>Options saved.</strong></p></div>';
		?>
	    <p id="tom-notification"><?php echo (isset($_GET['settings-updated'])) ? $updateNotice : ''; ?></p>
	    <h2 class="nav-tab-wrapper">
	        <?php echo tomGenerate::create_tom_tabs(); ?>
	    </h2>

	    <div id="tom-create-options-panel" class="metabox-holder metabox-main">
		    <div id="tonjoo-tom" class="postbox">
				<form id="f_create-options" action="options.php" method="post">
				<?php settings_fields( 'tom_options' ); ?>
				<?php tomGenerate::tom_generate_create_options_fields(); /* Settings */ ?>
				</form>
			</div> <!-- / #container -->
		</div>
		<div id="tom-add-options-panel" class="metabox-holder metabox-side">
		  	<div class="form-wrap postbox">
			    <h3>
			      Add New Option
			    </h3>
			    <div id="add-tom-options">
			    	<label for="tom-id-new-data">
			          Option ID :
			        </label>
			        <div class="input">
				        <input id="tom-id-new-data" type="text" value="" class="input-width">
				        <p>
				          Option ID (Use for Shortcode).
				        </p>
			        </div>
			      	<label for="tom-name-new-data">
			          Name :
			        </label>
			        <div class="input">
			        <input name="name" id="tom-name-new-data" type="text" value="" class="input-width">
			        <div class="input">
				        <p>
				          The name of option.
				        </p>
			        </div>
			      	<label for="tom-required-new-data">
			          Required :
			        </label>
			        <div class="input">
						<div id="new-data-required">
							<div class="required-container">
								<input id="tom-required-new-data" class="input-required" type="checkbox" name="tom_options[new-data][required]" value="1">
								<span class="status">( Not Required )</span>
							</div>
						</div>
				        <p>
				          Is required ?
				        </p>
			        </div>

			        <label for="tom-desc-new-data">
			          Desription :
			        </label>
			        <div class="input">
				        <textarea name="desc" id="tom-desc-new-data" class="input-width"></textarea>
				        <p>
				          Short description of option.
				        </p>
			        </div>
			        <label for="tom-type">
			          Type :
			        </label>
			        <div class="input">
			        <?php $config = $this->tom_configs(); ?>
				        <select name="type" id="tom-type-new-data" id="tom-type-new-data" class="tom-type" data-container="new-data">
			        	<?php  
			        		foreach ($config['type-options'] as $value => $name) {
			        			echo '<option value="'.$value.'">'.$name.'</option>';
			        		}
			        	?>
				        </select>
				        <p>
				          Type of option.
				        </p>
				        <div id="new-data-options" class="options-container" style="display:none;" data-default="new-data">
				        	<div class="tom-label-options">Options : </div>
					        <div id="add-opt-new-data" class="input-options">
						        <div data-order="1" class="input-options-group">
						        	<i class="dashicons dashicons-yes"></i>
						        	<input class="input-opt input-key" name="opt-key" value="" placeholder="Key">
						        	<input class="input-opt input-val" name="opt-val" value="" placeholder="Value">
						        	<a class="btn-remove dashicons dashicons-dismiss"></a>
					        	</div>
					        </div>
					        <p><a id="new-repeatable" href="#">Add New Field</a></p>
				        </div>
			        </div>
			      	<label for="tom-default-new-data">
			          Default :
			        </label>
			        <div class="input">
						<div id="new-data-default">
						<!-- will dynamic generate by jquery  -->
						<input class="input-default" name="default" type="text" id="tom-default-new-data" value="">
				        </div>

				        <?php $defaultOptions = $this->tom_default_options(); ?>
						<div id="typography-options" class="input-default typography-options" style="display:none;">
				        	<label>Color :</label>
							<div class="color-container">
								<input class="array-default color-picker" type="text" data-name="color" />
							</div>
				        	<label>Size :</label>
							<select class="array-default tom-typography tom-typography-size" data-name="size">
							    <?php foreach ($defaultOptions['font-size'] as $key => $value) {
								echo '<option value="'.$value.'px">'.$value.' px</option>';
								} ?>
							</select>
						  	<br>
				        	<label>Font Face :</label>
							<select class="array-default tom-typography tom-typography-face" data-name="face">
								<?php foreach ($defaultOptions['font-face'] as $key => $value) {
								echo '<option value="'.$key.'">'.$value.'</option>';
								} ?>
							</select>
							<br>
				        	<label>Font Style :</label>
							<select class="array-default tom-typography tom-typography-style" data-name="style">
							    <?php foreach ($defaultOptions['font-style'] as $key => $value) {
								echo '<option value="'.$key.'">'.$value.'</option>';
								} ?>
							</select>
						</div>
				        <p>
				          Default value.
				        </p>
			        </div>
			    </div>
		  	</div>
			<div id="tonjoo-tom-submit">
				<a id="tom-add-options" class="button-primary">Add Option</a>
				<span id="loading-new-data" class="tom-loading" style="display:none;"><img src="<?php echo admin_url(); ?>images/spinner.gif" alt=""></span>
				<div class="clear"></div>
			</div>
		</div>
	</div>
	<?php
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

	function tom_validate_options( $input ) {
		

		if ( isset( $_POST['reset'] ) ) {
			add_settings_error( 'tonjoo-tom', 'restore_defaults', 'Default options restored.', 'updated fade' );
			return $this->tom_get_default_values();
		}

		foreach ($input as $key => $value) {
    		$value['options'] = array();
    		$haveoptions = array();
    		if(!empty($value['options'])) {
    			/* combine input value key and input value to one array as key => value */
    			$combine[$key]['options'] = array_combine($value['options']['opt-key'], $value['options']['opt-val']);
    			/* get other field like name, type */
    			$org[$key] = $value;
    			/* Merge options field */
    			$haveoptions[$key] = array_merge($org[$key],$combine[$key]);
    		}
    		$input = array_merge($input,$haveoptions);
    	}
    	/* Merge with main input */
    	$input = array_merge($input,$haveoptions);

		return $input;
	}

	public function tom_validate_create_options( $input ) {

		/* If have value from new group */
	  	if(!empty($input['new-group']['name'])) {
	  		$idFromName = sanitize_title_with_dashes( $input['new-group']['name'] );
	  		
	  		$input[$idFromName]['name'] = $input['new-group']['name'];
	  		$input[$idFromName]['type'] = 'heading';
	  		$input[$idFromName]['desc'] = $input['new-group']['desc'];

	  		unset($input['new-group']);
	  	} else {
	  		unset($input['new-group']);
	  	}
	  	
	  	$value['options'] = array();
		$haveoptions = array();
    	/* Parse input options */
    	foreach ($input as $key => $value) {
    		if(!empty($value['options'])) {
    			/* combine input value key and input value to one array as key => value */
    			$combine[$key]['options'] = array_combine($value['options']['opt-key'], $value['options']['opt-val']);
    			/* get other field like name, type */
    			$org[$key] = $value;
    			/* Merge options field */
    			$haveoptions[$key] = array_merge($org[$key],$combine[$key]);
    		}
    		$input = array_merge($input,$haveoptions);
    	}
    	/* Merge with main input */
    	$input = array_merge($input,$haveoptions);

		return $input;
		
	}
	
}