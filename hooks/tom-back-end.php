<?php
/**
 * Init plugin options to white list our options
 */
add_action( 'admin_init', 'tom_settings_init' );

function tom_settings_init() {

	/* Register TOM Settings */
	register_setting( 'tonjoo-tom', 'tom_data', 'tom_validate_options' );
	register_setting( 'tom_options', 'tom_options', 'tom_validate_create_options' );
}

add_action( 'admin_menu', 'tom_admin_page' );

function tom_admin_page() {

		$config = TOMOption::tom_configs();

        add_menu_page(
        	$config['page_title'],
        	$config['menu_title'],
        	$config['capability'],
        	$config['menu_slug'],
        	'tom_options_page',
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
		    	'tom_create_options_page' );
        }
	}


function tom_validate_options( $input ) {
		

	if ( isset( $_POST['reset'] ) ) {
		add_settings_error( 'tonjoo-tom', 'restore_defaults', 'Default options restored.', 'updated fade' );
		return TOMOption::tom_get_default_values();
	}

	foreach ($input as $key => $value) {
		// $value['options'] = array();
		$haveoptions = array();
		if(!empty($value['options']) && is_array($value['options'])) {
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

	return json_encode($input);
}

function tom_validate_create_options( $input ) {

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

	return json_encode($input);
	
}

	/* Options Page */
	function tom_options_page() {
		include TOM_BASE_PATH . "/views/option-page.php";
	}

	function tom_create_options_page() {
		include TOM_BASE_PATH . "/views/create-options-page.php";
	}