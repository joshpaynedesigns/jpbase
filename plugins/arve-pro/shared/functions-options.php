<?php

function arve_pro_get_min_suffix() {
	return ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? '' : '.min';
}

function arve_pro_get_options() {

	$options  = get_option( 'arve_options_pro', array() );
	$defaults = arve_pro_get_options_defaults();

	return wp_parse_args( $options, $defaults );
}

function arve_pro_get_options_defaults() {

	return array(
		'thumbnail_fallback'      => '',
		'transient_expire_time'   => 86400, // 1 day
		'play_icon_style'         => 'youtube',
		'disable_links'           => false,
		'grow'                    => true,
		'hide_title'              => false,
		'hover_effect'            => 'zoom',
		'inview_lazyload'         => true,
		'thumbnail_sizes_pattern' => '(max-width: 640px) 100vw, %spx',
	);
}

function arve_pro_get_settings_definitions() {

	$options = arve_pro_get_options();

	$properties = array(
		array(
			'attr'         => 'thumbnail_fallback',
			'hide_from_sc' => true,
			'label'        => __( 'Thumbnail Fallback', 'arve-pro' ),
			'type'         => 'text',
			'meta'         => array(
				'placeholder' => __( 'URL or media gallery image ID used for thumbnail', 'arve-pro' ),
			),
		),
		array(
			'attr'        => 'hide_title',
			'label'       => esc_html__( 'Hide Title', 'arve-pro' ),
			'type'        => 'bool',
			'description' => esc_html__( 'Usefull when the thumbnail image already displays the video title (Lazyload mode). The title will still be used for SEO.' ),
		),
		array(
			'attr'        => 'grow',
			'label'       => __( 'Expand on play?', 'arve-pro' ),
			'type'        => 'bool',
			'description' => __( 'Expands video size after clicking the thumbnail (Lazyload Mode)', 'arve-pro' ),
		),
		array(
			'attr'    => 'play_icon_style',
			'label'   => __( 'Play Button', 'arve-pro' ),
			'type'    => 'select',
			'options' => array(
				// Translators: 1 %s is play icon style.
				''        => sprintf( esc_html__( 'Default (current setting: %s)', 'arve-pro' ), $options['play_icon_style'] ),
				'youtube' => __( 'Youtube style', 'arve-pro' ),
				'circle'  => __( 'Circle', 'arve-pro' ),
				'none'    => __( 'No play image', 'arve-pro' ),
			),
		),
		array(
			'attr'         => 'hover_effect',
			'hide_from_sc' => true,
			'label'        => __( 'Hover Effect', 'arve-pro' ),
			'type'         => 'select',
			'options'      => array(
				'zoom'      => __( 'Zoom Thumbnail', 'arve-pro' ),
				'rectangle' => __( 'Move Rectangle in', 'arve-pro' ),
				'none'      => __( 'None', 'arve-pro' ),
			),
		),
		array(
			'attr'         => 'disable_links',
			'hide_from_sc' => true,
			'label'        => esc_html__( 'Disable links', 'arve-pro' ),
			'type'         => 'bool',
			'description'  => __( 'Prevent ARVE embeds to open new popups/tabs/windows from links inside video embeds. Note this also breaks all kinds of sharing functionality and the like. (Pro Addon)' ),
		),
		array(
			'attr'         => 'transient_expire_time',
			'hide_from_sc' => true,
			'label'        => __( 'External Image Cache Time', 'arve-pro' ),
			'type'         => 'number',
			'description'  => __( '(seconds) This plugin uses WordPress transients to cache video thumbnail URLS that greatly speeds up Page loading. This setting defines how long external image URLs are beeing stored without contacting the hosts APIs again. For example: hour - 3600, day - 86400, week - 604800.', 'arve-pro' ),
		),
		array(
			'attr'         => 'inview_lazyload',
			'hide_from_sc' => true,
			'label'        => __( 'Inview Lazyload', 'arve-pro' ),
			'type'         => 'bool',
			'description'  => __( 'The inview lazyload mode videos as they come into the screen as a workarround for the problem that it otherwise needs two touches to play a lazyloaded video because mobile browsers prevent autoplay. Note that this will prevent users to see your custom thumbnails or titles!', 'arve-pro' ),
		),
	);

	$options = arve_pro_get_options();

	foreach ( $properties as $key => $value ) {

		if ( 'bool' === $value['type'] ) {
			$properties[ $key ]['type']          = 'select';
			$properties[ $key ]['sanitise_func'] = 'boolval';
			$properties[ $key ]['options']       = array(
				// Translators: $s is current setting.
				''    => __( 'Default (settings page)', 'arve-pro' ),
				'yes' => __( 'Yes', 'arve-pro' ),
				'no'  => __( 'No', 'arve-pro' ),
			);
		}
	}

	return $properties;
}
