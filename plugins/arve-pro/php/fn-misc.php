<?php

declare(strict_types = 1);

namespace Nextgenthemes\ARVE\Pro;

use JsonException;
use MO;
use WP_Error;
use WP_HTML_Tag_Processor;
use function Nextgenthemes\ARVE\height_from_width_and_ratio;
use function Nextgenthemes\ARVE\get_host_properties;
use function Nextgenthemes\ARVE\options;
use function Nextgenthemes\WP\remote_get_body_cached;
use function Nextgenthemes\WP\remote_get_head_cached;
use function Nextgenthemes\WP\remote_get_json_cached;

function oembed_data( object $data ): void {

	if ( 'youtube' === $data->provider ) {
		youtube_srcset( $data );
		add_youtube_api_data( $data );
	}

	vimeo_srcset( $data );
}

function youtube_srcset( object $data ): void {

	$default            = $data->thumbnail_url;
	$path               = parse_url( $default, PHP_URL_PATH );
	$filename           = basename( $path ); // this is hqdefault.jpg or hq2.jpg for shorts.
	$sddefault          = str_replace( $filename, 'sddefault.jpg', $default );
	$maxresdefault      = str_replace( $filename, 'maxresdefault.jpg', $default );
	$webp_default       = strtr(
		$default,
		array(
			'/vi/' => '/vi_webp/',
			'.jpg' => '.webp',
		)
	);
	$webp_path          = parse_url( $webp_default, PHP_URL_PATH );
	$webp_filename      = basename( $webp_path );
	$webp_sddefault     = str_replace( $webp_filename, 'sddefault.webp', $webp_default );
	$webp_maxresdefault = str_replace( $webp_filename, 'maxresdefault.webp', $webp_default );

	if ( ! is_wp_error( remote_get_head_cached( $webp_default ), array(), YEAR_IN_SECONDS ) ) {
		$srcset_arr[] = "{$webp_default} 480w";
		// if smallest webp is available, assume all others have webp versions.
		$sddefault     = $webp_sddefault;
		$maxresdefault = $webp_maxresdefault;
	} else {
		$srcset_arr[] = "{$default} 480w";
	}

	if ( ! is_wp_error( remote_get_head_cached( $sddefault ), array(), YEAR_IN_SECONDS ) ) {
		$srcset_arr[] = "{$sddefault} 640w";
	}

	if ( ! is_wp_error( remote_get_head_cached( $maxresdefault ), array(), YEAR_IN_SECONDS ) ) {
		$srcset_arr[] = "{$maxresdefault} 1280w";
	}

	if ( ! empty( $srcset_arr ) ) {
		$data->thumbnail_srcset    = implode( ', ', $srcset_arr );
		$data->thumbnail_large_url = str_replace( [ ' 480w', ' 640w', ' 1280w' ], '', end( $srcset_arr ) );
	}
}

function vimeo_srcset( object $data ): void {

	if ( 'vimeo' !== $data->provider ) {
		return;
	}

	if ( empty( $data->width ) || empty( $data->height ) ) {
		$data->arve_vimeo_srcset_error = 'No width or height in oembed data';
	}

	foreach ( [ 320, 640, 960, 1280 ] as $width ) {

		$height       = (int) height_from_width_and_ratio( $width, "{$data->width}:{$data->height}" );
		$url          = preg_replace( '/^(.*)_([0-9x]{3,9}(\.jpg)?)$/i', "$1_{$width}x{$height}", $data->thumbnail_url );
		$srcset_arr[] = "{$url} {$width}w";

		if ( 1280 === $width ) {
			$data->thumbnail_large_url = $url;
		}
	}

	$data->thumbnail_srcset = implode( ', ', $srcset_arr );
}

function add_youtube_api_data( object $data ): object {

	if ( 'youtube' !== $data->provider ) {
		return $data;
	}

	if ( get_option( 'arve_youtube_api_error' ) ) {
		return $data;
	}

	$yt_api_data = get_yt_api_data( $data->arve_url );

	if ( is_wp_error( $yt_api_data ) ) {
		add_option( 'arve_youtube_api_error', $yt_api_data );
	} else {
		$data->description = $yt_api_data['snippet']['description'];
		$data->upload_date = $yt_api_data['snippet']['publishedAt'];
		$data->category_id = $yt_api_data['snippet']['categoryId'];
		$data->view_count  = $yt_api_data['statistics']['viewCount'];
		$data->like_count  = $yt_api_data['statistics']['likeCount'];
	}

	return $data;
}

/**
 * Gets the YouTube video data from the YouTube API.
 *
 * @param string $url The URL of the YouTube video.
 *
 * @return mixed|WP_Error The video data, a WP_Error object on failure.
 */
function get_yt_api_data( string $url ) {

	$pattern = get_host_properties()['youtube']['regex'];
	preg_match( $pattern, $url, $matches );

	if ( empty( $matches['id'] ) ) {
		return new WP_Error( 'arve_youtube_api_error', 'Video ID not detected with regex', compact( 'pattern', 'url', 'matches' ) );
	}

	$api_key = options()['youtube_data_api_key'] ?? '';
	$api_key = empty( $api_key ) ? 'AIzaSyAQ7WFzTAUrOX-FjsIrFS3JwZBFzgIvloc' : $api_key;

	// Construct API request URL for video details
	$url = 'https://www.googleapis.com/youtube/v3/videos?' . http_build_query(
		array(
			'part' => 'snippet,statistics',
			'id'   => $matches['id'],
			'key'  => $api_key,
		)
	);

	$data = remote_get_json_cached( $url, array(), 'items', YEAR_IN_SECONDS );

	if ( is_wp_error( $data ) ) {
		return $data;
	}

	return $data[0];
}

function add_youtube_data( object $data ): object {

	$youtube_data = scrape_youtube_data( $data->arve_url );

	if ( is_wp_error( $youtube_data ) ) {
		wp_trigger_error( __FUNCTION__, $youtube_data->get_error_message() );
		add_option( 'arve_youtube_scrape_error', $youtube_data );
	} else {
		$microformat = $youtube_data['microformat']['playerMicroformatRenderer'];

		$data->description = $microformat['description']['simpleText'];
		$data->upload_date = $microformat['uploadDate'];
		$data->view_count  = $microformat['viewCount'];
		$data->category    = $microformat['category'];
		$data->scraped     = true;
	}

	return $data;
}

/**
 * Gets the YouTube video data from the YouTube without API.
 *
 * @param string $url The URL of the YouTube video.
 *
 * @return array|WP_Error The video data or a WP_Error object on failure.
 */
function scrape_youtube_data( string $url ) {

	$html = remote_get_body_cached(
		$url,
		array(
			'headers'     => array(
				'Range'           => 'bytes=0-300000', // YouTube servers do not support this
				'User-Agent'      => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36',
				'Accept-Language' => 'en-US,en;q=0.5',
				'Cookie'          => 'CONSENT=YES+cb.20250314-17-p0.en+FX+123', // Skip consent dialog
			),
			'timeout'     => 15, // Increase timeout if needed
			'redirection' => 5, // Follow redirects
		),
		YEAR_IN_SECONDS
	);

	if ( is_wp_error( $html ) ) {
		return $html;
	}

	$p    = new WP_HTML_Tag_Processor( $html );
	$data = false;

	while ( ! $data && $p->next_tag( 'SCRIPT' ) ) {

		$script = $p->get_modifiable_text();

		if ( ! str_starts_with( $script, 'var ytInitialPlayerResponse' ) ) {
			continue;
		}

		preg_match( '/var ytInitialPlayerResponse\s*=\s*(\{.*?\})\s*;/', $html, $matches );

		if ( empty( $matches[1] ) ) {
			return new WP_Error( 'failed-finding-youtube-json', 'No ytInitialPlayerResponse JSON found', $url );
		}

		try {
			$data = json_decode( $matches[1], true, 50, JSON_THROW_ON_ERROR );
		} catch ( JsonException $e ) {
			return new WP_Error( 'invalid-youtube-json', 'YouTube JSON Error: ' . $e->getMessage(), $url );
		}
	}

	if ( empty( $data['microformat']['playerMicroformatRenderer'] ) ) {
		return new WP_Error( 'empty-microformat', 'YouTube JSON Error: No microformat data found', $url );
	}

	return $data;
}

/**
 * Unused
 *
 * @return mixed
 */
function json_api_call( string $api_url, array $atts ) {

	$wp_remote_get_args = [];

	// wp_remote_get_fails for yahoo.
	if ( 'yahoo' === $atts['provider'] ) {

		// phpcs:disable WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$response = file_get_contents( $api_url ); // TODO Check wp_remote_post.
		// phpcs:enable WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		return json_decode( $response );

	} elseif ( 'twitch' === $atts['provider'] ) {

		$wp_remote_get_args['headers'] = [ 'Client-ID' => 'in8d3vsv6bmbmsdrfoch204ict7kos7' ];
	}

	$response = remote_get_json( $api_url, $wp_remote_get_args );
}
