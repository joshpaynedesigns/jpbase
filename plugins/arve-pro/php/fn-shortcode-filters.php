<?php declare(strict_types=1);
namespace Nextgenthemes\ARVE\Pro;

use Nextgenthemes\WP;

use function Nextgenthemes\ARVE\options;

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

	foreach ( [
		'title',
		'description',
		'author_name',
		'duration',
	] as $k ) {

		if ( empty( $a[ $k ] ) && ! empty( $a['oembed_data']->$k ) ) {
			$a[ $k ] = (string) $a['oembed_data']->$k;
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

	if ( empty( $a['upload_date'] ) && get_post_datetime() ) {
		$a['upload_date'] = get_post_datetime()->format( \DATETIME::ATOM );
	}
	if ( empty( $a['upload_date'] ) ) {
		$a['upload_date'] = current_datetime()->format( \DATETIME::ATOM );
	}

	return $a;
}

/**
 * 1. Manual thumbnail= id/url (you can use string 'featured' to force the use of featured image)
 * 2. Oembed
 * 3. Get remote without oembed
 * 4. Post Image Fallback
 * 5. Fallback from options page
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

	// if ( empty( $thumbnail ) ) {
	//  $thumbnail = get_thumb_from_api( $a['provider'], $a['id'], $a['errors'] );
	// }

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

	$thumb = WP\remote_get_json( $url, $remote_get_args, $json_name );

	if ( is_wp_error( $thumb ) ) {
		$errors->add( 'thumb-api-call', $thumb->get_error_message() );
		return '';
	}

	return $thumb;
}
