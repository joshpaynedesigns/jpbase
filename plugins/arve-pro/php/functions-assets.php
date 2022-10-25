<?php
namespace Nextgenthemes\ARVE\Pro;

use \Nextgenthemes\ARVE;
use \Nextgenthemes\ARVE\Common;

function register_assets() {

	Common\asset(
		[
			'handle' => 'arve-pro',
			'src'    => plugins_url( 'build/main.js', PLUGIN_FILE ),
			'path'   => PLUGIN_DIR . '/build/main.js',
			'deps'   => [],
		]
	);

	Common\asset(
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
