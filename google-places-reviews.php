<?php
/*
Plugin Name: Google Places Reviews
Plugin URI: http://wordimpress.com/plugins/google-places-reviews-pro/
Description: Display Google Places Reviews for one or many businesses anywhere on your WordPress site using an easy to use and intuitive widget.
Version: 1.1.3
Author: Devin Walker
Author URI: http://imdev.in/
Text Domain: gpr
License: GPL2
*/

define( 'GPR_PLUGIN_NAME', 'google-places-reviews' );
define( 'GPR_PLUGIN_NAME_PLUGIN', plugin_basename( __FILE__ ) );
define( 'GPR_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'GPR_PLUGIN_URL', plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) );

function init_google_places_reviews_widget() {

	// Include Core Framework class
	require_once 'classes/core.php';

	// Create plugin instance
	$google_places_reviews = new GPR_Plugin_Framework( __FILE__ );

	// Include options set
	include_once 'inc/options.php';

	// Create options page
	$google_places_reviews->add_options_page( array(), $google_places_reviews_options );

	// Make plugin meta translatable
	__( 'Google Places Reviews', 'gpr' );
	__( 'Devin Walker', 'gpr' );
	__( 'gpr', 'gpr' );

	//Include the widget
	if ( ! class_exists( 'Google_Places_Reviews' ) ) {
		require 'classes/widget.php';
	}

	//Admin only
	if ( is_admin() ) {
		//Deactivating normal activation banner for upgrade to Place ID banner
		require_once GPR_PLUGIN_PATH . '/inc/admin.php';

		//Display our upgrade notice
		require_once GPR_PLUGIN_PATH . '/inc/upgrades/upgrade-functions.php';
		require_once GPR_PLUGIN_PATH . '/inc/upgrades/upgrades.php';

	}

	return $google_places_reviews;

}

/*
 * @DESC: Register Open Table widget
 */
add_action( 'widgets_init', 'init_google_places_reviews_widget' );
add_action( 'widgets_init', create_function( '', 'register_widget( "Google_Places_Reviews" );' ) );


/**
 * Custom CSS for Options Page
 */
add_action( 'admin_enqueue_scripts', 'gpr_options_scripts' );

function gpr_options_scripts( $hook ) {

	if ( 'settings_page_googleplacesreviews' != $hook ) {
		return;
	} else {
		wp_register_style( 'gpr_custom_options_styles', plugin_dir_url( __FILE__ ) . '/assets/css/options.css' );
		wp_enqueue_style( 'gpr_custom_options_styles' );

	}


}