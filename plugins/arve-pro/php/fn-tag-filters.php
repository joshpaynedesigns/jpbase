<?php declare(strict_types=1);
namespace Nextgenthemes\ARVE\Pro;

use function Nextgenthemes\ARVE\iframesrc_urlarg_autoplay;
use function Nextgenthemes\ARVE\translation;
use function Nextgenthemes\ARVE\options;
use function Nextgenthemes\ARVE\height_from_width_and_ratio;
use function Nextgenthemes\ARVE\new_height;

function noscript_wrap( string $html, array $a ): string {

	if ( 'lazyload' === $a['mode'] ) {
		$html = '<noscript class="arve-noscript">' . $html . '</noscript>';
	}

	return $html;
}

function tag_filter_arve( array $tag, array $a ): array {

	$reset = options()['reset_after_played'];

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
		#$tag['attr']['hidden'] = '';
	}

	return $tag;
}

function tag_filter_video( array $tag, array $a ): array {

	$tag['attr']['onloadstart'] = 'this.volume=' . ( $a['volume'] / 100 );

	return $tag;
}

function tag_filter_title( array $tag, array $a ): array {

	if ( ! $a['hide_title'] && in_array( $a['mode'], [ 'lazyload', 'lightbox' ], true ) && $a['title'] ) {
		$tag['tag']           = 'h5';
		$tag['inner_html']    = trim( $a['title'] );
		$tag['attr']['class'] = 'arve-title';
	}

	return $tag;
}

/**
 * @return mixed
 */
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

	$thumb_id = \get_post_thumbnail_id();
	$options  = options();

	if ( empty( $srcset ) && $options['thumbnail_post_image_fallback'] && $thumb_id ) {
		$srcset = wp_get_attachment_image_srcset( $$thumb_id, 'small' );
	}

	return $srcset;
}

/**
 * Function for filtering tags and generating thumbnails.
 *
 * @param array $tag The tag array to be filtered.
 * @param array $a The array containing mode, img source, thumbnail, oembed data, and aspect ratio.
 * @return array The filtered tag array.
 */
function tag_filter_thumbnail( array $tag, array $a ): array {

	if ( ! in_array( $a['mode'], [ 'lazyload', 'lightbox' ], true ) || empty( $a['img_src'] ) ) {
		return $tag;
	}

	$width = 480;

	if ( is_numeric( $a['thumbnail'] ) ) {

		$image = wp_get_attachment_image_src( $a['thumbnail'], 'full' );

		if ( $image ) {
			list( , $att_width, $att_height ) = $image;

			$height = new_height(
				$att_width,
				$att_height,
				$width
			);
		}
	} elseif ( ! empty( $a['oembed_data']->thumbnail_width ) &&
		! empty( $a['oembed_data']->thumbnail_height )
	) {
		$height = new_height(
			(float) $a['oembed_data']->thumbnail_width,
			(float) $a['oembed_data']->thumbnail_height,
			$width
		);
	} else {
		$ratio  = empty( $a['aspect_ratio'] ) ? '16:9' : $a['aspect_ratio'];
		$height = height_from_width_and_ratio( $width, $ratio );
	}

	$tag['tag']             = 'img';
	$tag['attr']['alt']     = trim( $a['title'] );
	$tag['attr']['src']     = $a['img_src'];
	$tag['attr']['srcset']  = srcset( $a );
	$tag['attr']['class']   = 'arve-thumbnail';
	$tag['attr']['width']   = $width;
	$tag['attr']['height']  = $height;
	$tag['attr']['loading'] = 'lazy';

	return $tag;
}

function tag_filter_button( array $tag, array $a ): array {

	if ( ! in_array( $a['mode'], array( 'lazyload', 'lightbox' ), true ) ) {
		return $tag;
	}

	// TODO: check if this is cool on all devices.
	// if (
	//  'html5' === $a['provider'] &&
	//  empty( $a['img_src'] )
	// ) {
	//  $a['play_icon_style'] = 'none';
	// }

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
			'<svg class="%s" focusable="false" aria-hidden="true"',
			esc_attr( 'arve-play-svg arve-play-svg--' . $a['play_icon_style'] )
		),
		$svg
	);

	if ( 'vimeo' === $a['play_icon_style'] ) {
		$svg = '<span class="arve-play-btn-inner">' . $svg . '</span>';
	}

	$tag['tag']                 = 'button';
	$tag['inner_html']          = $svg;
	$tag['attr']['role']        = 'button';
	$tag['attr']['type']        = 'button';
	$tag['attr']['class']       = 'arve-play-btn arve-play-btn--' . $a['play_icon_style'];
	$tag['attr']['data-target'] = '#' . $a['uid'];
	$tag['attr']['aria-label']  = sprintf(
		translation('play_video_%'),
		$a['title']
	);

	if ( 'lightbox' === $a['mode'] ) {
		$tag['attr'] = bigger_picture_attr( $a, $tag['attr'] );
	}

	return $tag;
}

function bigger_picture_attr( array $a, array $attr = array() ): array {

	if ( ! empty( $a['lightbox_aspect_ratio'] ) ) {
		$ratio = $a['lightbox_aspect_ratio'];
	} else {
		$ratio = $a['aspect_ratio'] ?? '16:9';
	}

	$attr['data-target']   = '#' . $a['uid'];
	$attr['data-width']    = options()['lightbox_maxwidth'];
	$attr['data-height']   = height_from_width_and_ratio( $attr['data-width'], $ratio );
	$attr['data-thumb']    = $a['img_src'];
	$attr['aria-haspopup'] = 'dialog';
	$attr['aria-label']    = sprintf(
		translation('play_video_%'),
		$a['title']
	);

	if ( 'html5' === $a['provider'] ) {

		unset($a['video_attr']['width']);
		unset($a['video_attr']['muted']);
		unset($a['video_attr']['autoplay']);
		$a['video_attr']['class'] = str_replace( 'arve-video', '', $a['video_attr']['class'] );

		$attr['data-attr']    = array_filter( $a['video_attr'], __NAMESPACE__ . '\is_not_false_and_not_null' );
		$attr['href']         = $a['video_sources'][0]['src'];
		$attr['data-sources'] = \wp_json_encode( $a['video_sources'], 0, 2);
		$attr['data-tracks']  = \wp_json_encode( $a['tracks'], 0, 2);
	} else {

		unset( $a['iframe_attr']['src'] ); // bigger picture use data-iframe instead
		$a['iframe_attr']['class'] = str_replace( 'arve-iframe', '', $a['iframe_attr']['class'] );

		$attr['data-attr']   = array_filter( $a['iframe_attr'], __NAMESPACE__ . '\is_not_false_and_not_null' );
		$attr['data-iframe'] = iframesrc_urlarg_autoplay( $a['src'], $a['provider'], true );
		$attr['href']        = $a['url'];
	}

	return $attr;
}

/**
 * @param mixed $value The value to check
 */
function is_not_false_and_not_null( $value ): bool {
	return false !== $value && null !== $value;
}
