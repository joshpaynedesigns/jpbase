<?php
namespace Nextgenthemes\ARVE\Pro;

use \Nextgenthemes\ARVE;
use \Nextgenthemes\ARVE\Common;

function register_assets() {

	$use_lity = ( 'lity' === ARVE\options()['lightbox_script'] );

	Common\asset(
		[
			'handle' => 'lity',
			'src'    => plugins_url( 'node_modules/lity/dist/lity.min.js', PLUGIN_FILE ),
			'ver'    => Common\ver( VERSION, 'node_modules/lity/dist/lity.min.js', PLUGIN_FILE ),
			'deps'   => [ 'jquery' ],
		]
	);

	Common\asset(
		[
			'handle' => 'lity',
			'src'    => plugins_url( 'node_modules/lity/dist/lity.min.css', PLUGIN_FILE ),
			'ver'    => Common\ver( VERSION, 'node_modules/lity/dist/lity.min.css', PLUGIN_FILE ),
		]
	);

	Common\asset(
		[
			'handle' => 'arve-pro',
			'src'    => plugins_url( 'build/main.js', PLUGIN_FILE ),
			'ver'    => Common\ver( VERSION, 'build/main.js', PLUGIN_FILE ),
			'deps'   => $use_lity ? [ 'lity' ] : [],
		]
	);

	Common\asset(
		[
			'handle' => 'arve-pro',
			'src'    => plugins_url( 'build/main.css', PLUGIN_FILE ),
			'ver'    => Common\ver( VERSION, 'build/main.css', PLUGIN_FILE ),
			'deps'   => $use_lity ? [ 'arve-main', 'lity' ] : [ 'arve-main' ],
			'mce'    => true,
		]
	);

	Common\add_dep_to_script( 'arve', 'arve-pro' );
	Common\add_dep_to_style( 'arve', 'arve-pro' );
}
