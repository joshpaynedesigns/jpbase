<?php

declare(strict_types = 1);

namespace Nextgenthemes\ARVE\Pro;

use DateTime;
use function Nextgenthemes\WP\remote_get_json_cached;
use function Nextgenthemes\ARVE\translation;
use function Nextgenthemes\ARVE\options;
use function Nextgenthemes\ARVE\arve_errors;
use function Nextgenthemes\ARVE\get_host_properties;

function arg_filter_autoplay( bool $autoplay, array $a ): bool {

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

function shortcode_atts_extra_data( array $a ): array {

	$cur_post = get_post();

	// Just pretend we got oembed data
	$a['oembed_data'] = banned_video_graphql_data( $a );

	foreach ( [
		'title',
		'description',
		'author_name',
		'duration',
		'upload_date', // Not part of oEmbed but used for banned.video 'createdAt'
	] as $k ) {

		if ( empty( $a[ $k ] ) && ! empty( $a['oembed_data']->{$k} ) ) {
			$a[ $k ] = (string) $a['oembed_data']->{$k};
		}
	}

	if ( empty( $a['thumbnail'] ) ) {

		if ( ! empty( $a['oembed_data']->arve_thumbnail_id ) ) {
			$a['thumbnail'] = (string) $a['oembed_data']->arve_thumbnail_id;
		} elseif ( ! empty( $a['oembed_data']->thumbnail_url ) ) {
			$a['thumbnail'] = (string) $a['oembed_data']->thumbnail_url;
		}
	}

	if ( empty( $a['title'] ) && ! empty( get_the_title() ) ) {
		$a['title'] = trim( get_the_title() );
	}
	if ( empty( $a['title'] ) ) {
		$a['title'] = trim( get_bloginfo( 'name' ) );
	}

	if ( empty( $a['description'] ) && ! empty( $cur_post->post_content ) ) {
		$a['description'] = trim( wp_html_excerpt( strip_shortcodes( $cur_post->post_content ), 300 ) );
	}
	if ( empty( $a['description'] )
		&& ! empty( $a['title'] )
		&& ! empty( $a['oembed_data']->provider_name )
	) {
		$a['description'] = sprintf(
			'%s - %s %s',
			$a['title'],
			$a['oembed_data']->provider_name,
			translation( 'video' )
		);
	} elseif ( empty( $a['description'] ) && ! empty( $a['title'] ) ) {
		$a['description'] = sprintf(
			'%s - %s',
			$a['title'],
			translation( 'video' )
		);
	}

	if ( empty( $a['upload_date'] ) && get_post_datetime() ) {
		$a['upload_date'] = get_post_datetime()->format( DATETIME::ATOM );
	}
	if ( empty( $a['upload_date'] ) ) {
		$a['upload_date'] = current_datetime()->format( DATETIME::ATOM );
	}

	return $a;
}

/**
 * 1. Manual thumbnail= id/url (you can use string 'featured' to force the use of featured image)
 * 2. Oembed
 * 3. Get remote without oembed
 * 4. Post Image Fallback
 * 5. Fallback from options page (defaults to a black image with some lines)
 *
 * @param int|string $thumbnail ID, URL, 'featured'
 *
 * @return mixed
 */
function arg_filter_thumbnail( $thumbnail ) {

	$options  = options();
	$thumb_id = get_post_thumbnail_id();

	if ( 'featured' === $thumbnail && $thumb_id ) {
		$thumbnail = $thumb_id;
	}

	if ( empty( $thumbnail ) && $options['thumbnail_post_image_fallback'] && $thumb_id ) {
		$thumbnail = $thumb_id;
	}

	if ( empty( $thumbnail ) && $options['thumbnail_fallback'] ) {
		$thumbnail = $options['thumbnail_fallback'];
	}

	return $thumbnail;
}

/**
 * Get the thumbnail from the API based on the provider.
 */

/*
function get_thumb_from_api( string $provider, string $id, \WP_Error $errors ): string {

	$thumbnail = '';

	switch ( $provider ) {

		case 'alugha':
			$thumbnail = get_json_thumbnail(
				"https://api.alugha.com/v1/videos/{$id}",
				[],
				'thumb',
				$errors
			);

			break;
	}

	return $thumbnail;
}
*/

/**
 * Retrieves a JSON thumbnail from a given URL using provided arguments.
 *
 * @param string $url The URL to retrieve the JSON thumbnail from.
 * @param array $remote_get_args The arguments to be passed to the remote_get_json function.
 * @param string $json_name The name of the JSON key to retrieve.
 * @param \WP_Error $errors The WP_Error object for error handling.
 * @return string The retrieved JSON thumbnail.
 */
function get_json_thumbnail( string $url, array $remote_get_args, string $json_name, \WP_Error $errors ): string {

	$thumb = remote_get_json( $url, $remote_get_args, $json_name );

	if ( is_wp_error( $thumb ) ) {
		$errors->add( 'thumb-api-call', $thumb->get_error_message() );
		return '';
	}

	return $thumb;
}

function banned_video_graphql_data( array $a ): ?object {

	preg_match( get_host_properties()['bannedvideo']['regex'], $a['url'], $matches );

	if ( empty( $matches['id'] ) ) {
		return $a['oembed_data'];
	}

	$query =
		'query GetVideoAndComments($id: String!) {
			getVideo(id: $id) {
				streamUrl
				directUrl
				title
				summary
				playCount
				largeImage
				videoDuration
				channel {
					title
				}
				publishedAt
				createdAt
			}
		}';

	// Make the GraphQL call
	$response = remote_get_json_cached(
		'https://api.banned.video/graphql',
		array(
			'method'  => 'POST',
			'headers' => array(
				'Content-Type' => 'application/json',
			),
			'body'    => wp_json_encode(
				[
					'query'     => $query,
					'variables' => [
						'id' => $matches['id'],
					],
				]
			),
		),
		'data',
		WEEK_IN_SECONDS
	);

	if ( is_wp_error( $response ) ) {

		arve_errors()->add(
			'banned-video-graphql-error',
			sprintf(
				// Translators: %s error message
				__( 'banned.video GraphQL error: %s', 'arve-pro' ),
				$response->get_error_message()
			)
		);

		return $a['oembed_data'];
	}

	$a['oembed_data']                = (object) array();
	$a['oembed_data']->provider_name = 'bannedvideo';
	$a['oembed_data']->type          = 'video';
	$a['oembed_data']->title         = $response['getVideo']['title'];
	$a['oembed_data']->description   = $response['getVideo']['summary'];
	$a['oembed_data']->thumbnail_url = $response['getVideo']['largeImage'];
	$a['oembed_data']->upload_date   = $response['getVideo']['createdAt'];
	$a['oembed_data']->duration      = $response['getVideo']['videoDuration'];
	$a['oembed_data']->author_name   = $response['getVideo']['channel']['title'];

	return $a['oembed_data'];
}
