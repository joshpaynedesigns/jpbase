<?php

function arve_pro_sc_filter_attr( $a ) {

	$options = arve_pro_get_options();

	// wrapper.
	if ( in_array( $a['mode'], array( 'lazyload', 'lazyload-lightbox' ), true ) && ! empty( $a['hover_effect'] ) ) {
		$a['wrapper_attr']['class'] .= ' arve-hover-effect-' . $a['hover_effect'];
	}

	if ( 'link-lightbox' === $a['mode'] ) {
		$a['wrapper_attr']['class'] .= ' arve-hidden';
	}

	if ( 'lazyload' === $a['mode'] && $a['grow'] ) {
		$a['wrapper_attr']['data-grow'] = '';
	}

	if ( ! empty( $a['inview_lazyload'] ) ) {
		$a['wrapper_attr']['data-inview-lazyload'] = '';
	}

	if ( $a['volume'] > 0 ) {
		$a['wrapper_attr']['data-volume'] = $a['volume'];

		if ( ! empty( $a['video_attr'] ) ) {
			$a['video_attr']['onloadstart'] = 'this.volume=' . ( $a['volume'] / 100 );
		}
	}

	if ( arve_pro_use_jsapi( $a ) ) {
		$a['wrapper_attr']['data-jsapi'] = '';
	}

	// iframe.
	if ( ! empty( $a['iframe_attr'] ) ) :

		if ( 'youtube' === $a['provider'] && arve_pro_use_jsapi( $a ) ) {

			$parsed_url = wp_parse_url( get_site_url() );

			$a['iframe_attr']['src'] = add_query_arg(
				array(
					'enablejsapi' => 1,
				),
				$a['iframe_attr']['src']
			);
		}

		if ( $a['disable_links'] && ! empty( $a['iframe_attr']['sandbox'] ) ) {
			$a['iframe_attr']['sandbox'] = str_replace( ' allow-popups', '', $a['iframe_attr']['sandbox'] );
		}

	endif;

	return $a;
}

function arve_pro_sc_filter_inview_lazyload( $a ) {

	$options = arve_pro_get_options();

	if (
		(
			arve_pro_is_mobile() || ( ! arve_pro_is_mobile() && empty( $a['img_src'] ) )
		) &&
		$options['inview_lazyload'] &&
		'html5' !== $a['provider'] &&
		'lazyload' === $a['mode'] &&
		! arve_pro_use_jsapi( $a )
	) {
		$a['inview_lazyload'] = true;
	}

	return $a;
}

function arve_pro_sc_filter_autoplay( $atts ) {

	if ( in_array( $atts['mode'], array( 'lazyload', 'lazyload-lightbox', 'link-lightbox' ), true ) ) {
		$atts['autoplay'] = true;
	}

	if ( ! empty( $atts['inview_lazyload'] ) ) {
		$atts['autoplay'] = false;
	}

	return $atts;
}

function arve_pro_sc_filter_latest_channel_video( $atts ) {

	if ( empty( $atts['url'] ) ) {
		return $atts;
	}

	$prefix = 'https://www.youtube.com/channel/';

	if ( ! arve_starts_with( $atts['url'], $prefix ) ) {
		return $atts;
	}

	$channel_url    = $atts['url'];
	$channel_id     = str_replace( $prefix, '', $atts['url'] );
	$transient_name = 'arve_latest_from_channel_' . $channel_id;

	$atts['url'] = get_transient( $transient_name );

	if ( false === $atts['url'] ) {

		$response = wp_remote_get( 'https://www.youtube.com/feeds/videos.xml?channel_id=' . $channel_id, array() );

		if ( is_wp_error( $response ) ) {
			return $atts;
		}

		$body = wp_remote_retrieve_body( $response );

		if ( '' === $body ) {
			return $atts;
		}

		$xml = simplexml_load_string( $body );

		if ( false === $xml || empty( $xml->entry[0]->children( 'yt', true )->videoId[0] ) ) {

			$atts['latest'] = new WP_Error(
				'video not detected',
				sprintf(
					// Translators: URL.
					__( 'Latest video from <a href="%s">channel</a> could not be detected: ', 'arve-pro' ),
					esc_url( $channel_url )
				)
			);

		} else {

			$atts['url'] = 'https://youtube.com/watch?v=' . (string) $xml->entry[0]->children( 'yt', true )->videoId[0];
			set_transient( $transient_name, $atts['url'], HOUR_IN_SECONDS );
		}
	}

	return $atts;
}

function arve_pro_sc_filter_validate( $atts ) {

	$options = arve_pro_get_options();

	if ( ! nextgenthemes_has_valid_key( 'arve_pro' ) ) {

		$atts['error'] = new WP_Error(
			'invalid_license',
			sprintf(
				// Translators: URL.
				__( '<a href="%s">ARVE Pro</a> License not activated or valid', 'arve-pro' ),
				esc_url( 'https://nextgenthemes.com/plugins/advanced-responsive-video-embedder-pro/documentation/installing-and-license-management/' )
			)
		);
	}

	$atts['grow']          = arve_validate_bool( $atts['grow'], 'grow' );
	$atts['hide_title']    = arve_validate_bool( $atts['hide_title'], 'hide_title' );
	$atts['disable_links'] = arve_validate_bool( $atts['disable_links'], 'disable_links' );

	if (
		'html5' === $atts['provider'] &&
		in_array( $atts['mode'], array( 'lazyload', 'lazyload-lightbox' ), true ) &&
		empty( $atts['thumbnail'] )
	) {
		$atts['play_icon_style'] = 'none';
	}

	return $atts;
}

function arve_pro_sc_filter_oembed_img_src_and_title( $a ) {

	if ( ! empty( $a['title'] ) &&
		! empty( $a['img_src'] ) &&
		! empty( $a['description'] )
	) {
		return $a;
	}

	$api_endpoints = array(
		'collegehumor' => array( 'http://www.collegehumor.com/oembed.json?url=', 'http://www.collegehumor.com/video/' ),
		'dailymotion'  => array( 'https://www.dailymotion.com/services/oembed?format=json&url=', 'https://www.dailymotion.com/video/' ),
		'funnyordie'   => array( 'https://www.funnyordie.com/oembed.json?url=', 'https://www.funnyordie.com/videos/' ),
		'ted'          => array( 'https://www.ted.com/talks/oembed.json?url=', 'https://www.ted.com/talks/' ),
		'vimeo'        => array( 'https://vimeo.com/api/oembed.json?width=1920&url=', 'https://vimeo.com/' ),
		'yahoo'        => array( 'https://video.yahoo.com/services/oembed?url=', '' ),
		'youtube'      => array( 'https://www.youtube.com/oembed?format=json&url=', 'https://youtube.com/watch?v=' ),
		'youtubelist'  => array( 'https://www.youtube.com/oembed?format=json&url=', 'https://www.youtube.com/playlist?list=' ),
		'wistia'       => array( 'http://fast.wistia.com/oembed?url=', 'https://fast.wistia.net/embed/iframe/' ),
	);

	if ( empty( $api_endpoints[ $a['provider'] ] ) ) {
		return $a;
	}

	$options        = arve_pro_get_options();
	$transient_name = sprintf( 'arve_%s_%s_oembed', $a['provider'], $a['id'] );

	if ( defined( 'ARVE_DEBUG' ) ) {
		delete_transient( $transient_name );
	}

	$result = get_transient( $transient_name );

	if ( false === $result ) {

		$vimeo_secret = empty( $a['vimeo_secret'] ) ? '' : $a['vimeo_secret'];

		$api_url_base    = $api_endpoints[ $a['provider'] ][0];
		$api_request_url = rawurlencode( $api_endpoints[ $a['provider'] ][1] . $a['id'] . $vimeo_secret );

		$result = arve_pro_json_api_call( $api_url_base . $api_request_url, $a );
	}

	if ( is_wp_error( $result ) ) {

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$a['img_src'] = new WP_Error( 'oembed call', $result->get_error_message() );
		}
	} else {
		set_transient( $transient_name, (object) $result, $options['transient_expire_time'] );

		if ( empty( $a['title'] ) && ! empty( $result->title ) ) {
			$a['title'] = $result->title;
		}

		if ( empty( $a['description'] ) && ! empty( $result->description ) ) {
			$a['description'] = $result->description;
		}

		if ( empty( $a['img_src'] ) && ! empty( $result->thumbnail_url ) ) {
			$a['img_src'] = $result->thumbnail_url;
		}

		if ( ! empty( $a['img_src'] ) && 'dailymotion' === $a['provider'] ) {
			$a['img_src'] = str_replace( 'http://', 'https://', $a['img_src'] );
		}

		if ( empty( $a['img_src'] ) ) {
			$a['img_src'] = (string) $options['thumbnail_fallback'];
		}
	}

	return $a;
}

function arve_pro_sc_filter_default_seo_data( $a ) {

	$cur_post = get_post();

	if ( empty( $a['title'] ) && ! empty( $cur_post->post_title ) ) {
		$a['title'] = trim( $cur_post->post_title );
	}
	if ( empty( $a['title'] ) ) {
		$a['title'] = trim( get_bloginfo( 'name' ) );
	}

	if ( empty( $a['upload_date'] ) && ! empty( $cur_post->post_date ) ) {
		$a['upload_date'] = date( 'c', strtotime( $cur_post->post_date ) );
	}
	if ( empty( $a['upload_date'] ) ) {
		$a['upload_date'] = date( 'c' );
	}

	if ( empty( $a['description'] ) && ! empty( $cur_post->post_content ) ) {
		$a['description'] = trim( wp_html_excerpt( strip_shortcodes( $cur_post->post_content ), 300 ) );
	}
	if ( empty( $a['description'] ) ) {
		$a['description'] = sprintf( '%s - %s Video', $a['title'], $a['provider'] );
	}

	return $a;
}

function arve_pro_sc_filter_img_src( $a ) {

	if ( ! empty( $a['img_src'] ) ) {
		return $a;
	}

	$id             = $a['id'];
	$provider       = $a['provider'];
	$options        = arve_pro_get_options();
	$transient_name = "arve_{$provider}_{$id}_thumbnail";
	$transient      = get_transient( $transient_name );

	if ( defined( 'ARVE_DEBUG' ) ) {
		delete_transient( $transient_name );
	}

	if ( false !== $transient ) {
		$a['img_src'] = $transient;
		return $a;
	}

	switch ( $a['provider'] ) {
		case 'alugha':
			$a['img_src'] = arve_pro_get_json_thumbnail( "https://api.alugha.com/v1/videos/$id", 'thumb', $a );
			break;
		case 'dailymotionlist': // TODO Check if there are always 720p thumbnails.
			$a['img_src'] = arve_pro_get_json_thumbnail( "https://api.dailymotion.com/playlist/$id?fields=thumbnail_720_url$id", 'thumbnail_720_url', $a ); // TODO _url$id ??????
			break;
		case 'TODOmetacafe': // This shit isnt working.
			$request  = "http://www.metacafe.com/api/item/{$id}/";
			$response = wp_remote_get( $request );
			if ( ! is_wp_error( $response ) ) {
				$xml          = new SimpleXMLElement( $response['body'] );
				$result       = $xml->xpath( '/rss/channel/item/media:thumbnail/@url' );
				$result       = (string) $result[0]['url'];
				$a['img_src'] = arve_pro_drop_url_parameters( $result );
			}
			break;
		case 'twitch':
			if ( is_numeric( $id ) ) {
				$a['img_src'] = arve_pro_get_json_thumbnail( 'https://api.twitch.tv/kraken/videos/v' . $id, 'preview', $a );
			} else {
				$a['img_src'] = arve_pro_get_json_thumbnail( 'https://api.twitch.tv/kraken/channels/' . $id, 'video_banner', $a );
			}
			break;
		case 'liveleak':
			$response = wp_remote_get( "http://www.liveleak.com/view?$id", array() );
			$html     = wp_remote_retrieve_body( $response );

			preg_match( '#<meta property="og:image" content="([^"]+)#i', $html, $matches );

			if ( ! empty( $matches[1] ) && arve_starts_with( $matches[1], 'http' ) ) {

				$a['img_src'] = $matches[1];

				if ( arve_ends_with( $matches[1], 'logo.gif' ) ) {
					$a['img_src'] = '';
					break;
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

			$data = arve_pro_get_json_thumbnail( "https://graph.facebook.com/{$fb_vidid}/picture?redirect=false", 'data', $a );

			if ( ! empty( $data->url ) ) {
				$a['img_src'] = $data->url;
			}
			break;
	}

	if ( isset( $a['img_src'] ) && ! is_wp_error( $a['img_src'] ) ) {
		set_transient( $transient_name, (string) $a['img_src'], $options['transient_expire_time'] );
	}

	return $a;
}

function arve_pro_sc_filter_img_src_srcset( $atts ) {

	if (
		$atts['img_srcset'] ||
		! in_array( $atts['mode'], array( 'lazyload', 'lazyload-lightbox' ), true ) ||
		! function_exists( 'getimagesizefromstring' )
	) {
		return $atts;
	}

	$options        = arve_pro_get_options();
	$transient_name = "arve_{$atts['provider']}_{$atts['id']}_thumbnail_srcset";

	if ( defined( 'ARVE_DEBUG' ) ) {
		delete_transient( $transient_name );
	}

	$srcset = get_transient( $transient_name );

	if ( false === $srcset ) :

		$srcset = array();

		if ( 'youtube' === $atts['provider'] ) {

			$mq     = "https://i.ytimg.com/vi/{$atts['id']}/mqdefault.jpg";     // 320x180
			$hq     = "https://i.ytimg.com/vi/{$atts['id']}/hqdefault.jpg";     // 480x360
			$sd     = "https://i.ytimg.com/vi/{$atts['id']}/sddefault.jpg";     // 640x480
			$maxres = "https://i.ytimg.com/vi/{$atts['id']}/maxresdefault.jpg"; // hd, fullhd ...

			$size_mq     = arve_pro_get_image_size( $mq );
			$size_hq     = arve_pro_get_image_size( $hq );
			$size_sd     = arve_pro_get_image_size( $sd );
			$size_maxres = arve_pro_get_image_size( $maxres );

			// phpcs:disable WordPress.PHP.StrictComparisons.LooseComparison
			if ( $size_mq && 320 == $size_mq[0] && 180 == $size_mq[1] ) {
				$srcset[320] = $mq;
			}
			if ( $size_hq && 480 == $size_hq[0] && 360 == $size_hq[1] ) {
				$srcset[480] = $hq;
			}
			if ( $size_sd && 640 == $size_sd[0] && 480 == $size_sd[1] ) {
				$srcset[640] = $sd;
			}
			if ( $size_maxres && $size_maxres[0] >= 1280 && $size_maxres[1] >= 720 ) {
				$srcset[ $size_maxres[0] ] = $maxres;
			}
			// phpcs:enable WordPress.PHP.StrictComparisons.LooseComparison

		} elseif ( 'vimeo' === $atts['provider'] ) {

			if ( is_string( $atts['img_src'] ) && ! empty( $atts['img_src'] ) ) {

				foreach ( array( 320, 640, 1280, 1920 ) as $size ) :

					$matches = array();
					preg_match( '#^https://i.vimeocdn.com.*_([0-9]+)(x[0-9]+)?.jpg$#', $atts['img_src'], $matches );

					if ( empty( $matches[1] ) || $size > (int) $matches[1] ) {
						continue;
					}

					$vimeo_thumb_url = str_replace( array( '_320.jpg', '_640.jpg', '_1280.jpg', '_1920.jpg' ), "_$size.jpg", $atts['img_src'] );

					if ( arve_pro_get_image_size( $vimeo_thumb_url ) ) {
						$srcset[ $size ] = $vimeo_thumb_url;
					}

				endforeach;
			}
		}

		set_transient( $transient_name, (array) $srcset, $options['transient_expire_time'] );

	endif; // transient found.

	if ( ! empty( $srcset ) ) {

		foreach ( $srcset as $size => $url ) {
			$srcset_comb[] = "$url {$size}w";
		}

		$atts['img_srcset'] = implode( ', ', $srcset_comb );
	}

	if ( ! empty( $srcset[480] ) ) {
		$atts['img_src'] = $srcset[480];
	}

	if ( ! empty( $srcset[320] ) ) {
		$atts['img_src'] = $srcset[320];
	}

	if ( ! empty( $srcset[130] ) ) {
		$atts['img_src'] = $srcset[130];
	}

	return $atts;
}
