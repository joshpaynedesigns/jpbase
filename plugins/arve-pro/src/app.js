/* global YT, Vimeo */
(function ($) {
	'use strict';

	function vimeo_api_play( $iframe ) {

		var player = new Vimeo.Player( $iframe[ 0 ] );

		player.play();
	}

	function youtube_api_play( $iframe, $wrap ) {

		new YT.Player( $iframe[ 0 ], {
			events: {
				'onReady': onPlayerReady,
			}
		});

		function onPlayerReady(event) {

			if ( $wrap.is('[data-apiplay]') ) {
				event.target.playVideo();
			}

			if ( $wrap.is('[data-volume]') ) {
				event.target.setVolume( $wrap.data('ArveVolume') );
			}
		}
	}

	function load_video( $wrap ) {

		var	$video    = $wrap.find('video');
		var $lazyload = $wrap.find('.arve-lazyload');

		if ( $wrap.is('[data-grow]') ) {
			$wrap.css('max-width', 'none');
		}

		$wrap.find('.arve-title, .arve-description, .arve-play-btn, .arve-thumbnail').addClass('arve-hidden');

		if ( $lazyload.length ) {
			var lazyload_data = $lazyload.data();
			var $iframe = $('<iframe></iframe>').attr( lazyload_data ).insertAfter( $lazyload );
		}

		if ( $video.length ) {
			$video.get(0).play();
		}

		if ( false /* $wrap.is('[data-apiplay]') || $wrap.is('[data-apiplay]') */ ) {

			switch ( $wrap.data( 'host' ) ) {
				case 'youtube':
					youtube_api_play( $iframe, $wrap );
					break;
				case 'vimeo':
					vimeo_api_play( $iframe );
					break;
			}
		}
	}

	$(document).on( 'click', '[data-mode="lazyload"] .arve-play-btn', function( event ) {

		event.preventDefault();

		load_video( $(this).closest( '.arve-wrapper' ) );
	});

	function arve_inview() {

		$( '.arve-wrapper[data-inview-lazyload]' ).one( 'inview', function( event, isInView ) {
			if ( isInView ) {
				load_video( $(this) );
			}
		} );
	}

	arve_inview();

	$( document ).ajaxComplete(function() {
		arve_inview();
	});

	// Lity Lightbox
	$(document).on( 'lity:open', function() {
		// Hide all fixed elements except the lity lightbox
		$('*').not('.lity, .lity-wrap, .lity-close').filter(function() {
    		return $(this).css('position') === 'fixed';
		}).addClass('arve-hidden').attr('data-hidden-fixed', 'true');
	});

	$(document).on( 'lity:ready', function( event, instance ) {

		var $lity_el  = instance.element();
		var $video    = $lity_el.find( 'video' );
		var $lazyload = $lity_el.find( '.arve-lazyload' );

		$( '.lity-wrap' ).attr( 'id', 'arve' );

		if ( $lazyload.length ) {
			var $iframe = $('<iframe></iframe>').attr( $lazyload.data() ).insertAfter( $lazyload );

			if ( false ) {
				youtube_api_play( $iframe );
			}
		}

		if ( $video.length ) {
			$video.get(0).play();
		}
	});

	$( document ).on( 'lity:close', function( event, instance ) {

		var video = instance.element().find( 'video' );

		if ( video.length ){
			instance.element().find( 'video' ).get(0).pause();
		}

		$('.arve-lity-container .arve-iframe').remove();
		$('[data-hidden-fixed]').removeClass('arve-hidden');
	});

	$( document ).ready( function() {
		$('.arve-lightbox-link').off();
	});

}(jQuery));
