<?php
namespace Nextgenthemes\ARVE\Pro;

use \Nextgenthemes\ARVE;
use \Nextgenthemes\ARVE\Common;

function sc_filter_upload_date( array $a ) {

	if ( ARVE\has_fatal_error( $a ) ) {
		return $a;
	}

	if ( empty( $a['upload_date'] ) && ! empty( $a['oembed_data']->upload_date ) ) {
		$date             = strtotime( $a['oembed_data']->upload_date );
		$a['upload_date'] = gmdate( 'c', $time );
	}

	return $a;
}

function sc_filter_description( array $a ) {

	if ( empty( $a['description'] ) && ! empty( $a['oembed_data']->description ) ) {
		$a['description'] = $a['oembed_data']->description;
	}

	return $a;
}

function sc_filter_mode( array $a ) {

	if ( ARVE\has_fatal_error( $a ) ) {
		return $a;
	}

	// if ( 'lazyload' === $a['mode'] && ARVE\options()['lazyload_mode_fallback'] && ! use_jsapi( $a ) ) {
	// 	$a['mode'] = 'normal';
	// }

	if (
		'html5' === $a['provider'] &&
		in_array( $a['mode'], array( 'lazyload', 'lightbox' ), true ) &&
		empty( $a['img_src'] )
	) {
		$a['play_icon_style'] = 'none';
	}

	return $a;
}

function sc_filter_autoplay( array $a ) {

	if ( ARVE\has_fatal_error( $a ) ) {
		return $a;
	}

	if ( in_array(
		$a['mode'],
		[
			'lazyload',
			'lightbox',
			'link-lightbox',
		],
		true
	) ) {
		$a['autoplay'] = true;
	}

	return $a;
}

function early_sc_filter_latest_channel_video( array $a ) {

	if ( empty( $a['url'] ) ) {
		return $a;
	}

	$prefix = 'https://www.youtube.com/channel/';

	if ( ! Common\starts_with( $a['url'], $prefix ) ) {
		return $a;
	}

	$re = '@https://www\.youtube\.com/channel/(?<channel_id>[a-z0-9_-]+)@mi';

	preg_match( $re, $a['url'], $matches, PREG_OFFSET_CAPTURE, 0 );

	$transient_name = 'arve_pro_latest_from_channel_' . $matches['channel_id'];
	$a['url']       = get_transient( $transient_name );

	if ( false === $a['url'] ) {

		$response = Common\remote_get_body( 'https://www.youtube.com/feeds/videos.xml?channel_id=' . $channel_id );

		if ( is_wp_error( $response ) ) {
			return $a;
		}

		$xml = simplexml_load_string( $body );

		if ( false === $xml || empty( $xml->entry[0]->children( 'yt', true )->videoId[0] ) ) {

			$a['errors']->add(
				'video not detected',
				sprintf(
					// Translators: URL.
					__( 'Latest video from <a href="%s">channel</a> could not be detected: ', 'arve-pro' ),
					esc_url( $channel_url )
				)
			);

		} else {

			$a['url'] = 'https://youtube.com/watch?v=' . (string) $xml->entry[0]->children( 'yt', true )->videoId[0];
			set_transient( $transient_name, $a['url'], HOUR_IN_SECONDS );
		}
	}

	return $a;
}

function sc_filter_validate( array $a ) {

	if ( ARVE\has_fatal_error( $a ) ) {
		return $a;
	}

	if ( ! Common\has_valid_key( 'arve_pro' ) ) {

		$a['errors']->add(
			'fatal',
			sprintf(
				// Translators: URL.
				__( '<a href="%s">ARVE Pro</a> License not activated or valid', 'arve-pro' ),
				esc_url( 'https://nextgenthemes.com/plugins/arve/documentation/installing-and-license-management/' )
			)
		);
	}

	return $a;
}

function sc_filter_extra_data( array $a ) {

	if ( ARVE\has_fatal_error( $a ) ) {
		return $a;
	}

	$cur_post = get_post();
	$oembeds  = [
		#  $a         -  oembed
		'title'       => 'title',
		'description' => 'description',
		'author_name' => 'author_name',
		'duration'    => 'duration',
		'thumbnail'   => 'thumbnail_url',
	];

	foreach ( $oembeds as $key => $oembed_var ) {

		if ( empty( $a[ $key ] ) && ! empty( $a['oembed_data']->$oembed_var ) ) {
			$a[ $key ] = (string) $a['oembed_data']->$oembed_var;
		}
	}

	if ( empty( $a['title'] ) && ! empty( get_the_title() ) ) {
		$a['title'] = get_the_title();
	}
	if ( empty( $a['title'] ) ) {
		$a['title'] = trim( get_bloginfo( 'name' ) );
	}

	if ( empty( $a['description'] ) && ! empty( $cur_post->post_content ) ) {
		$a['description'] = trim( wp_html_excerpt( strip_shortcodes( $cur_post->post_content ), 300 ) );
	}
	if ( empty( $a['description'] ) ) {
		$a['description'] = sprintf( '%s - %s video', $a['title'], $a['provider'] );
	}

	if ( empty( $a['upload_date'] ) && ! empty( $cur_post->post_date ) ) {
		$a['upload_date'] = gmdate( 'c', strtotime( $cur_post->post_date ) );
	}
	if ( empty( $a['upload_date'] ) ) {
		$a['upload_date'] = gmdate( 'c' );
	}

	return $a;
}

function get_json_thumbnail( $a, $url, $remote_get_args, $json_name ) {

	if ( ARVE\has_fatal_error( $a ) ) {
		return $a;
	}

	$thumb = Common\remote_get_json( $url, $remote_get_args, $json_name );

	if ( is_wp_error( $thumb ) ) {
		$a['errors']->add( 'thumb-api-call', $thumb->get_error_message() );
	} else {
		$a['img_src'] = $thumb;
	}

	return $a;
}

function sc_filter_thumbnail( array $a ) {

	if ( ARVE\has_fatal_error( $a ) ) {
		return $a;
	}

	$thumb_id = get_post_thumbnail_id();

	if ( 'featured' === $a['thumbnail'] && $thumb_id ) {
		$a['thumbnail'] = $thumb_id;
	}

	return $a;
}

function sc_filter_img_src( array $a ) {

	if ( ARVE\has_fatal_error( $a ) ) {
		return $a;
	}

	if ( ! empty( $a['img_src'] ) ) {
		return $a;
	}

	$id       = $a['id'];
	$provider = $a['provider'];
	$thumb_id = get_post_thumbnail_id();
	$options  = ARVE\options();

	switch ( $a['provider'] ) {
		case 'alugha':
			$a = get_json_thumbnail(
				$a,
				"https://api.alugha.com/v1/videos/$id",
				[],
				'thumb'
			);
			break;
		case 'liveleak':
			$html = Common\remote_get_body( "http://www.liveleak.com/view?$id" );

			if ( is_wp_error( $html ) ) {
				$a['errors']->add( 'thumb-api-call', $html->get_error_message() );
			} else {
				preg_match( '#<meta property="og:image" content="([^"]+)#i', $html, $matches );

				if ( ! empty( $matches[1] ) &&
					Common\starts_with( $matches[1], 'http' ) &&
					! Common\ends_with( $matches[1], 'logo.gif' )
				) {
					$a['img_src'] = $matches[1];
				}
			}
			break;
		case 'facebook':
			$fb_vidid = false;

			if ( $a['url'] ) {
				preg_match( '~/videos/(?:[a-z]+\.[0-9]+/)?([0-9]+)~i', $a['url'], $matches );

				if ( ! empty( $matches[1] ) ) {
					$fb_vidid = $matches[1];
				}
			} elseif ( $a['id'] ) {
				$fb_vidid = $a['id'];
			}

			$data = Common\remote_get_json( "https://graph.facebook.com/{$fb_vidid}/picture?redirect=false", [], 'data' );

			if ( is_wp_error( $data ) ) {
				$a['errors']->add( 'thumb-api-call', $data->get_error_message() );
			} elseif ( ! empty( $data->url ) ) {
				$a['img_src'] = $data->url;
			} else {
				$a['errors']->add( 'thumb-api-call', 'data->url is empty' );
			}

			break;
	}

	if ( empty( $a['img_src'] ) && $options['thumbnail_post_image_fallback'] && $thumb_id ) {
		$a['img_src']    = wp_get_attachment_image_url( $thumb_id, 'small' );
		$a['img_srcset'] = wp_get_attachment_image_srcset( $$thumb_id, 'small' );
	}

	if ( empty( $a['img_src'] ) ) {
		$a['img_src'] = $options['thumbnail_fallback'];
	}

	return $a;
}

function get_image_size( $img_url ) {
	#$response = wp_remote_get( $img_url );
	$response = Common\remote_get_body( $img_url, [ 'timeout' => 2 ] );

	if ( is_wp_error( $response ) ) {
		return false;
	}

	return getimagesizefromstring( $response );
}

function sc_filter_img_srcset( array $a ) {

	if ( ARVE\has_fatal_error( $a ) ) {
		return $a;
	}

	if ( ! empty( $a['img_srcset'] )
		|| empty( $a['img_src'] )
		|| ! in_array( $a['mode'], array( 'lazyload', 'lightbox' ), true )
		|| ! function_exists( 'getimagesizefromstring' )
	) {
		return $a;
	}

	$srcset = array();

	if ( 'youtube' === $a['provider'] && Common\contains( $a['img_src'], 'i.ytimg.com' ) ) {

		$mq     = "https://i.ytimg.com/vi/{$a['id']}/mqdefault.jpg";     // 320x180
		$hq     = "https://i.ytimg.com/vi/{$a['id']}/hqdefault.jpg";     // 480x360
		$sd     = "https://i.ytimg.com/vi/{$a['id']}/sddefault.jpg";     // 640x480
		$maxres = "https://i.ytimg.com/vi/{$a['id']}/maxresdefault.jpg"; // hd, fullhd ...

		$size_mq     = get_image_size( $mq );
		$size_hq     = get_image_size( $hq );
		$size_sd     = get_image_size( $sd );
		$size_maxres = get_image_size( $maxres );

		// phpcs:disable WordPress.PHP.StrictComparisons.LooseComparison
		if ( $size_mq && 320 == $size_mq[0] ) {
			$srcset[320] = $mq;
		}
		if ( $size_hq && 480 == $size_hq[0] ) {
			$srcset[480] = $hq;
		}
		if ( $size_sd && 640 == $size_sd[0] ) {
			$srcset[640] = $sd;
		}
		if ( $size_maxres && $size_maxres[0] >= 1280 ) {
			$srcset[ $size_maxres[0] ] = $maxres;
		}
		// phpcs:enable WordPress.PHP.StrictComparisons.LooseComparison

	} elseif ( 'vimeo' === $a['provider'] && Common\contains( $a['img_src'], 'i.vimeocdn.com' ) ) {

		foreach ( SRCSET_SIZES as $size ) :

			$url = preg_replace( '#^(.*)_([0-9x]+).jpg$#i', "$1_$size.jpg", $a['img_src'] );

			$srcset[ $size ] = $url;

		endforeach;

		unset( $size );
	}

	if ( ! empty( $srcset ) ) {

		foreach ( $srcset as $size => $url ) {
			$srcset_comb[] = "$url {$size}w";
		}

		$a['img_srcset'] = implode( ', ', $srcset_comb );
	}

	foreach ( SRCSET_SIZES as $size ) {

		if ( ! empty( $srcset[ $size ] ) ) {
			$a['img_src'] = $srcset[ $size ];
			break;
		}
	}

	return $a;
}
