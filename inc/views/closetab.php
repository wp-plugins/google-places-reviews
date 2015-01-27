</table>
<?php
if ( ! isset( $option['actions'] ) || isset( $option['actions'] ) && $option['actions'] !== false ) {
	?>
	<div class="sunrise-plugin-actions-bar">
		<input type="submit" value="<?php _e( 'Save changes', 'gpr' ); ?>" class="sunrise-plugin-submit button-primary" />
		<span class="sunrise-plugin-spin"><img src="<?php echo admin_url( 'images/wpspin_light.gif' ); ?>" alt="" /> <?php _e( 'Saving', 'gpr' ); ?>&hellip;</span>
		<span class="sunrise-plugin-success-tip"><img src="<?php echo $this->assets( 'images', 'success.png' ); ?>" alt="" /> <?php _e( 'Saved', 'gpr' ); ?></span>
		<a href="<?php echo $this->admin_url; ?>&action=reset" class="sunrise-plugin-reset button alignright" title="<?php _e( 'Reset all settings to default. Are you sure? This action cannot be undone!', 'gpr' ); ?>"><?php _e( 'Restore default settings', 'gpr' ); ?></a>
	</div>
<?php } ?>
</div>