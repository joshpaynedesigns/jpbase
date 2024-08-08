<?php
/**
 * Manages the Autodetect Fields.
 *
 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
 *
 * @package Tribe\Events\Virtual\Autodetect
 */

namespace Tribe\Events\Virtual\Autodetect;

/**
 * Class Fields
 *
 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
 *
 * @package Tribe\Events\Virtual\Autodetect
 */
class Fields {

	/**
	 * Get the autodetect fields.
	 *
	 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
	 *
	 * @param array<string|string> $fields            An array of fields to display.
	 * @param string               $video_url         The virtual url string.
	 * @param string               $autodetect_source The optional name of the video source to attempt to autodetect.
	 * @param \WP_Post             $event             The current event post object, as decorated by the `tribe_get_event` function.
	 * @param array<string|mixed>  $ajax_data         An array of extra values that were sent by the ajax script.
	 */
	public function get_autodetect_fields( $fields, $video_url, $autodetect_source, $event, $ajax_data ) {
		/**
		 * Allow filtering of the virtual event video url.
		 *
		 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
		 * @todo [plugin-consolidation] Merge VE into ECP, hook to be deprecated and renamed.
		 *
		 * @param string The virtual url string.
		 * @param \WP_Post $post The current event post object, as decorated by the `tribe_get_event` function.
		 */
		$video_url = apply_filters( 'tec_events_virtual_autodetect_video_url', $video_url, $event );

		$autodetect_fields = [];

		foreach ( $fields as $field ) {
			/**
			 * Allow filtering of the virtual event video url.
			 *
			 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
			 * @todo [plugin-consolidation] Merge VE into ECP, hook to be deprecated and renamed.
			 *
			 * @param array<string|string> $autodetect_fields An array of field types to display.
			 * @param string               $video_url         The virtual url string.
			 * @param \WP_Post             $event             The current event post object, as decorated by the `tribe_get_event` function.
			 * @param array<string|mixed>  $ajax_data         An array of extra values that were sent by the ajax script.
			 */
			$autodetect_fields = apply_filters( "tec_events_virtual_video_source_autodetect_field_{$field}", $autodetect_fields, $video_url, $autodetect_source, $event, $ajax_data );
		}

		/**
		 * Allow filtering of all the autodetect fields.
		 *
		 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
		 * @todo [plugin-consolidation] Merge VE into ECP, hook to be deprecated and renamed.
		 *
		 * @param array<string|string> $autodetect_fields An array of field types to display.
		 * @param string               $video_url         The virtual url string.
		 * @param \WP_Post             $event             The current event post object, as decorated by the `tribe_get_event` function.
		 * @param array<string|mixed>  $ajax_data         An array of extra values that were sent by the ajax script.
		 */
		return apply_filters( "tec_events_virtual_video_source_autodetect_fields", $autodetect_fields, $video_url, $autodetect_source, $event, $ajax_data );
	}
}
