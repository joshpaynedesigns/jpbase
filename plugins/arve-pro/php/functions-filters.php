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

function pro_modes() {

	return [
		'lazyload'      => __( 'Lazyload', 'arve-pro' ),
		'lightbox'      => __( 'Lightbox', 'arve-pro' ),
		'link-lightbox' => __( 'Link -> Lightbox', 'arve-pro' ),
	];
}

function add_pro_modes( $modes ) {
	return array_merge( $modes, pro_modes() );
}

function html_js_class() {
	echo '<script>document.documentElement.classList.add("js");</script>';
}

function append_lightbox_link( $html, array $a) {

	if ( 'link-lightbox' === $a['mode'] ) {

		$html .= ARVE\build_tag(
			[
				'name'       => 'lightbox-link',
				'tag'        => 'a',
				'inner_html' => trim( $a['title'] ),
				'attr'       => [
					'href'        => '#' . $a['uid'],
					'data-target' => '#' . $a['uid'],
					'role'        => 'button',
					'class'       => 'arve-lightbox-link',
				],
			],
			$a
		);
	}

	return $html;
}
