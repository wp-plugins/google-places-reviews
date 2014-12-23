<?php
	// No-js message
?>
<div class="error sunrise-plugin-notification hide-if-js">
	<p><?php echo $notifications['js']; ?> <a href="http://enable-javascript.com/" target="_blank"><?php _e( 'Instructions', 'gpr' ); ?></a>.</p>
</div>
<?php
	// Options reseted
	if ( isset($_GET['message']) && $_GET['message'] == 1 ) {
		?>
		<div class="updated sunrise-plugin-notification">
			<p><?php echo $notifications['reseted']; ?><small class="hide-if-no-js"><?php _e( 'Click to close', 'gpr' ); ?></small></p>
		</div>
		<?php
	}
	// Options not reseted
	if ( isset($_GET['message']) && $_GET['message'] == 2 ) {
		?>
		<div class="error sunrise-plugin-notification">
			<p><?php echo $notifications['not-reseted']; ?><small class="hide-if-no-js"><?php _e( 'Click to close', 'gpr' ); ?></small></p>
		</div>
		<?php
	}
	// Saved
	if ( isset($_GET['message']) && $_GET['message'] == 3 ) {
		?>
		<div class="updated sunrise-plugin-notification">
			<p><?php echo $notifications['saved']; ?><small class="hide-if-no-js"><?php _e( 'Click to close', 'gpr' ); ?></small></p>
		</div>
		<?php
	}
	// No changes
	if ( isset($_GET['message']) && $_GET['message'] == 4 ) {
		?>
		<div class="error sunrise-plugin-notification">
			<p><?php echo $notifications['not-saved']; ?><small class="hide-if-no-js"><?php _e( 'Click to close', 'gpr' ); ?></small></p>
		</div>
		<?php
	}
?>