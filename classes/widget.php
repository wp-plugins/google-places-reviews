<?php

/**
 *  Google Places Reviews
 *
 * @description: The Google Places Reviews
 * @since      : 1.0
 */
class Google_Places_Reviews extends WP_Widget {

	public $options; //Plugin Options from Options Panel
	public $api_key; //Plugin Options from Options Panel


	/**
	 * Array of Private Options
	 *
	 * @since    1.0.0
	 *
	 * @var array
	 */
	public $widget_fields = array(
		'title'                => '',
		'location'             => '',
		'reference'            => '',
		'place_id'             => '',
		'place_type'           => '',
		'cache'                => '',
		'disable_title_output' => '',
		'widget_style'         => 'Minimal Light',
		'review_filter'        => '',
		'review_limit'         => '5',
		'review_characters'    => '1',
		'hide_header'          => '',
		'hide_out_of_rating'   => '',
		'hide_google_image'    => '',
		'target_blank'         => '1',
		'no_follow'            => '1',
	);


	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
			'gpr_widget', // Base ID
			'Google Places Reviews', // Name
			array(
				'classname'   => 'google-places-reviews',
				'description' => __( 'Display user reviews for any location found on Google Places.', 'gpr' )
			)
		);

		$this->options = get_option( 'googleplacesreviews_options' );
		//API key (muy importante!)
		$this->api_key = $this->options['google_places_api_key'];

		add_action( 'wp_enqueue_scripts', array( $this, 'add_gpr_widget_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_gpr_admin_widget_scripts' ) );
		add_action( 'wp_ajax_clear_widget_cache', array( $this, 'gpr_clear_widget_cache' ) );


	}

	//Load Widget JS Script ONLY on Widget page
	function add_gpr_admin_widget_scripts( $hook ) {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( $hook == 'widgets.php' ) {

			wp_enqueue_script( 'gpr_google_places_gmaps', 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places', array( 'jquery' ) );

			//Enqueue
			wp_enqueue_script( 'gpr_widget_admin_tipsy', plugins_url( 'assets/js/gpr-tipsy' . $suffix . '.js', dirname( __FILE__ ) ), array( 'jquery' ) );
			wp_enqueue_script( 'gpr_widget_admin_scripts', plugins_url( 'assets/js/admin-widget' . $suffix . '.js', dirname( __FILE__ ) ), array( 'jquery' ) );

			// in javascript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
			wp_localize_script(
				'gpr_widget_admin_scripts', 'ajax_object',
				array( 'ajax_url' => admin_url( 'admin-ajax.php' ) )
			);

			wp_enqueue_style( 'gpr_widget_admin_tipsy', plugins_url( 'assets/css/gpr-tipsy' . $suffix . '.css', dirname( __FILE__ ) ) );
			wp_enqueue_style( 'gpr_widget_admin_css', plugins_url( 'assets/css/admin-widget' . $suffix . '.css', dirname( __FILE__ ) ) );


		}

	}


	/**
	 * Adds Google Places Reviews Stylesheets
	 */
	function add_gpr_widget_scripts() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$gpr_css = plugins_url( 'assets/css/google-places-reviews' . $suffix . '.css', dirname( __FILE__ ) );

		if ( $this->options["disable_css"] !== "on" ) {
			wp_register_style( 'gpr_widget', $gpr_css );
			wp_enqueue_style( 'gpr_widget' );
		}

	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	function widget( $args, $instance ) {

		extract( $args );

		//loop through options array and save variables for usage within function
		foreach ( $instance as $variable => $value ) {
			${$variable} = ! isset( $instance[ $variable ] ) ? $this->widget_fields[ $variable ] : esc_attr( $instance[ $variable ] );
		}

		//Enqueue individual CSS if debug is on; otherwise plugin uses min version
		if ( defined( 'SCRIPT_DEBUG' ) === true ) {
			$this->enqueue_widget_theme_scripts( $widget_style );
		}

		//Check for a reference. If none, output error
		if ( $reference === 'No location set' && empty( $place_id ) || empty( $reference ) && $place_id === 'No location set' ) {
			$this->output_error_message( __( 'There is no location set for this widget yet.', 'gpr' ), 'error' );

			return false;
		}


		//Title filter
		if ( isset( $title ) ) {
			$title = apply_filters( 'widget_title', $instance['title'] );
		}


		// Open link in new window if set
		if ( $target_blank == '1' ) {
			$target_blank = 'target="_blank" ';
		} else {
			$target_blank = '';
		}

		// Add nofollow relation if set
		if ( $no_follow == '1' ) {
			$no_follow = 'rel="nofollow" ';
		} else {
			$no_follow = '';
		}

		/**
		 * Use the new Google Places ID (rather than reference) - but don't break older widgets/shortcodes
		 */
		if ( ( empty( $reference ) || $reference === 'No Location Set' && ! empty( $place_id ) && $place_id !== 'No location set' ) || strlen( $place_id ) < 80 ) {
			$google_places_url = add_query_arg(
				array(
					'placeid' => $place_id,
					'key'     => $this->api_key
				),
				'https://maps.googleapis.com/maps/api/place/details/json'
			);
		} else {
			//User is on old Google's reference ID
			$google_places_url = add_query_arg(
				array(
					'reference' => $reference,
					'key'       => $this->api_key
				),
				'https://maps.googleapis.com/maps/api/place/details/json'
			);
		}
		//serialize($instance) sets the transient cache from the $instance variable which can easily bust the cache once options are changed
		$transient_unique_id = substr( $place_id, 0, 25 );
		$response            = get_transient( 'gpr_widget_api_' . $transient_unique_id );
		$widget_options      = get_transient( 'gpr_widget_options_' . $transient_unique_id );
		$serialized_instance = serialize( $instance );

		// Cache: cache option is enabled
		if ( $cache !== 'None' ) {

			// Check for an existing copy of our cached/transient data
			// also check to see if widget options have updated; this will bust the cache
			if ( $response === false || $serialized_instance !== $widget_options ) {

				// It wasn't there, so regenerate the data and save the transient
				//Get Time to Cache Data
				$expiration = $cache;

				//Assign Time to appropriate Math
				switch ( $expiration ) {
					case "1 Hour":
						$expiration = 3600;
						break;
					case "3 Hours":
						$expiration = 3600 * 3;
						break;
					case "6 Hours":
						$expiration = 3600 * 6;
						break;
					case "12 Hours":
						$expiration = 60 * 60 * 12;
						break;
					case "1 Day":
						$expiration = 60 * 60 * 24;
						break;
					case "2 Days":
						$expiration = 60 * 60 * 48;
						break;
					case "1 Week":
						$expiration = 60 * 60 * 168;
						break;
				}

				// Cache data wasn't there, so regenerate the data and save the transient
				$response = $this->grp_plugin_curl( $google_places_url );
				set_transient( 'gpr_widget_api_' . $transient_unique_id, $response, $expiration );
				set_transient( 'gpr_widget_options_' . $transient_unique_id, $serialized_instance, $expiration );

			} //end response


		} else {

			//No Cache option enabled;
			$response = $this->grp_plugin_curl( $google_places_url );

		}

		//Error message
		if ( ! empty( $response->error_message ) ) {

			$this->output_error_message( $response->error_message, 'error' );
			$this->delete_transient_cache( $transient_unique_id );

			return false;

		} //No Place ID or Reference set for this widget
		elseif ( empty( $reference ) && empty( $place_id ) ) {

			$this->output_error_message( __( '<strong>INVALID REQUEST</strong>: Please check that this widget has a Google Place ID set.', 'gpr' ), 'error' );
			$this->delete_transient_cache( $transient_unique_id );

			return;

		} elseif ( isset( $response['error_message'] ) && ! empty( $response['error_message'] ) ) {

			$error = '<strong>' . $response['status'] . '</strong>: ' . $response['error_message'];
			$this->output_error_message( $error, 'error' );
			$this->delete_transient_cache( $transient_unique_id );

			return false;

		}


		//Widget Style
		$style = "gpr-" . sanitize_title( $widget_style ) . "-style";
		// no 'class' attribute - add one with the value of width
		//@see http://wordpress.stackexchange.com/questions/18942/add-class-to-before-widget-from-within-a-custom-widget
		if ( ! empty( $before_widget ) && strpos( $before_widget, 'class' ) === false ) {
			$before_widget = str_replace( '>', 'class="' . $style . '"', $before_widget );
		} // there is 'class' attribute - append width value to it
		elseif ( ! empty( $before_widget ) && strpos( $before_widget, 'class' ) !== false ) {
			$before_widget = str_replace( 'class="', 'class="' . $style . ' ', $before_widget );
		} //no 'before_widget' at all so wrap widget with div
		else {
			$before_widget = '<div class="google-places-reviews">';
			$before_widget = str_replace( 'class="', 'class="' . $style . ' ', $before_widget );
		}

		/* Before widget */
		echo $before_widget;

		// if the title is set & the user hasn't disabled title output
		if ( ! empty( $title ) && isset( $disable_title_output ) && $disable_title_output !== '1' ) {
			/* Add class to before_widget from within a custom widget
		 http://wordpress.stackexchange.com/questions/18942/add-class-to-before-widget-from-within-a-custom-widget
		 */
			// no 'class' attribute - add one with the value of width
			if ( ! empty( $before_title ) && strpos( $before_title, 'class' ) === false ) {
				$before_title = str_replace( '>', ' class="gpr-widget-title">', $before_title );
			} //widget title has 'class' attribute
			elseif ( ! empty( $before_title ) && strpos( $before_title, 'class' ) !== false ) {
				$before_title = str_replace( 'class="', 'class="gpr-widget-title ', $before_title );
			} //no 'title' at all so wrap widget with div
			else {
				$before_title = '<h3 class="">';
				$before_title = str_replace( 'class="', 'class="gpr-widget-title ', $before_title );
			}
			$after_title = empty( $after_title ) ? '</h3>' : $after_title;

			echo $before_title . $title . $after_title;
		}


		include( GPR_PLUGIN_PATH . '/inc/widget-frontend.php' );


	}


	/**
	 * @DESC: Saves the widget options
	 * @SEE WP_Widget::update
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		//loop through options array and save to new instance
		foreach ( $this->widget_fields as $field => $value ) {
			$instance[ $field ] = strip_tags( stripslashes( $new_instance[ $field ] ) );
		}


		return $instance;
	}


	/**
	 * Back-end widget form.
	 * @see WP_Widget::form()
	 */
	function form( $instance ) {

		//API Key Check:
		if ( ! isset( $this->options['google_places_api_key'] ) || empty( $this->options['google_places_api_key'] ) ) {
			$api_key_error = sprintf( __( '<p><strong>Notice: </strong>No Google Places API key detected. You will need to create an API key to use Google Places Reviews. API keys are manage through the <a href="%1$s" class="new-window" target="_blank" class="new-window">Google API Console</a>. For more information please see <a href="%2$s"  target="_blank"  class="new-window" title="Google Places API Introduction">this article</a>.</p> <p>Once you have obtained your API key enter it in the <a href="%3$s" title="Google Places Reviews Plugin Settings">plugin settings page</a>.</p>', 'gpr' ), esc_url( 'https://code.google.com/apis/console/?noredirect' ), esc_url( 'https://developers.google.com/places/documentation/#Authentication' ), admin_url( '/options-general.php?page=googleplacesreviews' ) );
			$this->output_error_message( $api_key_error, 'error' );

			return;
		}

		//loop through options array and save options to new instance
		foreach ( $this->widget_fields as $field => $value ) {
			${$field} = ! isset( $instance[ $field ] ) ? $value : esc_attr( $instance[ $field ] );
		}
		//Get the widget form
		include( GPR_PLUGIN_PATH . '/inc/widget-form.php' );


	} //end form function


	/**
	 * @DESC: CURLs the Google Places API with our url parameters and returns JSON response
	 */
	function grp_plugin_curl( $url ) {

		// Send API Call using WP's HTTP API
		$data = wp_remote_get( $url );

		if ( is_wp_error( $data ) ) {
			$error_message = $data->get_error_message();
			$this->output_error_message( "Something went wrong: $error_message", 'error' );
		}

		//Use curl only if necessary
		if ( empty( $data['body'] ) ) {

			$ch = curl_init( $url );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_HEADER, 0 );
			$data = curl_exec( $ch ); // Google response
			curl_close( $ch );
			$response = json_decode( $data, true );

		} else {
			$response = json_decode( $data['body'], true );
		}

		//GPR Reviews Array
		$gpr_reviews = array();

		//includes Avatar image from user
		//@see: https://gist.github.com/jcsrb/1081548
		if ( isset( $response['result']['reviews'] ) && ! empty( $response['result']['reviews'] ) ) {
			//Loop Google Places reviews
			foreach ( $response['result']['reviews'] as $review ) {

				$user_id = isset( $review['author_url'] ) ? str_replace( 'https://plus.google.com/', '', $review['author_url'] ) : '';

				//Add args to
				$request_url = add_query_arg(
					array(
						'alt' => 'json',
					),
					'https://picasaweb.google.com/data/entry/api/user/' . $user_id
				);

				$avatar_get      = wp_remote_get( $request_url );
				$avatar_get_body = json_decode( wp_remote_retrieve_body( $avatar_get ), true );
				$avatar_img      = preg_replace( "/^http:/i", "https:", $avatar_get_body['entry']['gphoto$thumbnail']['$t'] );
				//add array image to review array
				$review = array_merge( $review, array( 'avatar' => $avatar_img ) );
				//add full review to $gpr_views
				array_push( $gpr_reviews, $review );

			}

			//merge custom reviews array with response
			$response = array_merge( $response, array( 'gpr_reviews' => $gpr_reviews ) );


		}
		//Business Avatar
		if ( isset( $response['result']['photos'] ) ) {

			$request_url = add_query_arg(
				array(
					'photoreference' => $response['result']['photos'][0]['photo_reference'],
					'key'            => $this->api_key,
					'maxwidth'       => '300',
					'maxheight'      => '300',
				),
				'https://maps.googleapis.com/maps/api/place/photo'
			);

			$response = array_merge( $response, array( 'place_avatar' => $request_url ) );

		}

		//Google response data in JSON format
		return $response;

	}


	/**
	 * Enqueue Widget Theme Scripts
	 *
	 * Outputs the necessary scripts for the widget themes
	 *
	 * @param $widget_style
	 */
	function enqueue_widget_theme_scripts( $widget_style ) {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		//Determine which CSS to pull
		$css_raised  = GPR_PLUGIN_URL . '/assets/css/gpr-theme-raised' . $suffix . '.css';
		$css_minimal = GPR_PLUGIN_URL . '/assets/css/gpr-theme-minimal' . $suffix . '.css';
		$css_shadow  = GPR_PLUGIN_URL . '/assets/css/gpr-theme-shadow' . $suffix . '.css';
		$css_inset   = GPR_PLUGIN_URL . '/assets/css/gpr-theme-inset' . $suffix . '.css';

		if ( $widget_style === 'Minimal Light' || $widget_style === 'Minimal Dark' ) {
			//enqueue theme style
			wp_register_style( 'grp_widget_style_minimal', $css_minimal );
			wp_enqueue_style( 'grp_widget_style_minimal' );
		}
		if ( $widget_style === 'Shadow Light' || $widget_style === 'Shadow Dark' ) {
			wp_register_style( 'grp_widget_style_shadow', $css_shadow );
			wp_enqueue_style( 'grp_widget_style_shadow' );
		}
		if ( $widget_style === 'Inset Light' || $widget_style === 'Inset Dark' ) {
			wp_register_style( 'grp_widget_style_inset', $css_inset );
			wp_enqueue_style( 'grp_widget_style_inset' );
		}
		if ( $widget_style === 'Raised Light' || $widget_style === 'Raised Dark' ) {
			wp_register_style( 'grp_widget_style_raised', $css_raised );
			wp_enqueue_style( 'grp_widget_style_raised' );
		}

	}


	/**
	 * Output Error Message
	 *
	 * @param $message
	 * @param $style
	 */
	function output_error_message( $message, $style ) {

		switch ( $style ) {
			case 'error' :
				$style = 'gpr-error';
				break;
			case 'warning' :
				$style = 'gpr-warning';
				break;
			default :
				$style = 'gpr-warning';
		}

		$output = '<div class="gpr-alert ' . $style . '">';
		$output .= $message;
		$output .= '</div>';

		echo $output;

	}

	/**
	 * Get Star Rating
	 *
	 * Returns the necessary output for Google Star Ratings
	 *
	 * @param $rating
	 * @param $unix_timestamp
	 * @param $hide_out_of_rating
	 * @param $hide_google_image
	 *
	 * @return string
	 */
	function get_star_rating( $rating, $unix_timestamp, $hide_out_of_rating, $hide_google_image ) {

		$output        = '';
		$rating_value  = '<p class="gpr-rating-value" ' . ( ( $hide_out_of_rating === '1' ) ? ' style="display:none;"' : '' ) . '><span>' . $rating . '</span>' . __( ' out of 5 stars', 'gpr' ) . '</p>';
		$is_gpr_header = true;

		//AVATAR
		$google_img = '<div class="gpr-google-logo-wrap"' . ( ( $hide_google_image === '1' ) ? ' style="display:none;"' : '' )  . '><img src="' . GPR_PLUGIN_URL . '/assets/images/google-small-logo.png' . '" class="gpr-google-logo-header" title=" ' . __( 'Reviewed from Google', 'gpr' ) . '" alt="' . __( 'Reviewed from Google', 'gpr' ) . '" /></div>';


		//Header doesn't have a timestamp
		if ( $unix_timestamp ) {
			$is_gpr_header = false;
		}

		//continue with output

		$output .= '<div class="star-rating-wrap">';
		$output .= '<div class="star-rating-size" style="width:' . ( 65 * $rating / 5 ) . 'px;"></div>';
		$output .= '</div>';

		//Output rating next to stars for individual reviews
		if ( $is_gpr_header === false ) {
			$output .= $rating_value;
		}

		//Show timestamp for reviews
		if ( $unix_timestamp ) {
			$output .= '<span class="gpr-rating-time">' . $this->get_time_since( $unix_timestamp ) . '</span>';
		}

		//Show overall rating value of review
		if ( $is_gpr_header === true ) {

			//Google logo
			if ( isset( $hide_google_image ) && $hide_google_image !== 1 ) {

				$output .= $google_img;

			}
			$output .= $rating_value;
		}


		return $output;

	}

	/**
	 * Time Since
	 * Works out the time since the entry post, takes a an argument in unix time (seconds)
	 */
	static public function get_time_since( $date, $granularity = 1 ) {
		$difference = time() - $date;
		$retval     = '';
		$periods    = array(
			'decade' => 315360000,
			'year'   => 31536000,
			'month'  => 2628000,
			'week'   => 604800,
			'day'    => 86400,
			'hour'   => 3600,
			'minute' => 60,
			'second' => 1
		);

		foreach ( $periods as $key => $value ) {
			if ( $difference >= $value ) {
				$time = floor( $difference / $value );
				$difference %= $value;
				$retval .= ( $retval ? ' ' : '' ) . $time . ' ';
				$retval .= ( ( $time > 1 ) ? $key . 's' : $key );
				$granularity --;
			}
			if ( $granularity == '0' ) {
				break;
			}
		}

		return ' posted ' . $retval . ' ago';
	}

	/**
	 * AJAX Clear Widget Cache
	 */
	function gpr_clear_widget_cache() {

		if ( isset( $_POST['transient_id_1'] ) && isset( $_POST['transient_id_2'] ) ) {

			delete_transient( $_POST['transient_id_1'] );
			delete_transient( $_POST['transient_id_2'] );
			echo "Cache cleared";

		} else {
			echo "Error: Transient ID not set. Cache not cleared.";
		}

		die();

	}

	/**
	 * Delete Transient Cache
	 *
	 * Removes the transient cache when an error is displayed as to not cache error results
	 */

	function delete_transient_cache( $transient_unique_id ) {
		delete_transient( 'gpr_widget_api_' . $transient_unique_id );
		delete_transient( 'gpr_widget_options_' . $transient_unique_id );
	}

} //end Google_Places_Reviews Class
