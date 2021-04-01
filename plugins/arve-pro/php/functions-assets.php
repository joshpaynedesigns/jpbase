<?php
namespace Nextgenthemes\ARVE\Pro;

use \Nextgenthemes\ARVE;
use \Nextgenthemes\ARVE\Common;

function register_assets() {

	$use_lity = ( 'lity' === ARVE\options()['lightbox_script'] );

	Common\asset(
		[
			'handle' => 'lity',
			'src'    => plugins_url( 'build/lity.min.js', PLUGIN_FILE ),
			'path'   => PLUGIN_DIR . '/build/lity.min.js',
			'deps'   => [ 'jquery' ],
		]
	);

	Common\asset(
		[
			'handle' => 'lity',
			'src'    => plugins_url( 'build/lity.min.css', PLUGIN_FILE ),
			'path'   => PLUGIN_DIR . '/build/lity.min.css',
		]
	);

	Common\asset(
		[
			'handle' => 'arve-pro',
			'src'    => plugins_url( 'build/main.js', PLUGIN_FILE ),
			'path'   => PLUGIN_DIR . '/build/main.js',
			'deps'   => $use_lity ? [ 'lity' ] : [],
		]
	);

	Common\asset(
		[
			'handle' => 'arve-pro',
			'src'    => plugins_url( 'build/main.css', PLUGIN_FILE ),
			'path'   => PLUGIN_DIR . '/build/main.css',
			'deps'   => $use_lity ? [ 'lity' ] : [],
			'mce'    => true,
		]
	);

	Common\add_dep_to_script( 'arve', 'arve-pro' );
	Common\add_dep_to_style( 'arve', 'arve-pro' );
}
