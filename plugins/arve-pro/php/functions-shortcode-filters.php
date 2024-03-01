<?php
namespace Nextgenthemes\ARVE\Pro;

use function Nextgenthemes\ARVE\Common\remote_get_json;
use function Nextgenthemes\ARVE\options;

function arg_filter_autoplay( $autoplay, array $a ) {

	if ( in_array(
		$a['mode'],
		array(
			'lazyload',
			'lightbox',
			'link-lightbox',
		),
		true
	) ) {
		$autoplay = true;
	}

	return $autoplay;
}

function shortcode_atts_extra_data( array $a ) {

	$cur_post = get_post();
	$oembeds  = array(
		#  $a         -  oembed
		'title'       => 'title',
		'description' => 'description',
		'author_name' => 'author_name',
		'duration'    => 'duration',
		'thumbnail'   => 'thumbnail_url',
	);

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

/**
 * 1. Manual thumbnail= id/url (you can use string 'featured' to force the use of featured image)
 * 2. Oembed
 * 3. Get remote without oembed
 * 4. Post Image Fallback
 * 5. Fallback from options page
 */
function arg_filter_thumbnail( $thumbnail, array $a ) {

	$options  = options();
	$thumb_id = get_post_thumbnail_id();

	if ( 'featured' === $thumbnail && $thumb_id ) {
		$thumbnail = $thumb_id;
	}

	if ( empty( $thumbnail ) ) {
		$thumbnail = get_thumb_from_api($thumbnail, $a);
	}

	if ( empty( $thumbnail ) && $options['thumbnail_post_image_fallback'] && $thumb_id ) {
		$thumbnail = $thumb_id;
	}

	if ( empty( $thumbnail ) && $options['thumbnail_fallback'] ) {
		$thumbnail = $options['thumbnail_fallback'];
	}

	return $thumbnail;
}

function get_thumb_from_api( $thumbnail, array $a ) {

	switch ( $a['provider'] ) {
		case 'DOWN?alugha':
			$thumbnail = get_json_thumbnail(
				$a,
				"https://api.alugha.com/v1/videos/{$a['id']}",
				array(),
				'thumb'
			);
			break;
	}

	return $thumbnail;
}

function get_json_thumbnail( $a, $url, $remote_get_args, $json_name ) {

	$thumb = remote_get_json( $url, $remote_get_args, $json_name );

	if ( is_wp_error( $thumb ) ) {
		$a['errors']->add( 'thumb-api-call', $thumb->get_error_message() );
		$thumb = '';
	}

	return $thumb;
}
