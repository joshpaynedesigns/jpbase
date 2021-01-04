<?php
namespace Nextgenthemes\ARVE\Pro;

use \Nextgenthemes\ARVE;

// add_filter( 'nextgenthemes/arve/iframe', __NAMESPACE__ . '\no_tag_when_lity', 10, 2 );
// add_filter( 'nextgenthemes/arve/video', __NAMESPACE__ . '\no_tag_when_lity', 10, 2 );

// function is_lity( array $a ) {
// 	$later = true;
// 	return $later && in_array( $a['mode'], [ 'lightbox', 'link-lightbox' ], true );
// }

// function no_tag_when_lity( $tag, array $a ) {

// 	if ( is_lity( $a ) ) {
// 		$tag['tag'] = false;
// 	}

// 	return $tag;
// }

/// add_filter( 'nextgenthemes/arve/arve', __NAMESPACE__ . '\after_arve_', 10, 2 );

// function after_embed( $html, array $a ) {

// 	#return $html . '<div style="background: red;">hello</div>';

// 	if ( is_lity( $a ) ) {

// 		if ( 'html5' === $a['provider'] ) {
// 			$vid = ARVE\build_video_tag( $a );
// 		} else {
// 			$vid = ARVE\build_iframe_tag( $a );
// 		}

// 		add_filter( 'nextgenthemes/arve/embed', __NAMESPACE__ . '\make_embed_lity_container', 10, 2 );
// 		$html = ARVE\arve_embed( '<noscript>' . $vid . '</noscript>', $a );
// 		remove_filter( 'nextgenthemes/arve/embed', __NAMESPACE__ . '\make_embed_lity_container' );
// 	}

// 	return $html;
// }

// function make_embed_lity_container( array $tag, array $a ) {
// 	$tag['attr']['class'] .= ' arve-embed--lity arve-lity-container';
// 	return $tag;
// }

function noscript_wrap( $html, array $a ) {
	if ( in_array( $a['mode'], [ 'lazyload', 'lightbox', 'link-lightbox' ], true ) ) {
		$html = '<noscript class="arve-noscript">' . $html . '</noscript>';
	}
	return $html;
}

function is_lity( array $a ) {
	$later = true;
	return $later && in_array( $a['mode'], [ 'lightbox', 'link-lightbox' ], true );
}

function tag_filter_arve( array $tag, array $a ) {

	if ( 'link-lightbox' === $a['mode'] ) {
		$tag['tag'] = 'span';
		$tag['attr']['data-lightbox-maxwidth'] = ARVE\options()['lightbox_maxwidth'];

		if ( $a['src'] ) {
			$tag['attr']['data-iframe-src'] = $a['src'];
		}
	}

	if ( in_array( $a['mode'], [ 'lazyload', 'lightbox' ], true ) && ! empty( $a['hover_effect'] ) ) {
		$tag['attr']['class'] .= ' arve-hover-effect-' . $a['hover_effect'];
	}

	if ( in_array( $a['mode'], [ 'lightbox', 'link-lightbox' ], true ) ) {
		$tag['attr']['data-lightbox']          = ARVE\options()['lightbox_script'];
		$tag['attr']['data-lightbox-maxwidth'] = ARVE\options()['lightbox_maxwidth'];

		if ( $a['src'] ) {
			$tag['attr']['data-iframe-src'] = $a['src'];
		}
	}

	if ( 'lazyload' === $a['mode'] && $a['grow'] ) {
		$tag['attr']['data-grow'] = '';
	}

	if ( $a['fullscreen'] ) {
		$tag['attr']['data-fullscreen'] = $a['fullscreen'];
	}

	if ( 100 !== $a['volume'] ) {
		$tag['attr']['data-volume'] = $a['volume'];
	}

	if ( use_jsapi( $a ) ) {
		$tag['attr']['data-jsapi'] = $a['provider'];
	}

	if ( 'link-lightbox' === $a['mode'] ) {
		$tag['attr']['class'] .= ' arve-hidden';
	}

	return $tag;
}

function tag_filter_video( array $tag, array $a ) {

	$tag['attr']['onloadstart'] = 'this.volume=' . ( $a['volume'] / 100 );

	return $tag;
}

function tag_filter_iframe( array $tag, array $a ) {

	if ( 'youtube' === $a['provider'] && use_jsapi( $a ) ) {
		$tag['attr']['src'] = add_query_arg( [ 'enablejsapi' => 1 ], $tag['attr']['src'] );
	}

	if ( $a['disable_links'] && ! empty( $tag['attr']['sandbox'] ) ) {

		$sandbox_arr = \explode( ' ', $tag['attr']['sandbox'] );
		$sandbox_arr = \array_diff( $sandbox_arr, [ 'allow-popups', 'allow-popups-to-escape-sandbox' ] );

		$tag['attr']['sandbox'] = \implode( ' ', $sandbox_arr );
	}

	if ( 'normal' === $a['mode'] ) {
		$tag['attr']['loading'] = 'lazy';
	}

	return $tag;
}

function tag_filter_title( array $tag, array $a ) {

	if ( ! $a['hide_title'] && in_array( $a['mode'], [ 'lazyload', 'lightbox' ], true ) ) {
		$tag['tag']           = 'h5';
		$tag['inner_html']    = trim( $a['title'] );
		$tag['attr']['class'] = 'arve-title';
		unset( $tag['attr']['content'] );
	}

	return $tag;
}

function tag_filter_thumbnail( array $tag, array $a ) {

	if ( in_array( $a['mode'], [ 'lazyload', 'lightbox' ], true ) ) {

		if ( is_numeric( $a['thumbnail'] )  ) {

			$image = wp_get_attachment_image_src( $a['thumbnail'], 'full' );
 
			if ( $image ) {
				list( $src, $width, $height ) = $image;

				$height = ARVE\new_height(
					$width,
					$height,
					$a['maxwidth']
				);
			}
		} elseif ( ! empty( $a['oembed_data']->thumbnail_width ) &&
			! empty( $a['oembed_data']->thumbnail_height )
		) {
			$height = ARVE\new_height(
				$a['oembed_data']->thumbnail_width,
				$a['oembed_data']->thumbnail_height,
				$a['maxwidth']
			);
		} else {
			$ratio  = empty( $a['aspect_ratio'] ) ? '16:9' : $a['aspect_ratio'];
			$height = ARVE\new_height_from_aspect_ratio( $a['maxwidth'], $ratio );
		}

		$tag['tag']             = 'img';
		$tag['attr']['alt']     = trim( $a['title'] );
		$tag['attr']['src']     = $a['img_src'];
		$tag['attr']['srcset']  = $a['img_srcset'];
		$tag['attr']['class']   = 'arve-thumbnail';
		$tag['attr']['loading'] = 'lazy';
		$tag['attr']['width']   = $a['maxwidth'];
		$tag['attr']['height']  = $height;

		unset( $tag['attr']['content'] );
	}

	return $tag;
}

function tag_filter_button( array $tag, array $a) {

	if ( ! in_array( $a['mode'], [ 'lazyload', 'lightbox' ], true ) ) {
		return $tag;
	}

	$svg      = '';
	$svg_file = PLUGIN_DIR . "/svg/{$a['play_icon_style']}.svg";

	if ( 'custom' === $a['play_icon_style'] ) {
		$svg = apply_filters( 'nextgenthemes/arve/pro/play_svg', $svg );
	} elseif ( is_file( $svg_file ) ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents 
		$svg = \file_get_contents( $svg_file );
	}

	$svg = \str_replace(
		'<svg',
		sprintf( '<svg class="%s" focusable="false" role="img"', esc_attr( 'arve-play-svg arve-play-svg--' . $a['play_icon_style'] ) ),
		$svg
	);

	if ( 'vimeo' === $a['play_icon_style'] ) {
		$svg = '<div class="arve-play-btn-inner">' . $svg . '</div>';
	}

	$tag['tag']                 = 'button';
	$tag['inner_html']          = $svg;
	$tag['role']                = 'presentation';
	$tag['attr']['type']        = 'button';
	$tag['attr']['class']       = 'arve-play-btn arve-play-btn--' . $a['play_icon_style'];
	$tag['attr']['data-target'] = '#' . $a['uid'];
	$tag['attr']['aria-label']  = 'Play';

	return $tag;
}
