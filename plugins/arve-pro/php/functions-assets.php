<?php
namespace Nextgenthemes\ARVE\Pro;

use \Nextgenthemes\ARVE\Common;

function register_assets() {

	Common\register_asset(
		[
			'handle' => 'arve-pro',
			'src'    => plugins_url( 'build/main.js', PLUGIN_FILE ),
			'path'   => PLUGIN_DIR . '/build/main.js',
			'deps'   => [],
			'defer'  => true,
			'footer' => false,
		]
	);

	Common\register_asset(
		[
			'handle' => 'arve-pro',
			'src'    => plugins_url( 'build/main.css', PLUGIN_FILE ),
			'path'   => PLUGIN_DIR . '/build/main.css',
			'deps'   => [],
			'mce'    => true,
		]
	);

	Common\add_dep_to_script( 'arve', 'arve-pro' );
	Common\add_dep_to_style( 'arve', 'arve-pro' );
}
