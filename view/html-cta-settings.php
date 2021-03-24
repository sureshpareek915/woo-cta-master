<div class="wtb-setting-wrapper">
	<div class="heading-wrapper">
		<h2 class="heading"><?php _e('CTA Settings');  ?></h2>
	</div>	
	<form action="" method="POST" name="wtb-settings">
		<?php wp_nonce_field( 'save_cta_settings', '_cta_settings' ); ?>
		<?php echo $this->prepare_fields(); ?>
	</form>
</div>
<style>
	.wtb-setting-wrapper {
	    background: #fff;
	    border-left: 4px solid #fff;
	    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
	    margin: 5px 15px 2px;
	    padding: 1px 12px;
	}
	h2.heading {
	    margin: 0;
	    color: #fff;
	}
	.heading-wrapper {
	    background: #e37e0a;
	    color: #fff;
	    padding: 15px;
	    margin-left: -16px;
	    margin-right: -12px;
	    margin-top: -1px;
	}
	.wtb-setting-wrapper select, .wtb-setting-wrapper input[type=text] {
	    min-width: 200px;
	}
</style>