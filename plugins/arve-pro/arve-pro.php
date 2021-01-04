<?php
/**
 * @link              https://nextgenthemes.com
 * @since             1.0.0
 * @package           Advanced_Responsive_Video_Embedder_Pro
 *
 * @wordpress-plugin
 * Plugin Name:       ARVE Pro Addon
 * Plugin URI:        https://nextgenthemes.com/plugins/advanced-responsive-video-embedder-pro/
 * Description:       Lazyload, Lightbox, automatic thumbnails + titles and more for ARVE
 * Version:           4.3.0
 * Author:            Nicolas Jonas
 * Author URI:        https://nextgenthemes.com
 * License:           GPL 3.0
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       arve-pro
 * Domain Path:       /languages
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'ARVE_PRO_VERSION', '4.3.0' );
define( 'ARVE_PRO_FILE', __FILE__ );
define( 'ARVE_PRO_URL', plugin_dir_url( __FILE__ ) );
define( 'ARVE_PRO_NODE_URL', ARVE_PRO_URL . 'node_modules/' );
define( 'ARVE_PRO_DIST_URL', ARVE_PRO_URL . 'dist/' );

add_action( 'plugins_loaded', 'arve_pro_init' );

function arve_pro_init() {

	if ( ! function_exists( 'arve_init' ) ) {
		return;
	}

	require_once __DIR__ . '/admin/functions-admin.php';
	require_once __DIR__ . '/public/class-mobile-detect.php';
	require_once __DIR__ . '/public/functions-assets.php';
	require_once __DIR__ . '/public/functions-filters.php';
	require_once __DIR__ . '/public/functions-html-output.php';
	require_once __DIR__ . '/public/functions-misc.php';
	require_once __DIR__ . '/public/functions-shortcode-filters.php';
	require_once __DIR__ . '/shared/functions-options.php';

	add_action( 'admin_init', 'arve_pro_action_register_settings' );

	add_action( 'wp_enqueue_scripts', 'arve_pro_assets', 0 );

	add_filter( 'arve_pro_ad', '__return_false' );
	add_filter( 'arve_modes', 'arve_pro_filter_modes' );
	add_filter( 'arve_shortcode_pairs', 'arve_pro_filter_shortcode_pairs' );
	add_filter( 'mce_css', 'arve_pro_filter_mce_css' );
	add_action( 'wp_enqueue_scripts', 'arve_pro_maybe_enqueue_assets', 11 );
	add_filter( 'arve_output', 'arve_pro_filter_output', 10, 3 );

	add_filter( 'nextgenthemes/arve/thumbnail', 'arve_pro_filter_thumbnail', 10, 2 );
	add_filter( 'nextgenthemes/arve/title', 'arve_pro_filter_title', 10, 2 );

	add_filter( 'shortcode_atts_arve', 'arve_pro_sc_filter_latest_channel_video', -11 );
	add_filter( 'shortcode_atts_arve', 'arve_pro_sc_filter_validate', -1 );
	add_filter( 'shortcode_atts_arve', 'arve_pro_sc_filter_oembed_img_src_and_title', 1 );
	add_filter( 'shortcode_atts_arve', 'arve_pro_sc_filter_default_seo_data', 1 );
	add_filter( 'shortcode_atts_arve', 'arve_pro_sc_filter_img_src', 2 );
	add_filter( 'shortcode_atts_arve', 'arve_pro_sc_filter_img_src_srcset', 3 );
	add_filter( 'shortcode_atts_arve', 'arve_pro_sc_filter_inview_lazyload' );
	add_filter( 'shortcode_atts_arve', 'arve_pro_sc_filter_autoplay', 11 );
	add_filter( 'shortcode_atts_arve', 'arve_pro_sc_filter_attr', 21 );
}

function arve_pro_activation_hook() {

	if ( defined( 'ARVE_PRO_KEY' ) ) {
		nextgenthemes_api_update_key_status( 'arve_pro', ARVE_PRO_KEY, 'activate' );
	}
}

register_activation_hook( __FILE__, 'arve_pro_activation_hook' );
