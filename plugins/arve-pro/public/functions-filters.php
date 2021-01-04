<?php

function arve_pro_filter_shortcode_pairs( $pairs ) {

	$options = arve_pro_get_options();

	$pro_pairs = array(
		'grow'              => arve_bool_to_shortcode_string( $options['grow'] ),
		'hide_title'        => arve_bool_to_shortcode_string( $options['hide_title'] ),
		'hover_effect'      => $options['hover_effect'],
		'play_icon_style'   => $options['play_icon_style'],
		'disable_links'     => arve_bool_to_shortcode_string( $options['disable_links'] ),
		'volume'            => null,
		'lightbox_maxwidth' => 1200,
		'img_srcset'        => null,
	);

	return array_merge( $pairs, $pro_pairs );
}

function arve_pro_get_pro_modes() {

	return array(
		'lazyload'          => __( 'Lazyload', 'arve-pro' ),
		'lazyload-lightbox' => __( 'Lazyload -> Lightbox', 'arve-pro' ),
		'link-lightbox'     => __( 'Link -> Lightbox', 'arve-pro' ),
	);
}

function arve_pro_filter_modes( $modes ) {

	return array_merge( $modes, arve_pro_get_pro_modes() );
}

function arve_pro_filter_thumbnail( $args, $a ) {

	if ( in_array( $a['mode'], [ 'lazyload', 'lazyload-lightbox' ], true ) ) {

		$options = arve_pro_get_options();
		$args    = [
			'tag'  => 'img',
			'attr' => [
				'class'           => 'arve-thumbnail',
				'data-object-fit' => true,
				'itemprop'        => 'thumbnailUrl',
				'src'             => $a['img_src'],
				'srcset'          => $a['img_srcset'] ? $a['img_srcset'] : false,
				'sizes'           => sprintf( $options['thumbnail_sizes_pattern'], $a['maxwidth'] ),
				'alt'             => __( 'Video Thumbnail', 'arve-pro' ),
			],
		];
	}

	return $args;
}

function arve_pro_filter_title( $args, $a ) {

	if ( in_array($a['mode'], array( 'lazyload', 'lazyload-lightbox' ), true) && empty($a['hide_title']) ) {
		$args = [
			'tag'     => 'h5',
			'content' => trim( $a['title'] ),
			'attr'    => [
				'class'    => 'arve-title',
				'itemprop' => 'name',
			],
		];
	}

	return $args;
}
