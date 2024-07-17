<?php declare(strict_types=1);
namespace Nextgenthemes\ARVE\Pro;

add_action( 'plugins_loaded', __NAMESPACE__ . '\init', 15 );

function init(): void {

	if ( ! defined( 'Nextgenthemes\ARVE\VERSION' ) ||
		version_compare( \Nextgenthemes\ARVE\VERSION, '10.1.2-alpha1', '<' )
	) {
		return;
	}

	if ( version_compare( (string) get_option( 'nextgenthemes_arve_pro_version' ), VERSION, '<' ) ) {
		// Maybe delete oembed cache
		\update_option( 'nextgenthemes_arve_pro_version', VERSION );
	}

	require_once __DIR__ . '/fn-assets.php';
	require_once __DIR__ . '/fn-filters.php';
	require_once __DIR__ . '/fn-html-output.php';
	require_once __DIR__ . '/fn-misc.php';
	require_once __DIR__ . '/fn-shortcode-filters.php';
	require_once __DIR__ . '/fn-tag-filters.php';

	add_action( 'init', __NAMESPACE__ . '\register_assets' );
	add_filter( 'shortcode_atts_arve', __NAMESPACE__ . '\shortcode_atts_extra_data', -20 );
	add_filter( 'nextgenthemes/arve/inner_html', __NAMESPACE__ . '\lightbox_link', 10, 2 );
	add_filter( 'nextgenthemes/arve/iframe_html', __NAMESPACE__ . '\noscript_wrap', 10, 2 );
	add_filter( 'nextgenthemes/arve/shortcode_args', __NAMESPACE__ . '\latest_youtube_video_from_channel' );
	add_filter( 'nextgenthemes/arve/iframe_attr', __NAMESPACE__ . '\iframe_attr', 10, 2 );
	add_filter( 'nextgenthemes/arve/args/autoplay', __NAMESPACE__ . '\arg_filter_autoplay', 10, 2 );
	add_filter( 'nextgenthemes/arve/args/thumbnail', __NAMESPACE__ . '\arg_filter_thumbnail', 10, 1 );

	foreach ( [
		'arve',
		'button',
		'thumbnail',
		'title',
		'video',
	] as $tag ) {
		add_filter( "nextgenthemes/arve/$tag", __NAMESPACE__ . "\\tag_filter_$tag", 10, 2 );
	}
}

register_activation_hook( __FILE__, __NAMESPACE__ . '\activation_hook' );
function activation_hook(): void {
	if ( function_exists( '\Nextgenthemes\WP\activate_defined_key' ) ) {
		\Nextgenthemes\WP\activate_defined_key( __FILE__ );
	}
}
