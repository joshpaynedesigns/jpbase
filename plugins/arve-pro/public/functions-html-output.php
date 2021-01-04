<?php

function arve_pro_filter_output( $output, $html, $atts ) {

	if ( ! arve_pro_is_pro_mode( $atts['mode'] ) ) {
		return $output;
	}

	if ( 'link-lightbox' === $atts['mode'] ) {

		$html['embed_container'] = arve_pro_lity_container(
			$html['video'] . $html['meta'],
			$atts
		);

	} elseif ( 'lazyload-lightbox' === $atts['mode'] ) {

		$html['embed_container'] = arve_pro_lity_container( $html['video'], $atts );

		if ( 'html5' === $atts['provider'] ) {

			$html['embed_container'] .= arve_arve_embed_container(
				$html['video'] . $html['meta'] . arve_pro_play_btn( $atts ),
				$atts
			);

		} else {

			$html['embed_container'] .= arve_arve_embed_container(
				$html['meta'] . arve_pro_play_btn( $atts ),
				$atts
			);
		}
	} else {

		$html['embed_container'] = arve_arve_embed_container( $html['video'] . $html['meta'] . arve_pro_play_btn( $atts ), $atts );
	}

	$output = arve_arve_wrapper( $html['embed_container'] . $html['ad_link'], $atts );

	if ( 'link-lightbox' === $atts['mode'] ) {

		if ( ! empty( $atts['link_text'] ) ) {
			$atts['title'] = $atts['link_text'];
		}

		if ( empty( $atts['title'] ) ) {
			$atts['title'] = 'open video';
		}

		$link = sprintf(
			'<a href="#lity-%s" class="arve-lightbox-link" data-lity>%s</a>',
			esc_attr( $atts['wrapper_attr']['id'] ),
			$atts['title'] . $output
		);

		$output = $link;
	}

	if ( isset( $atts['wrapper_attr']['data-inview-lazyload'] ) ) {
		wp_enqueue_script( 'inview' );
	}

	if ( in_array( $atts['mode'], array( 'lazyload-lightbox', 'link-lightbox' ), true ) ) {
		wp_enqueue_style( 'lity' );
		wp_enqueue_script( 'lity' );
	}

	if ( arve_pro_use_jsapi( $atts ) ) {
		wp_enqueue_script( $atts['provider'] . '-jsapi' );
	}

	wp_enqueue_style( 'arve-pro' );
	wp_enqueue_script( 'arve-pro' );

	return $output;
}

function arve_pro_lity_container( $html, $atts ) {

	$lity_width = (int) $atts['lightbox_maxwidth'];

	$attr['id']    = 'lity-' . $atts['wrapper_attr']['id'];
	$attr['class'] = 'arve-lity-container lity-hide';

	if ( $atts['aspect_ratio'] ) {

		$attr['style'] = sprintf(
			'width:%dpx;height:%dpx;',
			$lity_width,
			arve_calculate_height( $lity_width, $atts['aspect_ratio'] )
		);

	} else {

		$attr['style'] = sprintf( 'width:%dpx;', $lity_width );
	}

	return sprintf( '<span%s>%s</span>', arve_attr( $attr ), $html );
}

function arve_pro_play_btn( $atts ) {

	if ( ! in_array( $atts['mode'], array( 'lazyload', 'lazyload-lightbox' ), true ) ) {
		return '';
	}

	$svg = '';

	if ( 'youtube' === $atts['play_icon_style'] ) {

		$svg = '<svg class="arve-play-svg" width="68" height="48" viewBox="0 0 68 48"><path class="arve-play-svg-youtube-bg" d="m .66,37.62 c 0,0 .66,4.70 2.70,6.77 2.58,2.71 5.98,2.63 7.49,2.91 5.43,.52 23.10,.68 23.12,.68 .00,-1.3e-5 14.29,-0.02 23.81,-0.71 1.32,-0.15 4.22,-0.17 6.81,-2.89 2.03,-2.07 2.70,-6.77 2.70,-6.77 0,0 .67,-5.52 .67,-11.04 l 0,-5.17 c 0,-5.52 -0.67,-11.04 -0.67,-11.04 0,0 -0.66,-4.70 -2.70,-6.77 C 62.03,.86 59.13,.84 57.80,.69 48.28,0 34.00,0 34.00,0 33.97,0 19.69,0 10.18,.69 8.85,.84 5.95,.86 3.36,3.58 1.32,5.65 .66,10.35 .66,10.35 c 0,0 -0.55,4.50 -0.66,9.45 l 0,8.36 c .10,4.94 .66,9.45 .66,9.45 z" fill="#1f1f1e" fill-opacity="0.81"></path><path d="m 26.96,13.67 18.37,9.62 -18.37,9.55 -0.00,-19.17 z" fill="#fff"></path><path d="M 45.02,23.46 45.32,23.28 26.96,13.67 43.32,24.34 45.02,23.46 z" fill="#ccc"></path></svg>';

	} elseif ( 'circle' === $atts['play_icon_style'] ) {

		$size  = 50;
		$mid   = $size / 2;
		$or    = $size / 2;
		$ir    = $or - 4;
		$ir_x2 = $ir * 2;

		$play_h           = $size - 30;
		$play_w           = $size - 35;
		$play_right_shift = 2;
		$play_h_half      = ( $play_h / 2 );
		$pt               = ( $size - $play_h ) / 2;
		$pl               = ( $size - $play_w ) / 2 + $play_right_shift;

		$svg = "
			<svg class='arve-play-svg' width='$size' height='$size' viewBox='0 0 $size $size'>
				<circle class='arve-play-svg-circle-bg' fill='black' fill-opacity='0.4' cx='$or' cy='$or' r='$or' />
				<path   class='arve-play-svg-circle-fg' fill='white' fill-opacity='0.8' fill-rule='evenodd'
					d='M $mid, $mid
					m -$ir, 0
					a $ir,$ir 0 1,0 $ir_x2,0
					a $ir,$ir 0 1,0 -$ir_x2,0
					M$pl $pt v$play_h l$play_w -{$play_h_half}z' />
			</svg>";
		$svg = preg_replace( '/\s+/', ' ', $svg );
	}

	$lazyload_lightbox = ( 'lazyload-lightbox' === $atts['mode'] ) ? true : false;

	$attr = array(
		'class'            => 'arve-play-btn arve-blay-btn-' . $atts['play_icon_style'],
		// 'href'      => $lazyload_lightbox ? '#lity-' . $atts['embed_id'] : '',
		'data-lity-target' => $lazyload_lightbox ? '#lity-' . $atts['wrapper_attr']['id'] : false,
		'data-lity'        => $lazyload_lightbox,
		'type'             => 'button',
	);

	return sprintf( '<button%s>%s</button>', arve_attr( $attr ), $svg );
}
