<?php
namespace Nextgenthemes\ARVE\Pro;

use \Nextgenthemes\ARVE;
use \Nextgenthemes\ARVE\Common;

function arg_filter_autoplay( $autoplay, array $a ) {

	if ( in_array(
		$a['mode'],
		[
			'lazyload',
			'lightbox',
			'link-lightbox',
		],
		true
	) ) {
		$autoplay = true;
	}

	return $autoplay;
}

function early_sc_filter_latest_channel_video( array $a ) {

	if ( empty( $a['url'] ) ) {
		return $a;
	}

	$prefix = 'https://www.youtube.com/channel/';

	if ( ! str_starts_with( $a['url'], $prefix ) ) {
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

function shortcode_atts_extra_data( array $a ) {

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

	$thumb = Common\remote_get_json( $url, $remote_get_args, $json_name );

	if ( is_wp_error( $thumb ) ) {
		$a['errors']->add( 'thumb-api-call', $thumb->get_error_message() );
	} else {
		$a['img_src'] = $thumb;
	}

	return $a;
}

function arg_filter_thumbnail( $thumbnail, array $a ) {

	$thumb_id = get_post_thumbnail_id();

	if ( 'featured' === $thumbnail && $thumb_id ) {
		$thumbnail = $thumb_id;
	}

	return $thumbnail;
}

function arg_filter_img_src( $img_src, array $a ) {

	if ( ! empty( $img_src ) ) {
		return $img_src;
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
					str_starts_with( $matches[1], 'http' ) &&
					! str_ends_with( $matches[1], 'logo.gif' )
				) {
					$img_src = $matches[1];
				}
			}
			break;
	}

	if ( empty( $img_src ) && $options['thumbnail_post_image_fallback'] && $thumb_id ) {
		$img_src = wp_get_attachment_image_url( $thumb_id, 'small' );
	}

	if ( empty( $img_src ) ) {
		$img_src = $options['thumbnail_fallback'];
	}

	return $img_src;
}

function get_image_size( $img_url ) {
	$response = Common\remote_get_body( $img_url, [ 'timeout' => 2 ] );

	if ( is_wp_error( $response ) ) {
		return false;
	}

	return getimagesizefromstring( $response );
}

function arg_filter_img_srcset( $img_srcset, array $a ) {

	$thumb_id = get_post_thumbnail_id();
	$options  = ARVE\options();

	if ( empty( $img_srcset ) && $options['thumbnail_post_image_fallback'] && $thumb_id ) {
		$img_srcset = wp_get_attachment_image_srcset( $$thumb_id, 'small' );
	}

	if ( ! empty( $img_srcset )
		|| empty( $a['img_src'] )
		|| ! in_array( $a['mode'], array( 'lazyload', 'lightbox' ), true )
		|| ! function_exists( 'getimagesizefromstring' )
	) {
		return $img_srcset;
	}

	$srcset = array();

	if ( 'youtube' === $a['provider'] && str_contains( $a['img_src'], 'i.ytimg.com' ) ) {

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

	} elseif ( 'vimeo' === $a['provider'] && str_contains( $a['img_src'], 'i.vimeocdn.com' ) ) {

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

		$img_srcset = implode( ', ', $srcset_comb );
	}

	foreach ( SRCSET_SIZES as $size ) {

		if ( ! empty( $srcset[ $size ] ) ) {
			$a['img_src'] = $srcset[ $size ];
			break;
		}
	}

	return $img_srcset;
}
