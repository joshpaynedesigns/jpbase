<?php

declare(strict_types = 1);

namespace Nextgenthemes\ARVE\Pro;

use WP_HTML_Tag_Processor;

use function Nextgenthemes\ARVE\arve_errors;
use function Nextgenthemes\ARVE\options;
use function Nextgenthemes\ARVE\translation;
use function Nextgenthemes\ARVE\height_from_width_and_ratio;
use function Nextgenthemes\ARVE\iframesrc_urlarg_autoplay;
use function Nextgenthemes\ARVE\is_card;
use function Nextgenthemes\WP\apply_attr;
use function Nextgenthemes\WP\first_tag_attr;
use function Nextgenthemes\WP\remote_get_head;
use function Nextgenthemes\WP\replace_links;

function process_tags( WP_HTML_Tag_Processor $p, array $a ): WP_HTML_Tag_Processor {

	$p->seek( 'arve' );

	arve_attr( $p, $a );

	if ( 'link-lightbox' !== $a['mode'] ) {

		if ( ! $p->next_tag( [ 'class_name' => 'arve-inner' ] ) ) {
			wp_trigger_error( __FUNCTION__, 'WP_HTML_Tag_Processor::next_tag() failed to find .arve-inner tag' );
			return $p;
		}

		inner_attr( $p, $a );
	}

	return $p;
}

/**
 * Add attributes to the outermost `.arve` element.
 *
 * @param WP_HTML_Tag_Processor $p   The tag processor.
 * @param array                 $a   The attributes.
 *
 * @return WP_HTML_Tag_Processor The tag processor.
 */
function arve_attr( WP_HTML_Tag_Processor $p, array $a ): WP_HTML_Tag_Processor {

	$reset   = options()['reset_after_played'];
	$ll_mode = in_array( $a['mode'], [ 'lazyload', 'lightbox' ], true );

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

	if ( is_card( $a ) ) {
		$p->add_class( 'arve--card' );
	}

	if ( $ll_mode && ! empty( $a['hover_effect'] ) ) {
		$p->add_class( 'arve-hover-effect-' . $a['hover_effect'] );
	}

	if ( $reset ) {
		$p->set_attribute( 'data-reset-after-played', true );
	}

	if ( 'lazyload' === $a['mode'] && $a['grow'] ) {
		$p->set_attribute( 'data-grow', true );
	}

	if ( $a['fullscreen'] ) {
		$p->set_attribute( 'data-fullscreen', $a['fullscreen'] );
	}

	if ( 100 !== $a['volume'] ) {
		$p->set_attribute( 'data-volume', $a['volume'] );
	}

	return $p;
}

function inner_attr( WP_HTML_Tag_Processor $p, array $a ): WP_HTML_Tag_Processor {

	if ( is_card( $a ) ) {

		$inner_attr = array(
			'role'        => 'button',
			'type'        => 'button',
			'data-target' => '#' . $a['uid'],
			'aria-label'  => sprintf( translation( 'play_video_%' ), $a['title'] ),
		);

		if ( 'lightbox' === $a['mode'] ) {
			$inner_attr = bigger_picture_attr( $a, $inner_attr );
		}

		apply_attr( $p, $inner_attr );
	}

	return $p;
}

function bigger_picture_attr( array $a, array $attr = array() ): array {

	if ( ! empty( $a['lightbox_aspect_ratio'] ) ) {
		$ratio = $a['lightbox_aspect_ratio'];
	} else {
		$ratio = $a['aspect_ratio'] ?? '16:9';
	}

	$width    = options()['lightbox_maxwidth'];
	$bp_attrs = [
		'data-target'   => '#' . $a['uid'],
		'data-width'    => $width,
		'data-height'   => height_from_width_and_ratio( $width, $ratio ),
		'data-thumb'    => $a['img_src'],
		'aria-haspopup' => 'dialog',
		'aria-label'    => sprintf(
			translation( 'play_video_%' ),
			$a['title']
		),
	];

	$attr = array_merge( $attr, $bp_attrs );

	if ( 'html5' === $a['provider'] ) {
		$attr['data-attr']    = big_picture_data_attr( $a['video_attr'], $a['provider'] );
		$attr['href']         = $a['video_sources'][0]['src'];
		$attr['data-sources'] = wp_json_encode( $a['video_sources'], 0, 2 );
		$attr['data-tracks']  = wp_json_encode( $a['tracks'], 0, 2 );
	} else {
		$attr['data-attr']   = big_picture_data_attr( $a['iframe_attr'], $a['provider'] );
		$attr['data-iframe'] = iframesrc_urlarg_autoplay( $a['src'], $a['provider'], true );
		$attr['href']        = $a['url'];
	}

	return $attr;
}

function big_picture_data_attr( array $attr, string $provider ): string {

	$attr = array_filter(
		$attr,
		function ( $value ): bool {

			if ( null !== $value && ! is_scalar( $value ) ) {
				wp_trigger_error( __FUNCTION__, 'Value should be scalar or null' );
			}

			return false !== $value && null !== $value;
		}
	);

	if ( 'html5' === $provider ) {
		unset( $attr['width'] );
		unset( $attr['muted'] );
		unset( $attr['autoplay'] );
		$attr['class'] = str_replace( 'arve-video', '', $attr['class'] );
	} else {
		unset( $attr['src'] ); // bigger picture use data-iframe instead
		$attr['class'] = str_replace( 'arve-iframe', '', $attr['class'] );
	}

	return wp_json_encode( $attr, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP );
}

function inner_html( array $a ): string {

	$ll_mode = in_array( $a['mode'], [ 'lazyload', 'lightbox' ], true );

	if ( ! $ll_mode ) {
		return '';
	}

	$lb   = PHP_EOL . "\t\t\t";
	$html = thumbnail_html( $a ) . $lb;

	if ( ! is_card( $a ) && ! $a['hide_title'] && $ll_mode && $a['title'] ) {

		$html .= sprintf(
			'<div class="arve-title">%s</div>' . $lb,
			esc_html( trim( $a['title'] ) )
		);
	}

	if ( $ll_mode ) {
		$html .= button_html( $a ) . $lb;
	}

	if ( ! is_card( $a ) && function_exists( '\Nextgenthemes\ARVE\Privacy\consent_html' ) ) {
		$html .= \Nextgenthemes\ARVE\Privacy\consent_html( $a );
	}

	return $html;
}

function title_html( array $a ): string {

	$html    = '';
	$card    = 'card' === $a['lazyload_style'];
	$ll_mode = in_array( $a['mode'], [ 'lazyload', 'lightbox' ], true );

	if ( ! $card && ! $a['hide_title'] && $ll_mode && $a['title'] ) {

		$html = sprintf(
			'<h5 class="arve-title">%s</h5>',
			esc_html( trim( $a['title'] ) )
		);
	}

	return $html;
}

function card_html( array $a ): string {

	if ( ! is_card( $a ) ) {
		return '';
	}

	$lb   = PHP_EOL . "\t\t";
	$html = sprintf(
		'<div class="arve-card-title">%s</div>' . $lb,
		esc_html( trim( $a['title'] ) )
	);

	$html .= sprintf(
		'<div class="arve-card-description">%s</div>' . $lb,
		#esc_html( trim( str_replace( [ 'https://', 'http://' ], '', $a['description'] ) ) )
		esc_html(
			trim(
				replace_links(
					$a['description'],
					'~' . translation( 'link_removed' ) . '~'
				)
			)
		)
	);

	$html .= sprintf(
		'<div class="arve-card-footer">%s</div>' . $lb,
		esc_html( parse_url( $a['src'], PHP_URL_HOST ) )
	);

	return $html;
}

function thumbnail_html( array $a ): string {

	if ( ! in_array( $a['mode'], [ 'lazyload', 'lightbox' ], true ) || empty( $a['img_src'] ) ) {
		return '';
	}

	$width = 1920;

	if ( is_numeric( $a['thumbnail'] ) ) {

		$image = wp_get_attachment_image_src( $a['thumbnail'], 'full' );

		if ( $image ) {
			list( , $width, $height ) = $image;
		}
	} elseif (
		! empty( $a['oembed_data']->arve_thumbnail_large_width ) &&
		! empty( $a['oembed_data']->arve_thumbnail_large_height )
	) {
		$width  = (float) $a['oembed_data']->arve_thumbnail_large_width;
		$height = (float) $a['oembed_data']->arve_thumbnail_large_height;

	} elseif (
		! empty( $a['oembed_data']->thumbnail_width ) &&
		! empty( $a['oembed_data']->thumbnail_height )
	) {
		$width  = (float) $a['oembed_data']->thumbnail_width;
		$height = (float) $a['oembed_data']->thumbnail_height;

	} else {
		$ratio  = empty( $a['aspect_ratio'] ) ? '16:9' : $a['aspect_ratio'];
		$height = height_from_width_and_ratio( $width, $ratio );
	}

	$attrs = [
		'src'     => $a['img_src'],
		'alt'     => trim( $a['title'] ),
		'srcset'  => srcset( $a ),
		'sizes'   => 'auto',
		'class'   => 'arve-thumbnail',
		'width'   => $width,
		'height'  => $height,
		'loading' => 'lazy',
	];

	return first_tag_attr( '<img>', $attrs );
}

function button_html( array $a ): string {

	$is_ll_mode = in_array( $a['mode'], [ 'lazyload', 'lightbox' ], true );

	if ( ! $is_ll_mode ) {
		return '';
	}

	$attr = [
		'class'       => 'arve-play-btn arve-play-btn--' . $a['play_icon_style'],
		'role'        => 'button',
		'type'        => 'button',
		'data-target' => '#' . $a['uid'],
		'aria-label'  => sprintf(
			translation( 'play_video_%' ),
			$a['title']
		),
	];

	if ( 'lightbox' === $a['mode'] ) {
		$attr = bigger_picture_attr( $a, $attr );
	}

	$tag = is_card( $a ) ? 'div' : 'button';

	return first_tag_attr(
		'<' . $tag . '>' . play_svg( $a['play_icon_style'] ) . '</' . $tag . '>',
		$attr
	);
}

function play_svg( string $style ): string {
	$svg      = '';
	$svg_file = PLUGIN_DIR . "/svg/$style.svg";

	if ( 'custom' === $style ) {
		$svg = apply_filters( 'nextgenthemes/arve/pro/play_svg', $svg );
	} elseif ( is_file( $svg_file ) ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents 
		$svg = file_get_contents( $svg_file );
	}

	$svg = str_replace(
		'<svg',
		sprintf(
			'<svg class="%s" focusable="false" aria-hidden="true"',
			esc_attr( 'arve-play-svg arve-play-svg--' . $style )
		),
		$svg
	);

	if ( 'vimeo' === $style ) {
		$svg = '<span class="arve-play-btn-inner">' . $svg . '</span>';
	}

	$svg = preg_replace( '/[\r\n\t]/', '', $svg );

	return $svg;
}

/**
 * @return string|false
 */
function srcset( array $a ) {

	if ( empty( $a['img_src'] ) ) {
		return false;
	}

	$srcset = false;

	if ( is_numeric( $a['thumbnail'] ) ) {
		$srcset = wp_get_attachment_image_srcset( $a['thumbnail'], 'small' );
	} elseif ( ! empty( $a['oembed_data']->thumbnail_srcset ) ) {
		$srcset = $a['oembed_data']->thumbnail_srcset;
	}

	$thumb_id = get_post_thumbnail_id();
	$options  = options();

	if ( empty( $srcset ) && $options['thumbnail_post_image_fallback'] && $thumb_id ) {
		$srcset = wp_get_attachment_image_srcset( $thumb_id, 'small' );
	}

	return $srcset;
}

function lightbox_link_html( array $a ): string {

	$html = first_tag_attr(
		'<a class="arve-lightbox-link">' . trim( $a['title'] ) . '</a>',
		bigger_picture_attr( $a )
	);

	return $html;
}
