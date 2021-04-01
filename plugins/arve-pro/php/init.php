<?php
namespace Nextgenthemes\ARVE\Pro;

add_action( 'plugins_loaded', __NAMESPACE__ . '\init' );

function init() {

	if ( ! defined( 'Nextgenthemes\ARVE\VERSION' ) ||
		version_compare( \Nextgenthemes\ARVE\VERSION, '9.2.0', '<' )
	) {
		return;
	}

	if ( version_compare( get_option( 'nextgenthemes_arve_pro_version' ), VERSION, '<' ) ) {
		update_option( 'nextgenthemes_arve_oembed_recache', time() );
		update_option( 'nextgenthemes_arve_pro_version', VERSION );
	}

	require_once __DIR__ . '/functions-assets.php';
	require_once __DIR__ . '/functions-filters.php';
	require_once __DIR__ . '/functions-html-output.php';
	require_once __DIR__ . '/functions-misc.php';
	require_once __DIR__ . '/functions-shortcode-filters.php';
	require_once __DIR__ . '/functions-tag-filters.php';

	add_action( 'init', __NAMESPACE__ . '\register_assets' );
	add_filter( 'nextgenthemes/arve/arve_html', __NAMESPACE__ . '\append_lightbox_link', 10, 2 );
	add_filter( 'nextgenthemes/arve/iframe_html', __NAMESPACE__ . '\noscript_wrap', 10, 2 );
	add_filter( 'nextgenthemes/arve/shortcode_args', __NAMESPACE__ . '\latest_youtube_video_from_channel' );

	add_filter( 'shortcode_atts_arve', __NAMESPACE__ . '\shortcode_atts_extra_data', -20 );

	foreach ( [
		'autoplay',
		'thumbnail',
	] as $filter ) {
		add_filter( "nextgenthemes/arve/args/$filter", __NAMESPACE__ . "\\arg_filter_$filter", 10, 2 );
	}

	foreach ( [
		'arve',
		'button',
		'iframe',
		'thumbnail',
		'title',
		'video',
	] as $tag ) {
		add_filter( "nextgenthemes/arve/$tag", __NAMESPACE__ . "\\tag_filter_$tag", 10, 2 );
	};
}

register_activation_hook( __FILE__, __NAMESPACE__ . '\activation_hook' );
function activation_hook() {
	if ( function_exists( '\Nextgenthemes\ARVE\Common\activate_defined_key' ) ) {
		\Nextgenthemes\ARVE\Common\activate_defined_key( __FILE__ );
	}
}
