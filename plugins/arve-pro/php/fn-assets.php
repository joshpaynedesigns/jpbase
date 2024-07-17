<?php declare(strict_types=1);
namespace Nextgenthemes\ARVE\Pro;

use function Nextgenthemes\WP\register_asset;
use function Nextgenthemes\WP\add_dep_to_script;
use function Nextgenthemes\WP\add_dep_to_style;

function register_assets(): void {

	register_asset(
		[
			'handle' => 'arve-pro',
			'src'    => plugins_url( 'build/main.css', PLUGIN_FILE ),
			'path'   => PLUGIN_DIR . '/build/main.css',
			'deps'   => [ 'arve' ],
			'mce'    => true,
		]
	);

	register_asset(
		[
			'handle'    => 'arve-pro',
			'src'       => plugins_url( 'build/main.js', PLUGIN_FILE ),
			'path'      => PLUGIN_DIR . '/build/main.js',
			'deps'      => [],
			'strategy'  => 'defer',
		]
	);

	add_dep_to_style( 'arve-block', 'arve-pro' );
}
