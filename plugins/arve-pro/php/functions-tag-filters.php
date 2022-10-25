<?php
namespace Nextgenthemes\ARVE\Pro;

use \Nextgenthemes\ARVE;

function noscript_wrap( $html, array $a ) {
	if ( in_array( $a['mode'], [ 'lazyload', 'lightbox', 'link-lightbox' ], true ) ) {
		$html = '<noscript class="arve-noscript">' . $html . '</noscript>';
	}
	return $html;
}

function tag_filter_arve( array $tag, array $a ) {

	$reset = ARVE\options()['reset_after_played'];

	switch ( $reset ) {
		default:
		case 'enabled':
			$reset = true;
			break;
		case 'disabled':
			$reset = false;
			break;
		case 'disabled-for-vimeo':
			if ( 'vimeo' === $a['provider'] ) {
				$reset = false;
			}
			break;
	}

	if ( $reset ) {
		$tag['attr']['data-reset-after-played'] = '';
	}

	if ( 'link-lightbox' === $a['mode'] ) {
		$tag['tag'] = 'span';
	}

	if ( in_array( $a['mode'], [ 'lazyload', 'lightbox' ], true ) && ! empty( $a['hover_effect'] ) ) {
		$tag['attr']['class'] .= ' arve-hover-effect-' . $a['hover_effect'];
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

	if ( 'link-lightbox' === $a['mode'] ) {
		$tag['attr']['hidden'] = '';
	}

	return $tag;
}

function tag_filter_video( array $tag, array $a ) {

	$tag['attr']['onloadstart'] = 'this.volume=' . ( $a['volume'] / 100 );

	return $tag;
}

function tag_filter_iframe( array $tag, array $a ) {

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

	if ( ! $a['hide_title'] && in_array( $a['mode'], [ 'lazyload', 'lightbox' ], true ) && $a['title'] ) {
		$tag['tag']           = 'h5';
		$tag['inner_html']    = trim( $a['title'] );
		$tag['attr']['class'] = 'arve-title';
	}

	return $tag;
}

function srcset( array $a ) {

	if ( empty( $a['img_src'] ) ) {
		return false;
	}

	$srcset = false;

	if ( is_numeric( $a['thumbnail'] ) ) {
		$srcset = wp_get_attachment_image_srcset( $a['thumbnail'], 'small' );
	} elseif ( ! empty( $a['oembed_data']->arve_srcset ) ) {
		$srcset = $a['oembed_data']->arve_srcset;
	}

	if ( 'vimeo' === $a['provider'] && str_contains( $a['img_src'], 'i.vimeocdn.com' ) ) {

		foreach ( SRCSET_SIZES as $size ) :

			$url = preg_replace( '#^(.*)_([0-9x]{3,9}(\.jpg)?)$#i', "$1_$size", $a['img_src'] );

			$srcset_comb[] = "$url {$size}w";

		endforeach;

		$srcset = implode( ', ', $srcset_comb );
	}

	$thumb_id = get_post_thumbnail_id();
	$options  = ARVE\options();

	if ( empty( $srcset ) && $options['thumbnail_post_image_fallback'] && $thumb_id ) {
		$srcset = wp_get_attachment_image_srcset( $$thumb_id, 'small' );
	}

	return $srcset;
}

function tag_filter_thumbnail( array $tag, array $a ) {

	if ( ! in_array( $a['mode'], [ 'lazyload', 'lightbox' ], true ) || empty( $a['img_src'] ) ) {
		return $tag;
	}

	$width = 480;

	if ( is_numeric( $a['thumbnail'] ) ) {

		$image = wp_get_attachment_image_src( $a['thumbnail'], 'full' );

		if ( $image ) {
			list( , $att_width, $att_height ) = $image;

			$height = ARVE\new_height(
				$att_width,
				$att_height,
				$width
			);
		}
	} elseif ( ! empty( $a['oembed_data']->thumbnail_width ) &&
		! empty( $a['oembed_data']->thumbnail_height )
	) {
		$height = ARVE\new_height(
			$a['oembed_data']->thumbnail_width,
			$a['oembed_data']->thumbnail_height,
			$width
		);
	} else {
		$ratio  = empty( $a['aspect_ratio'] ) ? '16:9' : $a['aspect_ratio'];
		$height = ARVE\height_from_width_and_ratio( $width, $ratio );
	}

	$tag['tag']             = 'img';
	$tag['attr']['alt']     = trim( $a['title'] );
	$tag['attr']['src']     = $a['img_src'];
	$tag['attr']['srcset']  = srcset( $a );
	$tag['attr']['class']   = 'arve-thumbnail';
	$tag['attr']['loading'] = 'lazy';
	$tag['attr']['width']   = $width;
	$tag['attr']['height']  = $height;

	return $tag;
}

function tag_filter_button( array $tag, array $a ) {

	if ( ! in_array( $a['mode'], [ 'lazyload', 'lightbox' ], true ) ) {
		return $tag;
	}

	if (
		'html5' === $a['provider'] &&
		empty( $a['img_src'] )
	) {
		$a['play_icon_style'] = 'none';
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
		sprintf(
			'<svg class="%s" focusable="false" role="img"',
			esc_attr( 'arve-play-svg arve-play-svg--' . $a['play_icon_style'] )
		),
		$svg
	);

	if ( 'vimeo' === $a['play_icon_style'] ) {
		$svg = '<span class="arve-play-btn-inner">' . $svg . '</span>';
	}

	if ( 'lightbox' === $a['mode'] ) {
		$tag['attr'] = bigger_picture_attr($a);
	}

	$tag['tag']                 = 'button';
	$tag['inner_html']          = $svg;
	$tag['role']                = 'presentation';
	$tag['attr']['type']        = 'button';
	$tag['attr']['class']       = 'arve-play-btn arve-play-btn--' . $a['play_icon_style'];
	$tag['attr']['data-target'] = '#' . $a['uid'];
	$tag['attr']['aria-label']  = __( 'Play', 'arve-pro' );

	return $tag;
}

function bigger_picture_attr( array $a ) {

	$ratio = $a['aspect_ratio'] ? $a['aspect_ratio'] : '16:9';

	if ( 'html5' === $a['provider'] ) {
		$attr['href']         = $a['video_sources'][0]['src'];
		$attr['data-sources'] = wp_json_encode( $a['video_sources'], 0, 2);
		$attr['data-tracks']  = wp_json_encode( $a['tracks'], 0, 2);
	} else {
		$attr['href']        = $a['url'];
		$attr['data-iframe'] = ARVE\iframe_src_autoplay_args( true, $a );
	}

	$attr['data-target'] = '#' . $a['uid'];
	$attr['data-width']  = ARVE\options()['lightbox_maxwidth'];
	$attr['data-height'] = ARVE\height_from_width_and_ratio( $attr['data-width'], $ratio );
	$attr['data-thumb']  = $a['img_src'];

	$attr['aria-label'] = __( 'Play', 'arve-pro' );

	return $attr;
}
