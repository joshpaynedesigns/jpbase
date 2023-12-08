<?php
namespace Nextgenthemes\ARVE\Pro;

use function \Nextgenthemes\ARVE\Common\register_asset;
use function \Nextgenthemes\ARVE\Common\add_dep_to_script;
use function \Nextgenthemes\ARVE\Common\add_dep_to_style;

function register_assets(): void {

	register_asset(
		[
			'handle'    => 'arve-pro',
			'src'       => plugins_url( 'build/main.js', PLUGIN_FILE ),
			'path'      => PLUGIN_DIR . '/build/main.js',
			'deps'      => [],
			'defer'     => true,
			'in_footer' => false,
		]
	);

	register_asset(
		[
			'handle' => 'arve-pro',
			'src'    => plugins_url( 'build/main.css', PLUGIN_FILE ),
			'path'   => PLUGIN_DIR . '/build/main.css',
			'deps'   => [],
			'mce'    => true,
		]
	);

	add_dep_to_script( 'arve', 'arve-pro' );
	add_dep_to_style( 'arve', 'arve-pro' );
}
