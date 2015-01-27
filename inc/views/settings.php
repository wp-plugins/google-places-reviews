<?php do_action( 'sunrise_page_before' ); ?>
<div class="gpr-about-wrap">
	<svg height="50px" id="Layer_1" style="enable-background:new 0 0 67 67;" version="1.1" viewBox="0 0 67 67" width="50px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path d="M8.237,26.788c0-2.091,0.21-4.253,1.569-5.938  c1.281-1.606,3.518-2.648,5.598-2.648c6.633,0,9.982,8.986,9.982,14.767c0,1.156-0.589,3.419-2.088,5.137  c-1.314,1.499-3.364,2.008-5.361,2.008C11.061,40.112,8.237,32.161,8.237,26.788z M50.074,25.573V15.435h-4.346v10.139H35.59v4.257  h10.139V40.06h4.346V29.83H60.25v-4.257H50.074z M10.023,52.445L5.354,49.75v-8.559c2.024,1.483,4.255,2.124,7.553,2.124  c0.801,0,1.678-0.081,2.56-0.161c-0.399,0.965-0.799,1.769-0.799,3.132c0,2.485,1.277,4.008,2.398,5.455  C15.196,51.867,12.713,51.951,10.023,52.445z M29.709,63.812L13.39,54.39c2.191-0.335,4.099-0.4,4.558-0.4  c0.796,0,1.195,0,1.832,0.082C25.424,58.096,29.197,60.245,29.709,63.812z M33.5,1l28.146,16.25v32.5L34.564,65.386  c0.45-1.465,0.686-2.396,0.686-3.853c0.002-5.538-4.594-8.271-8.11-11.24l-2.882-2.242c-0.878-0.723-2.077-1.687-2.077-3.448  c0-1.768,1.198-2.894,2.238-3.935c3.358-2.646,6.487-5.458,6.487-11.396c0-5.421-2.928-8.464-4.891-10.165h4.9l4.489-3.621H18.506  c-4.01,0.075-8.76,0.653-13.152,4V17.25L33.5,1z" style="fill-rule:evenodd;clip-rule:evenodd;fill:#D6492F;"/></svg>
</div>
<a href="http://wordimpress.com/plugins/google-places-reviews-pro/" class="new-window upgrade-link"><?php _e('Upgrade to Pro', 'gpr'); ?></a>
<p class="label">Free Version <?php echo $this->version; ?></p>

<div id="sunrise-plugin-settings" class="wrap">
	<h2 id="sunrise-plugin-tabs" class="nav-tab-wrapper hide-if-no-js">
		<?php
		// Show tabs
		$this->render_tabs();
		?>
	</h2>
	<?php
	// Show notifications
	$this->notifications(
		array(
			'js'          => __( 'For full functionality of this page it is recommended to enable javascript.', 'gpr' ),
			'reseted'     => __( 'Settings Reset Successfully', 'gpr' ),
			'not-reseted' => __( 'Plugins already set to default settings', 'gpr' ),
			'saved'       => __( 'Settings saved successfully', 'gpr' ),
			'not-saved'   => __( 'Settings not saved due to no changes being made.', 'gpr' )
		)
	);
	?>
	<form action="<?php echo $this->admin_url; ?>" method="post" id="sunrise-plugin-options-form">
		<?php
		// Show options
		$this->render_panes();
		?>
		<input type="hidden" name="action" value="save" />
	</form>


</div>
<?php do_action( 'sunrise_page_after' ); ?>
