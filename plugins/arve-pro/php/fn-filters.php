<?php

declare(strict_types = 1);

namespace Nextgenthemes\ARVE\Pro;

use WP_Error;
use function Nextgenthemes\ARVE\translation;
use function Nextgenthemes\ARVE\arve_errors;
use function Nextgenthemes\WP\remote_get_body_cached;

function latest_youtube_video_from_channel( array $a ): array {

	if ( empty( $a['url'] ) ) {
		return $a;
	}

	preg_match( '#youtube\.com/channel/(?<channel>[a-z0-9_]+)#i', $a['url'], $matches );

	if ( empty( $matches['channel'] ) ) {
		return $a;
	}

	$response = remote_get_body_cached( 'https://www.youtube.com/feeds/videos.xml?channel_id=' . $matches['channel'] );

	if ( is_wp_error( $response ) ) {
		arve_errors()->add(
			2,
			$response->get_error_message()
		);
	} else {

		$a['url'] = extract_video_id_with_regex( $response, $matches['channel'], arve_errors() );

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {

			$simplexml_url = extract_video_id_with_simplexml( $response, $matches['channel'], arve_errors() );

			if ( $a['url'] !== $simplexml_url ) {

				$url = $a['url'];

				arve_errors()->add(
					'fatal',
					"Mismatch between<br> regex____: $url <br>simplexml: $simplexml_url",
				);
			}
		}
	}

	return $a;
}

/**
 * Extracts the first video ID from a YT playlist XML using regex.
 *
 * @param string $xml Playlist XML.
 * @param string $channel The channel to extract the video ID for.
 * @param WP_Error $errors The error object to add errors to.
 * @return string The extracted video ID, or an empty string if not found.
 */
function extract_video_id_with_regex( string $xml, string $channel_id, WP_Error $errors ): string {

	preg_match(
		'#<yt:videoId>([^<]+)</yt:videoId>#m',
		$xml,
		$matches,
		0,
		500
	);

	if ( empty( $matches[1] ) ) {

		$errors->add(
			'fatal',
			sprintf(
				translation( 'latest_video_from_youtube_channel_could_not_be_detected' ),
				esc_url( 'https://youtube.com/channel/' . $channel_id ),
			)
		);

		return '';
	} else {
		return 'https://youtube.com/watch?v=' . $matches[1];
	}
}

/**
 * Extracts the first video ID from a YT playlist XML using SimpleXML.
 *
 * @param string $xml Playlist XML.
 * @param string $channel The channel to extract the video ID for.
 * @param WP_Error $errors The error object to add errors to.
 * @return string The extracted video ID, or an empty string if not found.
 */
function extract_video_id_with_simplexml( string $xml, string $channel_id, WP_Error $errors ): string {

	$xml = simplexml_load_string( $xml );

	if ( false === $xml || empty( $xml->entry[0]->children( 'yt', true )->videoId[0] ) ) {

		$channel_url = 'https://youtube.com/channel/' . $channel_id;

		$errors->add(
			'fatal',
			sprintf(
				// Translators: URL.
				translation( 'latest_video_from_youtube_channel_could_not_be_detected' ),
				esc_url( $channel_url )
			)
		);

		return '';
	} else {
		return 'https://youtube.com/watch?v=' . (string) $xml->entry[0]->children( 'yt', true )->videoId[0];
	}
}

/**
 * Returns an array of iframe attributes with modified sandbox values based on the 'disable_links' flag.
 *
 * @param array $iframe_attr The original array of iframe attributes.
 * @param array $a The array containing 'disable_links' flag.
 * @return array The modified array of iframe attributes.
 */
function iframe_attr( array $iframe_attr, array $a ): array {

	if ( $a['disable_links'] && ! empty( $iframe_attr['sandbox'] ) ) {

		$sandbox_arr = explode( ' ', $iframe_attr['sandbox'] );
		$sandbox_arr = array_diff( $sandbox_arr, [ 'allow-popups', 'allow-popups-to-escape-sandbox' ] );

		$iframe_attr['sandbox'] = implode( ' ', $sandbox_arr );
	}

	return $iframe_attr;
}
