<?php

function arve_pro_assets() {

	arve_pro_asset(
		[
			'handle'  => 'inview',
			'src'     => ARVE_PRO_NODE_URL . 'jquery-inview/jquery.inview.min.js',
			'cdn_src' => 'https://cdn.jsdelivr.net/gh/protonet/jquery.inview@v1.1.2/jquery.inview.min.js',
			'ver'     => '1.1.2',
		]
	);

	arve_pro_asset(
		[
			'handle'  => 'object-fit-polyfill',
			'src'     => ARVE_PRO_NODE_URL . 'object-fit-polyfill/dist/objectFitPolyfill.basic.min.js',
			'cdn_src' => 'https://cdn.jsdelivr.net/gh/constancecchen/object-fit-polyfill@v2.1.1/dist/objectFitPolyfill.basic.min.js',
			'ver'     => '2.1.1',
		]
	);

	arve_pro_asset(
		[
			'handle'  => 'lity',
			'src'     => ARVE_PRO_NODE_URL . 'lity/dist/lity.min.js',
			'cdn_src' => 'https://cdn.jsdelivr.net/npm/lity@2.3.1/dist/lity.min.js',
			'ver'     => '2.3.1',
		]
	);
	arve_pro_asset(
		[
			'handle'  => 'lity',
			'src'     => ARVE_PRO_NODE_URL . 'lity/dist/lity.min.css',
			'cdn_src' => 'https://cdn.jsdelivr.net/npm/lity@2.3.1/dist/lity.min.css',
			'ver'     => '2.3.1',
		]
	);

	arve_pro_asset(
		[
			'handle' => 'arve-pro',
			'src'    => arve_pro_cachebust( 'app.js' ),
			'deps'   => [ 'jquery', 'object-fit-polyfill' ],
			'ver'    => ARVE_PRO_VERSION,
		]
	);

	arve_pro_asset(
		[
			'handle' => 'arve-pro',
			'src'    => arve_pro_cachebust( 'app.css' ),
			'deps'   => [ 'advanced-responsive-video-embedder' ],
			'ver'    => ARVE_PRO_VERSION,
		]
	);

	// wp_register_script( 'vimeo-jsapi', ARVE_PRO_NODE_URL . '@vimeo/player/dist/player.min.js', array(), '2.0.1', true );.
	// wp_register_script( 'youtube-jsapi', 'https://www.youtube.com/iframe_api', array(), ARVE_PRO_VERSION, true );.
}

function arve_pro_maybe_enqueue_assets() {

	$options = arve_get_options();

	if ( $options['always_enqueue_assets'] ) {
		wp_enqueue_style( 'arve-pro' );
		wp_enqueue_script( 'arve-pro' );
		wp_enqueue_script( 'inview' );
		wp_enqueue_style( 'lity' );
		wp_enqueue_script( 'lity' );
	}
}

function arve_pro_asset( $args ) {

	$defaults = array(
		'handle'    => null,
		'src'       => null,
		'cdn_src'   => null,
		'deps'      => [],
		'in_footer' => true,
		'media'     => 'all',
		'ver'       => null,
		'cdn'       => apply_filters( 'nextgenthemes_use_cdn', true ), // phpcs:ignore
		'action'    => 'register',
	);

	$args = wp_parse_args( $args, $defaults );

	if ( $args['cdn'] && ! empty( $args['cdn_src'] ) ) {
		$args['src'] = $args['cdn_src'];
		$args['ver'] = null;
	}

	if ( ! in_array( $args['action'], [ 'enqueue', 'register' ], true ) ) {
		wp_die( 'action must be enqueue or register' );
	}

	$src_without_query = strtok( $args['src'], '?' );

	if ( '.js' === substr( $src_without_query, -3 ) ) {
		$function = "wp_{$args['action']}_script";
		$function( $args['handle'], $args['src'], $args['deps'], $args['ver'], $args['in_footer'] );
	} else {
		$function = "wp_{$args['action']}_style";
		$function( $args['handle'], $args['src'], $args['deps'], $args['ver'], $args['media'] );
	}
}

/**
 * Helper function for outputting an asset URL in the theme. This integrates
 * with Laravel Mix for handling cache busting. If used when you enqueue a script
 * or style, it'll append an ID to the filename.
 *
 * @link   https://laravel.com/docs/5.6/mix#versioning-and-cache-busting
 * @since  1.0.0
 * @access public
 * @param  string $path Path the the asset without dist dir.
 * @return string
 */
function arve_pro_cachebust( $path ) {

	// Get the Laravel Mix manifest.
	$manifest = json_decode(
		// phpcs:disable WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		file_get_contents( dirname( __DIR__ ) . '/dist/mix-manifest.json' ),
		// phpcs:enable
		true
	);

	// Make sure to trim any slashes from the front of the path.
	$path = '/' . ltrim( $path, '/' );

	if ( $manifest && isset( $manifest[ $path ] ) ) {
		$path = $manifest[ $path ];
	}

	return ARVE_PRO_URL . 'dist' . $path;
}
