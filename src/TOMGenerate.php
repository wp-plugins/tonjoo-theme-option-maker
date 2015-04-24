<?php

namespace Tonjoo\TOM;

use Tonjoo\TOM\Facade\TOMOptionFacade as TOMOptionFacade;

class TOMGenerate
{
	private $options; 
	private $app;

	public function __construct($container,$tom) {
		$this->app = $container;
		
		$this->options = $tom->tom_options_fields();
	}

	function tom_tabs() {
		$counter = 0;
		$options = $this->options;
		$menu = '';

		foreach ( $options as $obj_key =>$key ) {
			// Heading for Navigation
			if ( $key['type'] == "heading" ) {
				$counter++;
				$class = '';
				$class = ! empty( $obj_key ) ? $obj_key : $key['name'];
				$class = sanitize_title_with_dashes( $class ) . '-tab';
				$menu .= '<a id="options-group-'.  $counter . '-tab" class="nav-tab ' . $class .'" title="' . esc_attr( $key['name'] ) . '" href="' . esc_attr( '#options-group-'.  $counter ) . '">' . esc_html( $key['name'] ) . '</a>';
			}
		}

		return $menu;
	}

	/**
	 * Generates the options fields that are used in the form.
	 */
	function tom_generate_options_fields() {

		$option_name = 'tom_data';

		$settings = get_option($option_name);

		$settings  = is_string($settings) && is_object(json_decode($settings )) ? json_decode($settings,true ) : $settings ;
		
		$options = $this->options;
		$counter = 0;
		$menu = '';

		if(!empty($options)) {
			foreach ( $options as $obj_key =>$key ) {
				$name = ! empty( $key['name'] ) ? $key['name'] : '';
				$required  = (! empty( $key['required'] ) && @$key['required'] == '1' ) ? ' required' : '';
				$desc = ! empty( $key['desc'] ) ? $key['desc'] : '';
				$type = ! empty( $key['type'] ) ? $key['type'] : '';
				$options = ! empty( $key['options'] ) ? $key['options'] : array();
				/* Default value from file */
				$val = ! empty( $key['default'] ) ? $key['default'] : '';

				/* change $val default with default value from db if exist */
				if ( $key['type'] != 'heading' ) {
					if ( isset( $settings[($obj_key)]) ) {
						$val = $settings[($obj_key)];
						/* Striping slashes of non-array options */
						if ( !is_array($val) ) {
							$val = stripslashes( $val );
						}
					}
				}

				$short_default = !empty($key['default']) && !is_array($key['default']) ? ' default="'.$key['default'].'"' : '';
				$shortcode = '[tom id="'.$obj_key.'"]';

				$output = '';

				/* Parse Type to display options */
				switch ( $key['type'] ) {

				/* Create div container */
				case "heading":
					$counter++;
					if ( $counter >= 2 ) {
						$output .= '</tbody>'."\n";
						$output .= '</table>'."\n";
						$output .= '</div>'."\n";
						$output .= '</div>'."\n";
					}
					$class = '';
					$class = ! empty( $obj_key ) ? $obj_key : $key['name'];
					$class = sanitize_title_with_dashes( $class );
					$desc  = (!empty($key['desc'])) ? $key['desc'] : $key['name'];
					$output .= '<div id="options-group-' . $counter . '" class="group ' . $class . '">';
					$output .= '<h3>' . esc_html( $desc ) . '</h3>' . "\n";
					$output .= '<div class="container-table">' . "\n";
					$output .= '<table class="tom-options widefat">' . "\n";
					$output .= '<tbody class="container-body">' . "\n";
					break;

				case 'textarea':
					$output .= '<tr class="alternate tom-item">' . "\n";
					$output .= '<th scope="row"><label for="' . esc_attr( $obj_key ) . '">' . esc_attr( $name ) . '</label><br><span class="description">' . esc_attr( $desc ) . '</span></th>' . "\n";
					$output .= '<td><textarea class="tom-input" id="' . esc_attr( $obj_key ) . '" name="' . esc_attr( $option_name . '[' . $obj_key . ']' ) . '" placeholder="' . esc_attr( $val ) . '" rows="4" cols="50" ' . $required . '>' . esc_attr( $val ) . '</textarea></td>' . "\n";
					$output .= '<td class="shortcode">
									<span><a class="button-copy-shortcode" title="Copy Shortcode" href="javascript:;"><i class="dashicons dashicons-nametag"></i><span class="tooltipValue" data-title="'. esc_attr( $name ) .'" style="display:none;">'.$shortcode.'</span></a></span>
								</td>' . "\n";
					$output .= '</tr>' . "\n";
					break;

				case 'select':
					$output .= '<tr class="alternate tom-item">' . "\n";
					$output .= '<th scope="row"><label for="' . esc_attr( $obj_key ) . '">' . esc_attr( $name ) . '</label><br><span class="description">' . esc_attr( $desc ) . '</span></th>' . "\n";
					$output .= '<td><select class="tom-input" name="' . esc_attr( $option_name . '[' . $obj_key . ']' ) . '" id="' . esc_attr( $obj_key ) . '" ' . $required . '>' . "\n";
								foreach ($options as $key => $option ) {
									/* function selected dr wp @http://codex.wordpress.org/Function_Reference/selected */
									$output .= '<option '. selected( $val, $key, false ) .' value="' . esc_attr( $key ) . '">' . esc_html( $option ) . '</option>';
								}	
					$output .= '</td>' . "\n";
					$output .= '<td class="shortcode">
									<span><a class="button-copy-shortcode" title="Copy Shortcode" href="javascript:;"><i class="dashicons dashicons-nametag"></i><span class="tooltipValue" data-title="'. esc_attr( $name ) .'" style="display:none;">'.$shortcode.'</span></a></span>
								</td>' . "\n";
					$output .= '</tr>' . "\n";
					break;

				case "radio":
					$output .= '<tr class="alternate tom-item">' . "\n";
					$output .= '<th scope="row"><label for="' . esc_attr( $obj_key ) . '">' . esc_attr( $name ) . '</label><br><span class="description">' . esc_attr( $desc ) . '</span></th>' . "\n";
					$output .= '<td>' . "\n";
								foreach ($options as $key => $option ) {
									/* function selected dr wp @http://codex.wordpress.org/Function_Reference/checked */
									$output .= '<input type="' . esc_attr( $type ) . '" class="tom-input" name="' . esc_attr( $option_name . '[' . $obj_key . ']' ) . '" value="'. esc_attr( $key ) . '" '. checked( $val, $key, false) .' ' . $required . '>' . esc_attr( $option ) . "\n";
								}	
					$output .= '</td>' . "\n";
					$output .= '<td class="shortcode">
									<span><a class="button-copy-shortcode" title="Copy Shortcode" href="javascript:;"><i class="dashicons dashicons-nametag"></i><span class="tooltipValue" data-title="'. esc_attr( $name ) .'" style="display:none;">'.$shortcode.'</span></a></span>
								</td>' . "\n";
					$output .= '</tr>' . "\n";
					break;

				case "checkbox":
					$output .= '<tr class="alternate tom-item">' . "\n";
					$output .= '<th scope="row"><label for="' . esc_attr( $obj_key ) . '">' . esc_attr( $name ) . '</label><br><span class="description">' . esc_attr( $desc ) . '</span></th>' . "\n";
					$output .= '<td><input id="' . esc_attr( $obj_key ) . '" class="tom-input" type="' . esc_attr( $type ) . '" name="' . esc_attr( $option_name . '[' . $obj_key . ']' ) . '" value="1" '. checked( $val, '1', false) .' ' . $required . '/></td>' . "\n";
					$output .= '<td class="shortcode">
									<span><a class="button-copy-shortcode" title="Copy Shortcode" href="javascript:;"><i class="dashicons dashicons-nametag"></i><span class="tooltipValue" data-title="'. esc_attr( $name ) .'" style="display:none;">'.$shortcode.'</span></a></span>
								</td>' . "\n";
					$output .= '</tr>' . "\n";
					break;

				case "upload":
					if (!empty($val)) {
						$image 	 = wp_get_attachment_image_src( $val, 'medium' ); 
						$src 	 = (is_numeric($val)) ? $image[0] : $val;
						$display = '';
					} else {
						$src 	 = '';
						$display = 'style="display:none;"';
					}

					$output .= '<tr class="alternate tom-item">' . "\n";
					$output .= '<th scope="row"><label for="' . esc_attr( $obj_key ) . '">' . esc_attr( $name ) . '</label><br><span class="description">' . esc_attr( $desc ) . '</span></th>' . "\n";
					$output .= '<td>
								<div id="' . esc_attr( $obj_key ) . '" class="tom_media_upload">
									<img class="tom_media_image tom-option-image" src="'.$src.'" '. $display .'/>
									<div>
										<input class="tom_media_url" type="hidden" value="">
										<input class="tom_media_id" type="hidden" name="' . esc_attr( $option_name . '[' . $obj_key . ']' ) . '" value="' . esc_attr( $val ) . '" ' . $required . '>
										<a href="#" class="tom_button_upload button-secondary">Choose</a>
										<a href="#" class="tom_remove_image button-primary" ' . $display . '>Remove</a>
									</div>
								</div>';
								
					$output .= '</td>' . "\n";
					$output .= '<td class="shortcode">
									<span><a class="button-copy-shortcode" title="Copy Shortcode" href="javascript:;"><i class="dashicons dashicons-nametag"></i><span class="tooltipValue" data-title="'. esc_attr( $name ) .'" style="display:none;">'.$shortcode.'</span></a></span>
								</td>' . "\n";
					$output .= '</tr>' . "\n";

					break;

				case "select-image":
					$output .= '<tr class="alternate tom-item">' . "\n";
					$output .= '<th scope="row"><label for="' . esc_attr( $obj_key ) . '">' . esc_attr( $name ) . '</label><br><span class="description">' . esc_attr( $desc ) . '</span></th>' . "\n";
					$output .= '<td><div class="controls">' . "\n";
								foreach ( $options as $key => $option ) {
									$selected = '';
									if ( $val != '' && ($val == $key) ) {
										$selected = ' tom-radio-img-selected';
									}
									$output .= '<input type="radio" id="' . esc_attr( $obj_key .'_'. $key) . '" class="tom-radio-img-radio" value="' . esc_attr( $key ) . '" name="' . esc_attr( $option_name . '[' . $obj_key . ']' ) . '" '. checked( $val, $key, false ) .' ' . $required . '/>';
									$output .= '<div class="tom-radio-img-label">' . esc_html( $key ) . '</div>';
									$output .= '<img src="' . esc_url( $option ) . '" alt="' . $option .'" class="tom-radio-img-img' . $selected .'" onclick="document.getElementById(\''. esc_attr($obj_key .'_'. $key) .'\').checked=true;" />';
								}
					$output .= '</div></td>' . "\n";
					$output .= '<td class="shortcode">
									<span><a class="button-copy-shortcode" title="Copy Shortcode" href="javascript:;"><i class="dashicons dashicons-nametag"></i><span class="tooltipValue" data-title="'. esc_attr( $name ) .'" style="display:none;">'.$shortcode.'</span></a></span>
								</td>' . "\n";
					$output .= '</tr>' . "\n";
					break;

				case "multicheck":
					$output .= '<tr class="alternate tom-item">' . "\n";
					$output .= '<th scope="row"><label for="' . esc_attr( $obj_key ) . '">' . esc_attr( $name ) . '</label><br><span class="description">' . esc_attr( $desc ) . '</span></th>' . "\n";
					$output .= '<td>' . "\n";
								foreach ($options as $key => $option) {
									$checked = '';
									$label = $option;
									$option = sanitize_title_with_dashes( $key );

									$id = $option_name . '-' . $obj_key . '-'. $option;
									$name = $option_name . '[' . $obj_key . '][' . $option .']';

									if ( isset($val[$option]) ) {
										$checked = checked($val[$option], 1, false);
									}

									$output .= '<input id="' . esc_attr( $id ) . '" class="tom-input" type="checkbox" name="' . esc_attr( $name ) . '" value="1" ' . $checked . ' ' . $required . '/>' . esc_html( $label ) . '<br>' . "\n";
								}
					$output .= '</td>' . "\n";
					$output .= '<td class="shortcode">
									<span><a class="button-copy-shortcode" title="Copy Shortcode" href="javascript:;"><i class="dashicons dashicons-nametag"></i><span class="tooltipValue" data-title="'. esc_attr( $name ) .'" style="display:none;">'.$shortcode.'</span></a></span>
								</td>' . "\n";
					$output .= '</tr>' . "\n";
					break;

				case "color":
					$default_color = '';
					$output .= '<tr class="alternate tom-item">' . "\n";
					$output .= '<th scope="row"><label for="' . esc_attr( $obj_key ) . '">' . esc_attr( $name ) . '</label><br><span class="description">' . esc_attr( $desc ) . '</span></th>' . "\n";
					$output .= '<td>' . "\n";
								if ( isset($key['default']) ) {
									if ( $val !=  $key['default'] )
										$default_color = ' data-default-color="' .$key['default'] . '" ';
								}
					$output .= '<input name="' . esc_attr( $option_name . '[' . $obj_key . ']' ) . '" id="' . esc_attr( $obj_key ) . '" class="tom-color"  type="text" value="' . esc_attr( $val ) . '"' . $default_color .' ' . $required . '/>';
					$output .= '</td>' . "\n";
					$output .= '<td class="shortcode">
									<span><a class="button-copy-shortcode" title="Copy Shortcode" href="javascript:;"><i class="dashicons dashicons-nametag"></i><span class="tooltipValue" data-title="'. esc_attr( $name ) .'" style="display:none;">'.$shortcode.'</span></a></span>
								</td>' . "\n";
					$output .= '</tr>' . "\n";
					break;

				case 'editor':
					$output .= '<tr class="alternate tom-item">' . "\n";
					$output .= '<th scope="row"><label for="' . esc_attr( $obj_key ) . '">' . esc_attr( $name ) . '</label><br><span class="description">' . esc_attr( $desc ) . '</span></th>' . "\n";
					$output .= '<td>' . "\n";
								echo $output;
								$textarea_name = esc_attr( $option_name . '[' . $obj_key . ']' );
								$defaultOptions = TOMOptionFacade::tom_default_options();
								$editor_settings = $defaultOptions['editor-settings'];
								$editor_settings['textarea_name'] = $textarea_name;
								wp_editor( $val, $obj_key, $editor_settings );
								$output = '';
					$output .= '</td>' . "\n";
					$output .= '<td class="shortcode">
									<span><a class="button-copy-shortcode" title="Copy Shortcode" href="javascript:;"><i class="dashicons dashicons-nametag"></i><span class="tooltipValue" data-title="'. esc_attr( $name ) .'" style="display:none;">'.$shortcode.'</span></a></span>
								</td>' . "\n";
					$output .= '</tr>' . "\n";
					break;

				case 'typography':
					$defaultOptions = TOMOptionFacade::tom_default_options();

					$output .= '<tr class="alternate tom-item">' . "\n";
					$output .= '<th scope="row"><label for="' . esc_attr( $obj_key ) . '">' . esc_attr( $name ) . '</label><br><span class="description">' . esc_attr( $desc ) . '</span></th>' . "\n";
					$output .= '<td>' . "\n";
					$output .= '<select class="tom-typography tom-typography-size" name="' . esc_attr( $option_name . '[' . $obj_key . '][size]' ) . '" id="sample-typography_size">';
								    foreach ($defaultOptions['font-size'] as $key => $value) {
									$output .= '<option value="'.$value.'px" '. selected( $val['size'], $value.'px', false ) .'>'.$value.' px</option>';
									}
					$output .= '</select>
							  	<select class="tom-typography tom-typography-face" name="' . esc_attr( $option_name . '[' . $obj_key . '][face]' ) . '" id="sample-typography_face">';
								    foreach ($defaultOptions['font-face'] as $key => $value) {
									$output .= '<option value="'.$key.'" '. selected( $val['face'], $key, false ) .'>'.$value.'</option>';
									}
					$output .= '</select>
							  	<select class="tom-typography tom-typography-style" name="' . esc_attr( $option_name . '[' . $obj_key . '][style]' ) . '" id="sample-typography_style">';
								    foreach ($defaultOptions['font-style'] as $key => $value) {
									$output .= '<option value="'.$key.'" '. selected( $val['style'], $key, false ) .'>'.$value.'</option>';
									}
					$output .= '</select>
							  	<input name="' . esc_attr( $option_name . '[' . $obj_key . '][color]' ) . '" id="sample-typography_color" class="tom-color tom-typography-color  type="text" value="' . esc_attr( $val['color'] ) . '"/>';		
					$output .= '</td>' . "\n";
					$output .= '<td class="shortcode">
									<span><a class="button-copy-shortcode" title="Copy Shortcode" href="javascript:;"><i class="dashicons dashicons-nametag"></i><span class="tooltipValue" data-title="'. esc_attr( $name ) .'" style="display:none;">'.$shortcode.'</span></a></span>
								</td>' . "\n";
					$output .= '</tr>' . "\n";

					break;

				/* Default */
				default:
					$output .= '<tr class="alternate tom-item">' . "\n";
					$output .= '<th scope="row"><label for="' . esc_attr( $obj_key ) . '">' . esc_attr( $name ) . '</label><br><span class="description">' . esc_attr( $desc ) . '</span></th>' . "\n";
					$output .= '<td><input class="tom-input" type="' . esc_attr( $type ) . '" id="' . esc_attr( $obj_key ) . '" name="' . esc_attr( $option_name . '[' . $obj_key . ']' ) . '" placeholder="' . esc_attr( $val ) . '" value="' . esc_attr( $val ) . '" ' . $required . '></td>' . "\n";
					$output .= '<td class="shortcode">
									<span><a class="button-copy-shortcode" title="Copy Shortcode" href="javascript:;"><i class="dashicons dashicons-nametag"></i><span class="tooltipValue" data-title="'. esc_attr( $name ) .'" style="display:none;">'.$shortcode.'</span></a></span>
								</td>' . "\n";
					$output .= '</tr>' . "\n";
					// $output .= '<input id="' . esc_attr( $obj_key ) . '" class="tom-input" name="' . esc_attr( $option_name . '[' . $obj_key . ']' ) . '" type="text" value="' . esc_attr( $val ) . '" />';
					break;
				}
				
				echo $output;
			}
			/* Close last table */
			echo '</tbody></table></div>';

			if ( tomGenerate::tom_tabs() != '' ) {
				echo '</div>';
			}

			/* Submit Button */
			$submit =  '<div id="tonjoo-tom-submit" class="hide-if-empty">
							<input type="submit" class="button-primary" name="update" value="Save" />
							<input type="submit" class="reset-button button-secondary" name="reset" value="Reset" onclick="return confirm( \'Are you sure to reset. Any theme settings will be lost!\');" />
							<div class="clear"></div>
						</div>';


		} else { 

		/* Handle if empty options  */
		$config = TOMOptionFacade::tom_configs();
		$output =  '<div id="empty-group" class="group empty-group">
					  <h3>
					    Oops! your options look empty..
					  </h3>
					  <div class="container-table">
					  	<div class="empty-options">
							<h1>Oops! your options look empty..</h1>
							<h4>Please create options or put options file on theme directory</h4>';
		/* If Create options enabled, display button to create it */
		if ($config['mode'] == 'full') {
		$output .=	  	   '<div class="please-create-options">
					  			<a href="' . get_admin_url( null, 'admin.php?page=' . $config['sub_menu_slug'] ) .'" class="btn">Create Options Now</a>
					  		</div>';
		}
		$output .=	   '</div>
					  </div>
					</div>';
		echo $output;

		/* No Submit Button */
		$submit =  '';

		}

		/* Print Submit Button */
		echo $submit;
	}



	function create_tom_tabs() {
		$counter = 0;
		$options = $this->options;
		$menu = '';
		
		foreach ( $options as $obj_key =>$key ) {
			// Heading for Navigation
			if ( $key['type'] == "heading" ) {
				$counter++;
				$class = '';
				$class = ! empty( $obj_key ) ? $obj_key : $key['name'];
				$class = sanitize_title_with_dashes( $class ) . '-tab';
				$menu .= '<a id="options-group-'.  $counter . '-tab" class="nav-tab ' . $class .'" title="' . esc_attr( $key['name'] ) . '" href="' . esc_attr( '#options-group-'.  $counter ) . '">' . esc_html( $key['name'] ) . '</a>';
			}
		}
		/* Create new group tab */
		$menu .= '<a id="new-group-tab" class="nav-tab" title="Create new group" href="#new-group"><i class="dashicons dashicons-plus-alt"></i></a>';

		return $menu;
	}
	

	function tom_generate_create_options_fields() {

		$option_name = 'tom_options';
		$options = $this->options;
		$config = TOMOptionFacade::tom_configs();

		$counter = 0;
		$initNestable = '';
		if(!empty($options)) {
			foreach ($options as $obj_key =>$key) {
				$name = ! empty( $key['name'] ) ? $key['name'] : '';
				$req = ! empty( $key['required'] ) ? $key['required'] : '';
				$desc = ! empty( $key['desc'] ) ? $key['desc'] : '';
				$type = ! empty( $key['type'] ) ? $key['type'] : '';
				$configs = TOMOptionFacade::tom_configs();
				$types = $configs['type-options'];
				switch ($type) {
					case 'select':
						$show = true;
						break;

					case 'select-image':
						$show = true;
						break;

					case 'radio':
						$show = true;
						break;

					case 'typography':
						$show = true;
						break;
					
					default:
						$show = false;
						break;
				}
				$fieldoptions = ! empty( $key['options'] ) ? $key['options'] : array();
				$val = ! empty( $key['default'] ) ? $key['default'] : '';
				// $display = (!empty($val) && $show == true) ? '' : 'style="display:none;"';
				$display = ( $show == true ) ? '' : 'style="display:none;"';

				$output = '';
				if ( $key['type'] != "heading" ) {

					/* Keep all ids lowercase with no spaces */
					$obj_key = sanitize_title_with_dashes( $obj_key );

					$output .= '<li class="dd-item tom-item" data-id="'.esc_attr( $obj_key ).'">'."\n";
						$output .= '<div class="dd-handle"><span id="'.esc_attr( $obj_key ).'_name">' . $key['name'] ."</span>\n";
						/* button action */
						$output .= '<span class="tom-action-buttons">
										<a class="blue edit-nestable" href="#">
											<i class="dashicons dashicons-edit"></i>
										</a>
										<a class="red delete-nestable" href="#">
											<i class="dashicons dashicons-trash"></i>
										</a>
									</span>';
						$output .= '</div>'."\n";
						$output .= '<div class="nestable-input" id="'.esc_attr( $obj_key ).'" style="display:none;">'."\n";
						$output .= 		'<table class="widefat">
											  <tbody>
											    <tr class="inline-edit-row inline-edit-row-page inline-edit-page quick-edit-row quick-edit-row-page inline-edit-page alternate inline-editor">
											      <td colspan="5" class="colspanchange" style="padding-bottom:10px;">
											        <fieldset class="inline-edit-col-left">
											          <div class="inline-edit-col">
											            <h4>Edit Option : <span>'.esc_attr( $obj_key ).'</span></h4>
											            <label>
											              <span class="title">Name</span>
											              <span class="input-text-wrap input">
											                <input type="text" name="' . esc_attr( $option_name . '[' . $obj_key . ']' ) . '[name]" class="" value="' . esc_attr( $name ) . '">
											              </span>
											            </label>
											            <label>
														  <span class="title">Required</span>
														  <span class="input">
														  	<div class="required-container">
														  		<input id="'.esc_attr( $obj_key ).'-required" class="input-required" type="checkbox" name="' . esc_attr( $option_name . '[' . $obj_key . ']' ) . '[required]" value="1" '. checked( $req, '1', false) .'>';
						$output .=			              		'<span class="status">'.($req == '1' ? '( Required )' : '( Not Required )').'</span>';
						$output .=							'</div>
														  </span>
														</label>
											            <label>
											              <span class="title">
											                Description
											              </span>
											              <span class="input-text-wrap input">
											                <textarea name="' . esc_attr( $option_name . '[' . $obj_key . ']' ) . '[desc]">' . esc_attr( $desc ) . '</textarea>
											              </span>
											            </label>
											            </div>
											          </div>
											          <div class="save-button">
											        	<a href="#" class="btn button-primary save-nestable">Save</a>
											      	  	<span id="loading-'.esc_attr( $obj_key ).'" class="tom-loading" style="display:none;"><img src="' . admin_url() . 'images/spinner.gif" alt=""></span>
											      	  </div>
											        </fieldset>
											        <fieldset class="inline-edit-col-right">
											          <div class="inline-edit-col">
											            <label>
											              <span class="title">
											                Type
											              </span>
											              <span class="input-text-wrap input">
												              <select name="' . esc_attr( $option_name . '[' . $obj_key . ']' ) . '[type]" id="tom-type-'.esc_attr( $obj_key ).'" class="tom-type" data-container="'.esc_attr( $obj_key ).'">'."\n";
												                foreach ($types as $key => $option ) {
																	/* function selected dr wp @http://codex.wordpress.org/Function_Reference/selected */
																	$output .= '<option'. selected( $type, $key, false ) .' value="' . esc_attr( $key ) . '">' . esc_html( $option ) . '</option>';
																}
						$output .= 							  '</select>
									              		  </span>
											            </label>';

											            /* Never show options on typography type */
											            $hideOptions = ($type == 'typography') ? 'style="display:none;"' : $display ;
						$output .=					    '<label id="'.esc_attr( $obj_key ).'-options" '. $hideOptions .'>
											              <span class="title">
											                Options
											              </span>
											              <span class="input-text-wrap input">
												           	<div id="opt-container-'.esc_attr( $obj_key ).'" class="options-container" data-default="'.esc_attr( $obj_key ).'">
														        <div id="add-opt-'.esc_attr( $obj_key ).'" class="input-options">'."\n";
																
																switch ($type) {
																	case 'select-image':
																		$order = 1;
																		foreach ($fieldoptions as $key => $value ) {
						$output .=											'<div data-order="'.$order.'" class="input-options-group">
																				<div class="tom_media_upload repeatable_upload">
																					<div class="tom_media_button">
																						<img class="tom_media_image tom-default-image" src="'.$value.'" style="width: 30px;">		
																						<input class="input-opt input-key" name="' . esc_attr( $option_name . '[' . $obj_key . ']' ) . '[options][opt-key][]" data-key="key" value="'.$key.'" placeholder="Key">		
																						<input class="input-opt input-val tom_media_url" type="hidden" name="' . esc_attr( $option_name . '[' . $obj_key . ']' ) . '[options][opt-val][]" data-key="val" value="'.$value.'">		
																						<a href="#" class="tom_button_upload button-secondary">Change</a>		
																						<a class="btn-remove dashicons dashicons-dismiss"></a>	
																					</div>
																				</div>
																			</div>'."\n";
																		$order++;
																		}
																		break;
																	
																	default:
																		$order = 1;
																		foreach ($fieldoptions as $key => $value ) {
						$output .=											'<div data-order="'.$order.'" class="input-options-group">
																	        	<i class="dashicons dashicons-yes"></i>
																	        	<input class="input-opt input-key" name="' . esc_attr( $option_name . '[' . $obj_key . ']' ) . '[options][opt-key][]" data-key="key" value="'.$key.'" placeholder="Key">
																	        	<input class="input-opt input-val" name="' . esc_attr( $option_name . '[' . $obj_key . ']' ) . '[options][opt-val][]" data-key="value" value="'.esc_attr( $value ).'" placeholder="Value">
																	        	<a class="btn-remove dashicons dashicons-dismiss"></a>
																        	</div>'."\n";
																		$order++;
																		}
																		break;
																}
						$output .= 								'</div>
														        <p><a id="new-repeatable" href="#">Add New Field</a></p>
													        </div>
									              		  </span>
											            </label>
											            <label>
											              <span class="title">
											                Default
											              </span>
											              <span class="input-text-wrap input">';
				        $output .=						 '<div id="'.esc_attr( $obj_key ).'-default">';
															/***********************
															* Switch input type
															************************/
															switch ($type) {
																case 'select':
																	if (!empty($fieldoptions)) {
						$output .=			              			'<select id="tom-default-'.esc_attr( $obj_key ).'" name="' . esc_attr( $option_name . '[' . $obj_key . ']' ) . '[default]">';										
																		foreach ($fieldoptions as $key => $option ) {
																			/* function selected dr wp @http://codex.wordpress.org/Function_Reference/selected */
																			$output .= '<option'. selected( $val, $key, false ) .' value="' . esc_attr( $key ) . '">' . esc_html( $option ) . '</option>';
																		}
						$output .=			              			'</select>';										
																	} else {
						$output .=			              			'<select>Select default value</select>';	
																	}	
																	break;

																case 'radio':
																	if (!empty($fieldoptions)) {
						$output .=			              			'<select id="tom-default-'.esc_attr( $obj_key ).'" name="' . esc_attr( $option_name . '[' . $obj_key . ']' ) . '[default]">';										
																		foreach ($fieldoptions as $key => $option ) {
																			/* function selected dr wp @http://codex.wordpress.org/Function_Reference/selected */
																			$output .= '<option'. selected( $val, $key, false ) .' value="' . esc_attr( $key ) . '">' . esc_html( $option ) . '</option>';
																		}
						$output .=			              			'</select>';										
																	} else {
						$output .=			              			'<select>Select default value</select>';	
																	}	
																	break;
																
																case 'textarea':
						$output .=			              			'<textarea id="tom-default-'.esc_attr( $obj_key ).'" name="' . esc_attr( $option_name . '[' . $obj_key . ']' ) . '[default]">'.esc_attr( $val ).'</textarea>';
																	break;

																case 'checkbox':
						$output .=			              			'<div id="tom-default-'.esc_attr( $obj_key ).'" class="tom-checkbox-default">';
						$output .=			              				'<input type="checkbox" name="' . esc_attr( $option_name . '[' . $obj_key . ']' ) . '[default]" value="1" '. checked( $val, '1', false) .'>';
						$output .=			              				'<span class="status">'.($val == '1' ? '( Checked )' : '( Not Checked )').'</span>';
						$output .=			              			'</div>';
																	break;

																case 'multicheck':
																	if (!empty($fieldoptions)) {
						$output .=			              			'<div id="tom-default-'.esc_attr( $obj_key ).'" class="tom-checkbox-default">';										
																		foreach ($fieldoptions as $chkey => $chname ) {
																			$chdef = ! empty( $val[$chkey] ) ? $val[$chkey] : '';
																			/* function selected dr wp @http://codex.wordpress.org/Function_Reference/selected */
																			$output .= '<div class="input-group-multicheck">';
																			$output .= 		'<input class="input-multicheck" type="checkbox" name="' . esc_attr( $option_name . '[' . $obj_key . ']' ) . '[default]['.$chkey.']" value="1" '. checked( $chdef, '1', false) .'> '. $chname; 
																			$output .= 		' <span class="status">'.($chdef == '1' ? '( Checked )' : '( Not Checked )').'</span><br>';
																			$output .= '</div>';
																		}
						$output .=			              			'</div>';										
																	} else {
																			$output .= '<div id="tom-default-'.esc_attr( $obj_key ).'" class="tom-checkbox-default">';	
																			$output .= '<div class="input-group-multicheck"><input class="input-multicheck" type="checkbox" disabled="disabled"><span class="status">Please create field options</span><br></div>';	
																			$output .= '</div>';	
																	}	
																	break;

																case 'upload':
																			$display = (empty($val)) ? 'style="display:none;"' : '';
						$output .=											'<div id="' . esc_attr( $obj_key ) . '" class="tom_media_upload">';
						$output .=											'	<img class="tom_media_image tom-option-image" src="'.$val.'" '. $display .'/>';
						$output .=											'	<div>';
						$output .=											'		<input class="tom_media_url" type="hidden" value="">';
						$output .=											'		<input class="tom_media_id" type="hidden" name="' . esc_attr( $option_name . '[' . $obj_key . ']' ) . '[default]" value="' . esc_attr( $val ) . '">';
						$output .=											'		<a href="#" class="tom_button_upload button-secondary">Choose</a>';
						$output .=											'		<a href="#" class="tom_remove_image button-primary" ' . $display . '>Remove</a>';
						$output .=											'	</div>';
						$output .=											'</div>';
																	break;

																case 'select-image':
																	if (!empty($fieldoptions)) {
						$output .=			              			'<select id="tom-default-'.esc_attr( $obj_key ).'" name="' . esc_attr( $option_name . '[' . $obj_key . ']' ) . '[default]">';										
																		foreach ($fieldoptions as $key => $option ) {
																			/* function selected dr wp @http://codex.wordpress.org/Function_Reference/selected */
																			$output .= '<option'. selected( $val, $key, false ) .' value="' . esc_attr( $key ) . '">' . esc_html( $key ) . '</option>';
																		}
						$output .=			              			'</select>';										
																	} else {
						$output .=			              			'<select id="tom-default-'.esc_attr( $obj_key ).'"><option value="">Select default option</option></select>';	
																	}	
																	break;

																case 'color':
						$output .=			              			'<input id="tom-default-'.esc_attr( $obj_key ).'" name="' . esc_attr( $option_name . '[' . $obj_key . ']' ) . '[default]" type="text" value="'.esc_attr( $val ).'" class="tom-color">';
																	break;
																
																case 'typography':
																	if (!empty($val)) {
																	$defaultOptions = TOMOptionFacade::tom_default_options();
						$output .=			              			'<div id="tom-default-'.esc_attr( $obj_key ).'" class="input-default typography-options" '.$display.'>
															        	<label>Color :</label>
																		<div class="color-container">
																			<input class="tom-color" name="' . esc_attr( $option_name . '[' . $obj_key . ']' ) . '[default][color]" type="text" value="'.esc_attr( $val['color'] ).'" />
																		</div>
															        	<label>Size :</label>
																		<select class="array-default tom-typography tom-typography-size" name="' . esc_attr( $option_name . '[' . $obj_key . ']' ) . '[default][size]">';
																		    foreach ($defaultOptions['font-size'] as $key => $value) {
																			$output .= '<option value="'.$value.'px" '. selected( $val['size'], $value.'px', false ) .'>'.$value.' px</option>';
																			}
						$output .=										'</select>
																	  	<br>
															        	<label>Font Face :</label>
																		<select class="array-default tom-typography tom-typography-face" name="' . esc_attr( $option_name . '[' . $obj_key . ']' ) . '[default][face]">';
																		    foreach ($defaultOptions['font-face'] as $key => $value) {
																			$output .= '<option value="'.$key.'" '. selected( $val['face'], $key, false ) .'>'.$value.'</option>';
																			}
						$output .=										'</select>
																		<br>
															        	<label>Font Style :</label>
																		<select class="array-default tom-typography tom-typography-style" name="' . esc_attr( $option_name . '[' . $obj_key . ']' ) . '[default][style]">';
																		    foreach ($defaultOptions['font-style'] as $key => $value) {
																			$output .= '<option value="'.$key.'" '. selected( $val['style'], $key, false ) .'>'.$value.'</option>';
																			}
						$output .=										'</select>
																	</div>';										
																	}
																	break;

																default:
						$output .=			              			'<input name="' . esc_attr( $option_name . '[' . $obj_key . ']' ) . '[default]" type="text" value="'.esc_attr( $val ).'">';
																	break;
															}
						$output .=			              	'</div>
											              </span>
											            </label>
											          </div>
											        </fieldset>
											      </td>
										        </tr>
									      	</tbody>
										  </table>'."\n";
						$output .= '</div>'."\n";
					$output .= '</li>'."\n";
				}

				/* Heading for Navigation */
				if (is_array($key) && $key['type'] == "heading") {
					$counter++;
					if ( $counter >= 2 ) {
						$output .= '</ol></div></div>'."\n";
					}

					/* init nestable menu */
					$initNestable .= '$("#nestable-' . $counter . '").nestable({"maxDepth":"1"});'."\n";

					$class = '';
					$class = ! empty( $obj_key ) ? $obj_key : $key['name'];
					$class = sanitize_title_with_dashes( $class );
					$desc  = (!empty($key['desc'])) ? $key['desc'] : $key['name'];
					$output .= '<div id="options-group-' . $counter . '" class="group ' . $class . '">';
					$output .= '<h3>' . esc_html( $desc ) . '</h3>' . "\n";

					/* output heading ke hidden input biar tetep kesimpen jadi array */
					$output .= '<input name="tom_options['.esc_attr( $obj_key ).'][name]" type="hidden" class="" value="' . $key['name'] .'" />
								<input name="tom_options['.esc_attr( $obj_key ).'][type]" type="hidden" class="" value="' . $key['type'] .'" />';
					$output .= '<div class="dd" id="nestable-' . $counter . '">' . "\n";
					$output .= '<ol class="dd-list container-body">' . "\n";
				} 

				echo $output;
			}

			echo '</ol></div>';
			
			// Outputs closing div if there tabs
			if ( tomGenerate::create_tom_tabs() != '' ) {
				echo '</div>'."\n";
			}
			/* Initial nestable list */
			echo '<script type="text/javascript">
					jQuery(document).ready(function($) {
						'. $initNestable .'
					});
				  </script>';

			

			/* Submit and Delete Group Button */
			$submit =  '<div id="tonjoo-tom-submit">
							<input id="tom-submit-create" type="submit" class="button-primary hide-if-empty" name="update" value="Save" />
							<span id="loading-save-group" class="tom-loading" style="float:right;padding:4px;display:none;"><img src="' . admin_url() . 'images/spinner.gif" alt=""></span>
							<a id="tom-delete-group" class="reset-button button-secondary">Delete Group</a>
							<span id="loading-delete-group" class="tom-loading" style="display:none;"><img src="' . admin_url() . 'images/spinner.gif" alt=""></span>
							<div class="clear"></div>
						</div>';
		} else {

			/* Create New Group Button */
			$submit =  '<div id="tonjoo-tom-submit">
							<input id="tom-submit-create" type="submit" class="button-primary" name="update" value="Create" />
							<div class="clear"></div>
						</div>';
			}

			/* Div Create new option group */
			echo '	<div id="new-group" class="group new-group">
					  <h3>
					    Create New Option Group
					  </h3>
					  <div class="container-table">
					  	<table class="widefat">
						  <tbody class="container-body">
						    <tr class="alternate tom-item">
								<th scope="row"><label for="group-name">Group Name</label><br><span class="description">Name of option group</span></th>
								<td><input class="tom-input" type="text" id="group-name" name="tom_options[new-group][name]" placeholder="Group Name" value=""></td>
							</tr>
						    <tr class="alternate tom-item">
								<th scope="row"><label for="group-desc">Description</label><br><span class="description">Short descriptipn</span></th>
								<td><textarea class="tom-input" id="group-desc" name="tom_options[new-group][desc]" placeholder="Description" rows="4" cols="50"></textarea></td>
							</tr>
						  </tbody>
						</table>
					  </div>
					</div>';

			/* Print Submit Button */
			echo $submit;
	}
}
