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
 * Version:           5.3.8
 * Author:            Nicolas Jonas
 * Author URI:        https://nextgenthemes.com
 * License:           GPL 3.0
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       arve-pro
 * Domain Path:       /languages
 */
namespace Nextgenthemes\ARVE\Pro;

const VERSION      = '5.3.8';
const PLUGIN_FILE  = __FILE__;
const SRCSET_SIZES = [ 320, 640, 960, 1280, 1920 ];
const PLUGIN_DIR   = __DIR__;

require_once __DIR__ . '/php/init.php';
