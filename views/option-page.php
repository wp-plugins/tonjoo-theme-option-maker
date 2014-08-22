<div  class="wrap">
	<?php $config = TOMOption::tom_configs(); ?>
	<h2><?php echo esc_html( $config['page_title'] ); ?></h2>
	<p><?php echo esc_html( $config['page_desc'] ); ?></p>
	<p><?php echo $config['page_manual']; ?></p>
	<?php 
	$updateNotice = '<div id="setting-error-save_options" class="updated fade settings-error below-h2"> 
		<p><strong>Options saved.</strong></p></div>';
	?>
    <p id="tom-notification"><?php echo (isset($_GET['settings-updated'])) ? $updateNotice : ''; ?></p>

    <h2 class="nav-tab-wrapper">
        <?php echo TOMGenerate::tom_tabs(); ?>
    </h2>

    <div id="tom-options-panel" class="metabox-holder metabox-main metabox-options">
	    <div id="tonjoo-tom" class="postbox">
			<form action="options.php" method="post">
			<?php settings_fields( 'tonjoo-tom' ); ?>
			<?php TOMGenerate::tom_generate_options_fields(); /* Settings */ ?>
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