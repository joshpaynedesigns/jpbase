<?php
/**
 * @link              https://nextgenthemes.com
 * @since             1.0.0
 * @package           Advanced_Responsive_Video_Embedder_Pro
 *
 * @wordpress-plugin
 * Plugin Name:       ARVE Pro Addon
 * Plugin URI:        https://nextgenthemes.com/plugins/arve-pro/
 * Description:       Lazyload, Lightbox, automatic thumbnails + titles and more for ARVE
 * Version:           5.1.10
 * Author:            Nicolas Jonas
 * Author URI:        https://nextgenthemes.com
 * License:           GPL 3.0
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       arve-pro
 * Domain Path:       /languages
 */

namespace Nextgenthemes\ARVE\Pro;

use \Nextgenthemes\ARVE;

const VERSION      = '5.1.10';
const PLUGIN_FILE  = __FILE__;
const SRCSET_SIZES = [ 320, 640, 960, 1280, 1920 ];
const PLUGIN_DIR   = __DIR__;

add_action( 'plugins_loaded', __NAMESPACE__ . '\init' );

function init() {

	if ( ! defined('Nextgenthemes\ARVE\VERSION') ||
		version_compare( \Nextgenthemes\ARVE\VERSION, '9.2.0', '<' )
	) {
		return;
	}

	if ( version_compare( get_option( 'nextgenthemes_arve_pro_version'), VERSION, '<' ) ) {
		update_option( 'nextgenthemes_arve_oembed_recache', time() );
		update_option( 'nextgenthemes_arve_pro_version', VERSION );
	}

	require_once __DIR__ . '/php/Admin/functions-admin.php';
	require_once __DIR__ . '/php/functions-assets.php';
	require_once __DIR__ . '/php/functions-filters.php';
	require_once __DIR__ . '/php/functions-html-output.php';
	require_once __DIR__ . '/php/functions-misc.php';
	require_once __DIR__ . '/php/functions-shortcode-filters.php';
	require_once __DIR__ . '/php/functions-tag-filters.php';

	add_action( 'init', __NAMESPACE__ . '\register_assets' );
	add_filter( 'nextgenthemes/arve/arve_html', __NAMESPACE__ . '\append_lightbox_link', 10, 2 );
	add_filter( 'nextgenthemes/arve/iframe_html', __NAMESPACE__ . '\noscript_wrap', 10, 2 );
	add_filter( 'nextgenthemes/arve/shortcode_args', __NAMESPACE__ . '\latest_youtube_video_from_channel' );

	add_filter( 'shortcode_atts_arve', __NAMESPACE__ . '\shortcode_atts_extra_data', -20 );

	foreach ( [
		'validate',
		'autoplay',
		'thumbnail',
		'img_src',
		'img_srcset',
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

	update_option( 'nextgenthemes_arve_oembed_recache', time() );

	if ( function_exists( '\Nextgenthemes\ARVE\Common\activate_defined_key' ) ) {
		\Nextgenthemes\ARVE\Common\activate_defined_key( __FILE__ );
	}
}
