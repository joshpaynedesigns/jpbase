<?php

declare(strict_types = 1);

namespace Nextgenthemes\ARVE\Pro;

function init(): void {
	require_once __DIR__ . '/fn-assets.php';
	require_once __DIR__ . '/fn-filters.php';
	require_once __DIR__ . '/fn-html-output.php';
	require_once __DIR__ . '/fn-misc.php';
	require_once __DIR__ . '/fn-shortcode-filters.php';

	add_action( 'init', __NAMESPACE__ . '\register_assets' );
	add_filter( 'shortcode_atts_arve', __NAMESPACE__ . '\shortcode_atts_extra_data', -20 );
	add_filter( 'nextgenthemes/arve/shortcode_args', __NAMESPACE__ . '\latest_youtube_video_from_channel' );
	add_filter( 'nextgenthemes/arve/args/autoplay', __NAMESPACE__ . '\arg_filter_autoplay', 10, 2 );
	add_filter( 'nextgenthemes/arve/args/thumbnail', __NAMESPACE__ . '\arg_filter_thumbnail', 10, 1 );
}

register_activation_hook( __FILE__, __NAMESPACE__ . '\activation_hook' );
function activation_hook(): void {
	if ( function_exists( '\Nextgenthemes\WP\activate_defined_key' ) ) {
		\Nextgenthemes\WP\activate_defined_key( __FILE__ );
	}
}
