<?php

function arve_pro_filter_mce_css( $mce_css ) {

	if ( ! empty( $mce_css ) ) {
		$mce_css .= ',';
	}
	$mce_css .= ARVE_PRO_DIST_URL . 'app.css';

	return $mce_css;
}

function arve_pro_action_register_settings() {

	$options = arve_pro_get_options();

	$title = __( 'Pro Options', 'arve-pro' );
	add_settings_section(
		'pro_section',
		sprintf( '<span class="arve-settings-section" id="arve-settings-section-pro" title="%s"></span>%s', esc_attr( $title ), esc_html( $title ) ),
		null,
		'advanced-responsive-video-embedder'
	);

	foreach ( arve_pro_get_settings_definitions() as $key => $value ) {

		if ( ! empty( $value['hide_from_settings'] ) ) {
			continue;
		};

		if ( empty( $value['meta'] ) ) {
			$value['meta'] = array();
		};

		if ( isset( $value['options'][''] ) ) {
			unset( $value['options'][''] );
		}

		if ( in_array( $value['type'], array( 'text', 'number', 'url' ), true ) ) {
			$callback_function = 'arve_input';
		} else {
			$callback_function = 'arve_' . $value['type'];
		}

		add_settings_field(
			"arve_options_pro[{$value['attr']}]", // ID.
			$value['label'],                      // title.
			$callback_function,                   // callback.
			'advanced-responsive-video-embedder', // page.
			'pro_section',                        // section.
			array(                                // args.
				'label_for'     => ( 'radio' === $value['type'] ) ? null : "arve_options_pro[{$value['attr']}]",
				'input_attr'    => $value['meta'] + array(
					'type'  => $value['type'],
					'value' => $options[ $value['attr'] ],
					'id'    => "arve_options_pro[{$value['attr']}]",
					'name'  => "arve_options_pro[{$value['attr']}]",
				),
				'description'   => ! empty( $value['description'] ) ? $value['description'] : null,
				'option_values' => $value,
			)
		);
	}

	add_settings_field(
		'arve_options_pro[reset]',
		null,
		'arve_submit_reset',
		'advanced-responsive-video-embedder',
		'pro_section',
		array(
			'reset_name' => 'arve_options_pro[reset]',
		)
	);

	register_setting( 'arve-settings-group', 'arve_options_pro', 'arve_pro_validate_options' );
}

function arve_pro_validate_options( $input ) {

	// Storing the Options Section as a empty array will cause the plugin to use defaults.
	if ( isset( $input['reset'] ) ) {
		return array();
	}

	$output['grow']            = ( 'yes' === $input['grow'] ) ? true : false;
	$output['hide_title']      = ( 'yes' === $input['hide_title'] ) ? true : false;
	$output['disable_links']   = ( 'yes' === $input['disable_links'] ) ? true : false;
	$output['inview_lazyload'] = ( 'yes' === $input['inview_lazyload'] ) ? true : false;

	$output['hover_effect']       = sanitize_text_field( $input['hover_effect'] );
	$output['play_icon_style']    = sanitize_text_field( $input['play_icon_style'] );
	$output['thumbnail_fallback'] = sanitize_text_field( $input['thumbnail_fallback'] );

	if ( (int) $input['transient_expire_time'] >= 1 ) {
		$output['transient_expire_time'] = (int) $input['transient_expire_time'];
	}

	// Store only the options in the database that are different from the defaults.
	return array_diff_assoc( $output, arve_pro_get_options_defaults() );
}
