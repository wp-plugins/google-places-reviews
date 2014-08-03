<?php
$google_places_reviews_options = array(
    array('name' => __('About', $google_places_reviews->textdomain), 'type' => 'opentab'),
    array('type' => 'about'),

	array(
        'name' => __('Google Places API Key', $google_places_reviews->textdomain),
		'desc' => sprintf( __( 'API keys are manage through the <a href="%1$s" class="new-window" target="_blank">Google API Console</a>. For more information please <a href="%2$s" class="new-window" target="_blank">review these steps</a>.', $google_places_reviews->textdomain ), esc_url( 'https://code.google.com/apis/console/?noredirect' ), esc_url( 'https://developers.google.com/places/documentation/#Authentication' ) ),
        'std' => '',
        'id' => 'google_places_api_key',
        'type' => 'text',
        'label' => __('Yes', $google_places_reviews->textdomain)
    ),
    array('type' => 'closetab', 'actions' => true),

    //Widgets Default Tab
//    array(
//        'name' => __('Widget Defaults', $google_places_reviews->textdomain),
//        'type' => 'opentab'
//    ),
//
//    array('type' => 'closetab'),

    //Advanced Options
    array(
        'name' => __('Advanced Options', $google_places_reviews->textdomain),
        'type' => 'opentab'
    ),

    array(
        'name' => __('Disable Plugin CSS', $google_places_reviews->textdomain),
        'desc' => __('Useful to style your own widget and for theme integration and optimization.', $google_places_reviews->textdomain),
        'std' => '',
        'id' => 'disable_css',
        'type' => 'checkbox',
        'label' => __('Yes', $google_places_reviews->textdomain)
    ),
    array('type' => 'closetab')
);