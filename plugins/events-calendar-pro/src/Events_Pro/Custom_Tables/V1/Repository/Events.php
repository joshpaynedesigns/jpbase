<?php
/**
 * Handles the creation of recurring events with Series.
 *
 * @since   6.0.0
 *
 * @package TEC\Events_Pro\Custom_Tables\V1
 */

namespace TEC\Events_Pro\Custom_Tables\V1\Repository;

use TEC\Events\Custom_Tables\V1\Models\Event;
use TEC\Events\Custom_Tables\V1\Repository\Events as TEC_Events;
use TEC\Events\Custom_Tables\V1\Tables\Events as EventsSchema;
use TEC\Events\Custom_Tables\V1\Tables\Occurrences as OccurrencesSchema;
use TEC\Events_Pro\Custom_Tables\V1\Models\Series;
use TEC\Events_Pro\Custom_Tables\V1\Series\Relationship;
use TEC\Events_Pro\Custom_Tables\V1\Tables\Series_Relationships as Series_RelationshipsSchema;
use TEC\Events_Pro\Custom_Tables\V1\Updates\Update_Type;
use TEC\Events_Pro\Custom_Tables\V1\Updates\WP_Function_Edit;
use RuntimeException;

/**
 * The recurrence creator callback.
 *
 * @since   6.0.0
 *
 * @package TEC\Events_Pro\Custom_Tables\V1
 */
class Events extends TEC_Events {
	/**
	 * A map of the update data per post ID.
	 *
	 * @since 6.0.0
	 *
	 * @var array<int,array<string,mixed>>
	 */
	private $update_data = [];

	/**
	 * Since the creation of Occurrences is handled in the upsert method,
	 * this one will no-op the Occurrences creation callback.
	 *
	 * @since 6.0.0
	 *
	 * @return callable The `__return_true` function, as this has already been handled.
	 */
	public function create_recurrence_callback() {
		return [ $this, 'upsert_occurrences' ];
	}

	/**
	 * Saves an Event, recurring or not, information to the database handling Series
	 * relationships.
	 *
	 * @since 6.0.0
	 *
	 * @param int|\WP_Post        $event_id The Event post ID, or a reference to the Event post object.
	 * @param array<string,mixed> $data     The data, all of it, used to upsert the Event.
	 *
	 * @return int|null The updated Event ID, or `null` on failure.
	 */
	public function update( $event_id, $data ) {
		// Save as TEC would.
		parent::update( $event_id, $data );

		$this->update_data[ $event_id ] = $data;

		try {
			/** @var Event $event */
			$event = Event::find( $event_id, 'post_id' );

			if ( ! $event instanceof Event ) {
				return null;
			}

			$this->update_series_relationships( $event, $data );
		} catch ( \Exception $e ) {
			do_action( 'tribe_log', 'error', $e->getMessage(), [
				'source'  => __CLASS__,
				'post_id' => $event_id,
			] );

			return null;
		}

		return $event_id;
	}

	/**
	 * Upserts the Event <> Series relationship information in the custom tables.
	 *
	 * @since 6.0.0
	 *
	 * @param Event               $event   A reference to the Event model.
	 * @param array<string,mixed> $payload The Event upsertion payload.
	 *
	 * @return Event A reference to the Event model.
	 *
	 * @throws RuntimeException On failure.
	 */
	private function update_series_relationships( Event $event, $payload ) {
		$event_post  = get_post( $event->post_id );
		$series_data = isset( $payload['meta_input']['series'] ) ?
			(array) $payload['meta_input']['series']
			: false;

		try {
			if ( ! empty( $series_data ) ) {
				// Associate new Event to the existing Series.
				tribe( Relationship::class )->with_event( $event, $series_data );
			} elseif ( ! empty( $event->rset ) ) {
				// If we're here, then the Event is recurring, it MUST have a Series relationship.
				$series_post_ids = (array) Series::vinsert(
					[ [ 'title' => $event_post->post_title ] ],
					[ 'post_status' => $event_post->post_status ]
				);
				tribe( Relationship::class )->with_event( $event, $series_post_ids );
			}
		} catch ( \Exception $e ) {
			throw new RuntimeException( 'Failed to update Series relationship in repository.' );
		}

		return $event;
	}

	/**
	 * This method will run after the normal creation one did run and wil repeat
	 * the Event and Occurrence upsert operations if Recurrence rules came in.
	 *
	 * While a double-write, this method will not run if the Event is not recurring
	 * and we need to run the "normal" upsertion in the `update` method and this new
	 * upsertion once we have new information about recurrence.
	 *
	 * @param int                 $event_id           The Event post ID.
	 * @param array<string,mixed> $recurrence_payload The recurrence payload.
	 *
	 * @return bool Whether the update was successful or not.
	 */
	public function upsert_occurrences( $event_id, $recurrence_payload ) {
		if ( ! isset( $recurrence_payload['recurrence'] ) ) {
			return true;
		}

		// The Recurrence data might not have been saved yet: let's do  it now.
		update_post_meta( $event_id, '_EventRecurrence', $recurrence_payload['recurrence'] );

		if ( Event::upsert( [ 'post_id' ], Event::data_from_post( $event_id ) ) === false ) {
			return false;
		}

		/** @var Event $event */
		$event = Event::find( $event_id, 'post_id' );

		if ( ! $event instanceof Event ) {
			return false;
		}

		try {
			$event->occurrences()->save_occurrences();
			if ( isset( $this->update_data[ $event_id ] ) ) {
				$data = $this->update_data[ $event_id ];
				unset( $this->update_data[ $event_id ] );
				$this->update_series_relationships( $event, $data );
			}
		} catch ( \Exception $e ) {
			do_action( 'tribe_log', 'error', 'Failed to upsert recurring Event Occurrences.', [
				'source'  => __CLASS__,
				'post_id' => $event_id,
			] );

			return false;
		}

		return true;
	}

	/**
	 * Counts the occurrences for this series.
	 *
	 * @since 6.0.0
	 *
	 * @param numeric $post_id The Series post ID.
	 *
	 * @return int
	 */
	public function get_occurrence_count_for_series( $post_id ) {
		global $wpdb;

		$events_table        = EventsSchema::table_name( true );
		$series_events_table = Series_RelationshipsSchema::table_name( true );
		$occurrence_table    = OccurrencesSchema::table_name( true );
		$query               = "
			SELECT COUNT(*)
			FROM `{$series_events_table}`
			INNER JOIN `{$events_table}`
				ON `{$series_events_table}`.event_id = `{$events_table}`.event_id
			INNER JOIN `{$wpdb->posts}`
				ON `{$wpdb->posts}`.ID = `{$events_table}`.post_id
			INNER JOIN `{$occurrence_table}`
				ON `{$series_events_table}`.event_id = `{$occurrence_table}`.event_id
			WHERE `{$wpdb->posts}`.post_status != 'trash'
			 	AND `{$series_events_table}`.`series_post_id` = %s";

		return (int) $wpdb->get_var( $wpdb->prepare( $query, $post_id ) );
	}
}
