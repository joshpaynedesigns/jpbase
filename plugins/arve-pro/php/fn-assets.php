<?php

declare(strict_types = 1);

namespace Nextgenthemes\ARVE\Pro;

use function Nextgenthemes\WP\ver;
use function Nextgenthemes\WP\add_dep_to_style;

function register_assets(): void {

	wp_register_style(
		'arve-pro',
		plugins_url( 'build/main.css', PLUGIN_FILE ),
		[ 'arve' ],
		ver( PLUGIN_DIR . '/build/main.css', VERSION ),
	);

	wp_register_script(
		'arve-pro',
		plugins_url( 'build/main.js', PLUGIN_FILE ),
		array(),
		ver( PLUGIN_DIR . '/build/main.js', VERSION ),
		array( 'strategy' => 'defer' ),
	);

	add_dep_to_style( 'arve-block', 'arve-pro' );
}
