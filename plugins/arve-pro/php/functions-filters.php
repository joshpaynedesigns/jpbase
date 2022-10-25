<?php
namespace Nextgenthemes\ARVE\Pro;

use \Nextgenthemes\ARVE;

function latest_youtube_video_from_channel( $a ) {

	if ( empty( $a['url'] ) ) {
		return $a;
	}

	preg_match( '#youtube\.com/channel/(?<channel>[a-z0-9]+)#i', $a['url'], $matches );

	if ( empty( $matches['channel'] ) ) {
		return $a;
	}

	$response = ARVE\Common\remote_get_body_cached( 'https://www.youtube.com/feeds/videos.xml?channel_id=' . $matches['channel'] );

	if ( is_wp_error( $response ) ) {
		$a['errors']->add(
			2,
			$response->get_error_message()
		);
	} else {

		$xml = simplexml_load_string( $response );

		if ( false === $xml || empty( $xml->entry[0]->children( 'yt', true )->videoId[0] ) ) {

			$channel_url = 'https://youtube.com/channel/' . $matches['channel'];

			$a['errors']->add(
				'fatal',
				sprintf(
					// Translators: URL.
					__( 'Latest video from <a href="%s">channel</a> could not be detected: ', 'arve-pro' ),
					esc_url( $channel_url )
				)
			);
		} else {
			$a['url'] = 'https://youtube.com/watch?v=' . (string) $xml->entry[0]->children( 'yt', true )->videoId[0];
		}
	}

	return $a;
}

function append_lightbox_link( $html, array $a ) {

	if ( 'link-lightbox' !== $a['mode'] ) {
		return $html;
	}

	$html .= ARVE\build_tag(
		[
			'name'       => 'lightbox-link',
			'tag'        => 'a',
			'inner_html' => trim( $a['title'] ),
			'attr'       => bigger_picture_attr( $a ),
		],
		$a
	);

	return $html;
}
