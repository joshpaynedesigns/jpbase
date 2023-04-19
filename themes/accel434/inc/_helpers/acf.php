<?php

if ( ! function_exists( 'get_field' ) ) {
	add_action( 'admin_notices', 'ns_acf_is_required_notice' );
}

add_filter( 'acf/fields/google_map/api', 'ns_acf_google_map_api' );
function ns_acf_google_map_api( $api ) {
	$api['key'] = 'AIzaSyA4stZyfaZsPwdxHmW7STkkOdgjSIIroC0';
	return $api;
}

function ns_acf_is_required_notice() {
	$class   = 'notice notice-error';
	$message = __( 'This Theme requires Advanced Custom Fields Pro to be active.' );

	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
}


function ns_get_field( $selector = null, $post = null, $format_value = true ) {
	if ( function_exists( 'get_field' ) ) {
		$result = get_field( $selector, $post, $format_value );
		if ( ! empty( $result ) ) {
			return $result;
		}
	}
	return false;
}

function ns_key_value( $incoming_array = null, $key = null ) {
	if ( is_array( $incoming_array ) && ! empty( $key ) ) {
		if ( array_key_exists( $key, $incoming_array ) && ! empty( $incoming_array[ $key ] ) ) {
			return $incoming_array[ $key ];
		}
	}

	return false;
}
