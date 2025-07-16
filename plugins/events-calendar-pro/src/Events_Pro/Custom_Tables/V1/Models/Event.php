<?php
/**
 * Provides the code required to extend the base Event Model using the extensions API.
 *
 * @since   6.0.0
 *
 * @package TEC\Events_Pro\Custom_Tables\V1\Models
 */

namespace TEC\Events_Pro\Custom_Tables\V1\Models;

use Exception;
use DateTime;
use TEC\Events\Custom_Tables\V1\Models\Occurrence;
use TEC\Events_Pro\Custom_Tables\V1\Events\Converter\From_Event_Recurrence_Converter;
use TEC\Events_Pro\Custom_Tables\V1\Models\Formatters\RSet_Formatter;
use TEC\Events_Pro\Custom_Tables\V1\Models\Validators\Valid_RSet;
use TEC\Events_Pro\Custom_Tables\V1\Series\Post_Type as Series;
use TEC\Events_Pro\Custom_Tables\V1\Traits\With_Event_Recurrence;
use Tribe__Cache;
use Tribe__Cache_Listener;
use Tribe__Date_Utils as Dates;
use Tribe__Timezones as Timezones;
use TEC\Events\Custom_Tables\V1\Tables\Events as Events_Table;
use TEC\Events_Pro\Custom_Tables\V1\Tables\Series_Relationships;
use TEC\Events_Pro\Custom_Tables\V1\Events\Provisional\ID_Generator;

/**
 * Class Event
 *
 * @since   6.0.0
 *
 * @package TEC\Events_Pro\Custom_Tables\V1\Models
 */
class Event {
	use With_Event_Recurrence;

	/**
	 * Retrieves the wp_posts id of the Series a post is connected to.
	 *
	 * @since 6.0.0
	 * @param $event_id
	 *
	 * @return int
	 */
	public static function get_series_id( $event_id ) {
		$related_series = Series_Relationship::where( 'event_post_id', '=', $event_id )->get();
		$series_map     = array_map(
			static function ( Series_Relationship $relationship ) {
				return $relationship->series_post_id;
			},
			$related_series
		);

		return (int) array_shift( $series_map );
	}

	/**
	 * Checks if the an id refers to an object that is part of a Series.
	 *
	 * @since 6.0.0
	 *
	 * @param $object_id
	 *
	 * @return bool
	 */
	public static function is_part_of_series( $object_id ) {

		$event_id = tribe( Occurrence::class )->normalize_occurrence_post_id( $object_id );

		if ( $object_id !== $event_id ) {
			return true;
		}

		return static::get_series_id( $event_id ) > 0;
	}

	/**
	 * Get the next upcoming event ID in a series or recurring event.
	 * If no upcoming event exists, get the last one.
	 *
	 * @since 7.5.0
	 *
	 * @param int $id The ID of the series or recurring event to get the next event for.
	 *
	 * @return int|null The next upcoming event ID or the last event ID. Null if no events exist.
	 */
	public static function next_in_series( int $id ): ?int {
		$cache     = tribe_cache();
		$cache_key = 'tec_series_next_event_' . $id;
		$cached    = $cache->get( $cache_key, Tribe__Cache_Listener::TRIGGER_SAVE_POST );

		if ( false !== $cached ) {
			return $cached;
		}

		// For recurring events get the parent Series ID.
		if ( tribe_is_recurring_event( $id ) ) {
			$id = static::get_series_id( $id );
		}

		if ( Series::POSTTYPE !== get_post_type( $id ) ) {
			return null;
		}

		global $wpdb;
		$events_table        = Events_Table::table_name( true );
		$series_events_table = Series_Relationships::table_name( true );

		$query = "
		SELECT `{$series_events_table}`.event_post_id
		FROM `{$series_events_table}`
		INNER JOIN `{$events_table}` ON `{$series_events_table}`.event_id = `{$events_table}`.event_id
		INNER JOIN `{$wpdb->posts}`  ON `{$wpdb->posts}`.ID = `{$events_table}`.post_id
		WHERE `{$wpdb->posts}`.post_status != 'trash'";
		// phpcs:disable WordPress.DB.PreparedSQL, WordPress.DB.DirectDatabaseQuery
		$query        .= $wpdb->prepare( " AND `{$series_events_table}`.`series_post_id` = %s", $id );
		$relationships = $wpdb->get_results( $query );
		// phpcs:enable WordPress.DB.PreparedSQL, WordPress.DB.DirectDatabaseQuery


		if ( count( $relationships ) === 0 ) {
			return null;
		}

		$related_event_ids = wp_list_pluck( $relationships, 'event_post_id' );

		$timezone = Timezones::build_timezone_object();
		$today    = new DateTime( 'now', $timezone );
		$next     = Occurrence::where_in( 'post_id', $related_event_ids )
			->where( 'start_date', '>=', $today )
			->order_by( 'start_date_utc', 'ASC' )
			->limit( 1 )
			->get();

		// If no future occurrences, get the last one.
		if ( empty( $next ) ) {
			$next = Occurrence::where_in( 'post_id', $related_event_ids )
				->order_by( 'start_date_utc', 'DESC' )
				->limit( 1 )
				->get();
		}

		$provisional_id_generator = new ID_Generator();
		$occurrence_post_id       = $provisional_id_generator->current() + $next[0]->occurrence_id;

		if ( $occurrence_post_id ) {
			$cache->set( $cache_key, $occurrence_post_id, Tribe__Cache::NON_PERSISTENT, Tribe__Cache_Listener::TRIGGER_SAVE_POST );
			return $occurrence_post_id;
		}

		$cache->set( $cache_key, null, Tribe__Cache::NON_PERSISTENT, Tribe__Cache_Listener::TRIGGER_SAVE_POST );
		return null;
	}

	/**
	 * Extends the base Event Model using the extensions API.
	 *
	 * @since 6.0.0
	 *
	 * @param array<string,array<string,mixed>> $extensions A map of the current Model
	 *                                                      extensions.
	 *
	 * @return array<string,array<string,mixed>> The filtered extensions map.
	 */
	public function extend( array $extensions = [] ) {
		return wp_parse_args(
			[
				'validators'  => [
					'rset' => Valid_RSet::class,
				],
				'formatters'  => [
					'rset' => RSet_Formatter::class,
				],
				'hashed_keys' => [
					'rset',
				],
				'methods'     => [
					'has_recurrence' => function () {
						/** @var Event $this Bound at run time to the Closure. */
						return ! empty( $this->rset );
					},
				],
			],
			$extensions
		);
	}

	/**
	 * Filters the Event post data adding the ECP data to it.
	 *
	 * @since 6.0.0
	 *
	 * @param array<string,mixed> $data     The Event post data, as produced by The Events Calendar and
	 *                                      previous filtering functions.
	 * @param int                 $event_id The Event post ID.
	 *
	 * @return array<string,mixed> The filtered Event post data.
	 */
	public function add_event_post_data( array $data, $event_id ) {
		$recurrence = get_post_meta( $event_id, '_EventRecurrence', true );

		$recurrence = $this->add_off_pattern_flag_to_meta_value( $recurrence, $event_id );

		if (
			empty( $recurrence['rules'] )
			|| ! isset( $data['start_date'], $data['end_date'], $data['timezone'], $data['duration'] )
		) {
			$data['rset'] = '';
		} else {
			try {
				$tz                        = Timezones::build_timezone_object( get_post_meta( $event_id, '_EventTimezone', true ) );
				$dtstart                   = Dates::immutable( get_post_meta( $event_id, '_EventStartDate', true ), $tz );
				$dtend                     = Dates::immutable( get_post_meta( $event_id, '_EventEndDate', true ), $tz );
				$from_recurrence_converter = new From_Event_Recurrence_Converter( $dtstart, $dtend );
				$converted_rset            = (array) $from_recurrence_converter->convert_to_rset(
					$data['start_date'],
					$data['end_date'],
					$data['timezone'],
					$recurrence
				);

				if ( count( $converted_rset ) ) {
					$data ['rset'] = $this->join_converted_rset( $converted_rset );
				} else {
					do_action(
						'tribe_log',
						'error',
						__CLASS__,
						[
							'message'    => 'Event RSET conversion empty.',
							'post_id'    => $event_id,
							'recurrence' => $recurrence,
						]
					);
					$data ['rset'] = '';
				}
			} catch ( Exception $e ) {
				/**
				 * Filters whether the conversion of `_EventRecurrence` format meta to RSET string
				 * should fail silently or not.
				 *
				 * @since 6.0.1
				 *
				 * @param bool $throw Whether the conversion should throw an exception or not.
				 */
				$throw = apply_filters( 'tec_events_pro_custom_tables_v1_throw_on_rset_conversion', true );

				if ( $throw ) {
					throw $e;
				} else {
					do_action(
						'tribe_log',
						'error',
						__CLASS__,
						[
							'message'    => 'Event RSET conversion failed.',
							'post_id'    => $event_id,
							'error'      => $e->getMessage(),
							'recurrence' => $recurrence,
						]
					);

					$data['rset'] = '';
				}
			}
		}

		return $data;
	}

	/**
	 * Joins the pieces of the converted RSET into a string format RSET definition.
	 *
	 * @since 6.0.0
	 *
	 * @param string|array $rset           Either a converted RSET in map format (from durations
	 *                                     to RRULEs/RDATEs) or in string format. The second will
	 *                                     not be converted.
	 *
	 * @return string The joined converted RSET definition, or an empty string if no line in the RSET
	 *                is providing a DTSTART definition.
	 */
	private function join_converted_rset( $rset ) {
		if ( is_string( $rset ) ) {
			return $rset;
		}

		$joined  = '';
		$dtstart = null;
		foreach ( $rset as $rset_line ) {
			if ( null === $dtstart && 0 === strpos( $rset_line, 'DTSTART' ) ) {
				list( $dtstart ) = explode( "\n", $rset_line );
				$joined         .= $rset_line;
			} elseif ( $dtstart ) {
				$joined .= str_replace( [ $dtstart, $dtstart . "\n" ], [ '', '' ], $rset_line );
			}
		}

		return $dtstart ? $joined : '';
	}
}
