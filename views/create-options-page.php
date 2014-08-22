<div class="wrap">
	<?php $config = TOMOption::tom_configs(); ?>
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
		        <?php $config = TOMOption::tom_configs(); ?>
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

			        <?php $defaultOptions = TOMOption::tom_default_options(); ?>
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