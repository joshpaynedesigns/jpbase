<?php
namespace Nextgenthemes\ARVE\Pro;

use \Nextgenthemes\ARVE\Common;
use function Nextgenthemes\ARVE\options;

function json_api_call( $api_url, $atts ) {

	$wp_remote_get_args = [];

	// wp_remote_get_fails for yahoo.
	if ( 'yahoo' === $atts['provider'] ) {

		// phpcs:disable WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$response = file_get_contents( $api_url ); // TODO Check wp_remote_post.
		// phpcs:enable WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		return json_decode( $response );

	} elseif ( 'twitch' === $atts['provider'] ) {

		$wp_remote_get_args['headers'] = [ 'Client-ID' => 'in8d3vsv6bmbmsdrfoch204ict7kos7' ];
	}

	$response = Common\remote_get_json( $api_url, $wp_remote_get_args );
}

function is_mobile() {

	static $is_mobile = null;

	if ( null === $is_mobile ) {
		$detect    = new \Mobile_Detect();
		$is_mobile = $detect->isMobile();
	}

	return $is_mobile;
}
