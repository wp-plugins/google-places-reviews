/**
 *  Google Places Reviews JS: WP Admin
 *
 *  @description: JavaScripts for the admin side of the widget
 *  @author: Devin Walker
 *  @since: 1.0
 */

(function ( $ ) {
	"use strict";

	/**
	 * Initialize this bad boy
	 */
	$( function () {

		//Initialize the API Request Method widget radio input toggles
		gpr_widget_toogles();
		gpr_initialize_autocomplete();
		gpr_tipsy();

	} );

	/**
	 * Clear Cache on Click
	 */
	$( document ).on( 'click', '.gpr-clear-cache', function ( e ) {
		e.preventDefault();
		var $this = $( this );
		$this.next( '.cache-clearing-loading' ).fadeIn( 'fast' );
		var data = {
			action        : 'clear_widget_cache',
			transient_id_1: $( this ).data( 'transient-id-1' ),
			transient_id_2: $( this ).data( 'transient-id-2' )
		};

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post( ajaxurl, data, function ( response ) {
			$( '.cache-clearing-loading' ).hide();
			$this.prev( '.cache-message' ).text( response ).fadeIn( 'fast' ).delay( 2000 ).fadeOut();
		} );

	} );


	/**
	 * Function to Refresh jQuery toggles for Yelp Widget Pro upon saving specific widget
	 */
	$( document ).ajaxSuccess( function ( e, xhr, settings ) {
		gpr_widget_toogles();
		gpr_initialize_autocomplete();
		gpr_tipsy();
	} );


	/**
	 * Hover Toggle Containers
	 */
	function gpr_widget_toogles() {

		//Advanced Options Toggle (Bottom-gray panels)
		$( '.gpr-widget-toggler:not("clickable")' ).each( function () {

			$( this ).addClass( "clickable" ).unbind( "click" ).click( function () {
				$( this ).toggleClass( 'toggled' );
				$( this ).next().slideToggle();
			} )

		} );


	}

	/**
	 * Initialize Google Places Autocomplete Field
	 */
	function gpr_initialize_autocomplete() {
		var input = $( '.gpr-autocomplete' );

		input.each( function ( index, value ) {
			var options = {
				types: ['establishment']
			};
			var autocomplete = new google.maps.places.Autocomplete( input[index], options );
			add_autocomplete_listener( autocomplete, input[index] );

			//Tame the enter key to not save the widget while using the autocomplete input
			$( input ).keydown( function ( e ) {
				if ( e.which == 13 ) return false;
			} );


		} );


	}

	function add_autocomplete_listener( autocomplete, input ) {
		google.maps.event.addListener( autocomplete, 'place_changed', function () {

			var place = autocomplete.getPlace();
			if ( !place.reference ) {
				alert( 'No place reference found for this location.' )
			}

			//set location and reference hidden input value
			$( input ).parentsUntil( 'form' ).find( '.location' ).val( place.name );
			$( input ).parentsUntil( 'form' ).find( '.reference' ).val( place.reference );
			$( input ).parentsUntil( 'form' ).find( '.set-business' ).slideDown();


		} );
	}


	/**
	 * Tipsy Tooltips for Info Bubbles
	 */
	function gpr_tipsy() {
		//Tooltips for admins
		$( '.tooltip-info' ).tipsy( {
			fade    : true,
			html    : true,
			gravity : 's',
			delayOut: 1000,
			delayIn : 500
		} );
	}

})( jQuery );