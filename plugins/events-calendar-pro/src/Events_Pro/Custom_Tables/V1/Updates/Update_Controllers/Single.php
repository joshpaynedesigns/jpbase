<?php
/**
 * Hooks on the WordPress IDENTIFY, WRITE and READ phases to break an Occurrence
 * out of the original Recurring Event and update the original and new Events.
 *
 * @since   6.0.0
 *
 * @package TEC\Events_Pro\Custom_Tables\V1\Updates\Update_Controllers
 */

namespace TEC\Events_Pro\Custom_Tables\V1\Updates\Update_Controllers;

use TEC\Events\Custom_Tables\V1\Models\Occurrence;
use TEC\Events_Pro\Custom_Tables\V1\Admin\Notices\Provider as Notices_Provider;
use TEC\Events_Pro\Custom_Tables\V1\Updates\Events;
use WP_Post;

/**
 * Class Single
 *
 * @since   6.0.0
 *
 * @package TEC\Events_Pro\Custom_Tables\V1\Updates\Update_Controllers
 */
class Single implements Update_Controller_Interface {
	use Update_Controller_Methods;

	/**
	 * A reference to the current Events repository handler.
	 *
	 * @since 6.0.0
	 *
	 * @var Events
	 */
	private $events;

	/**
	 * The ID of the Event post created by the Update Controller.
	 *
	 * @since 6.0.0
	 *
	 * @var int|null
	 */
	private $single_post_id;

	/**
	 * Single constructor.
	 *
	 * @since 6.0.0
	 *
	 * @param Events $events A reference to the current Events repository handler.
	 */
	public function __construct( Events $events ) {
		$this->events = $events;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 6.0.0
	 */
	public function apply_before_identify_step( $post_id ) {
		if ( false === ( $post = $this->check_step_requirements( $post_id ) ) ) {
			return false;
		}

		$this->save_request_id( $post_id );

		// 1. Duplicate the original Event.
		$single_post = $this->events->duplicate(
			$post,
			// Keep the same post status as the original Event.
			[ 'post_status' => get_post_field( 'post_status', $post ) ]
		);

		// Remove notices from watching the other events being updated
		tribe( Notices_Provider::class )->unregister();

		if ( ! $single_post instanceof WP_Post ) {
			do_action( 'tribe_log', 'error', 'Failed to create Event on Single update.', [
				'source'  => __CLASS__,
				'slug'    => 'duplicate-fail-on-single-update',
				'post_id' => $post_id,
			] );

			return false;
		}

		$post_id = $post->ID;

		$this->single_post_id = $single_post->ID;
		$occurrence_id        = $this->occurrence->occurrence_id;
		$occurrence_date      = $this->occurrence->start_date;

		$is_first = Occurrence::is_first( $occurrence_id );
		$is_last  = Occurrence::is_last( $occurrence_id );

		if ( $is_first ) {
			// 3. Update the original Event to start on the second Occurrence.
			$second = Occurrence::where( 'post_id', $post_id )
			                    ->order_by( 'start_date', 'ASC' )
			                    ->offset( 1 )
			                    ->first();

			if ( $second instanceof Occurrence ) {
				$this->events->move_event_date( $post_id, $second );
			}
		} elseif ( $is_last ) {
			// 3. Update the original Event Recurrence meta to end before the Occurrence date.
			$previous_occurrence = Occurrence::where( 'post_id', '=', $post_id )
			                                 ->order_by( 'start_date', 'DESC' )
			                                 ->where( 'start_date', '<', $this->occurrence->start_date )
			                                 ->first();

			if (
				$previous_occurrence instanceof Occurrence
				&& ! $this->events->set_until_limit_on_event( $post_id, $previous_occurrence->start_date )
			) {
				do_action( 'tribe_log', 'error', 'Failed to set UNTIL limit on original Event.', [
					'source'  => __CLASS__,
					'slug'    => 'set-until-limit-fail-on-single-update',
					'post_id' => $post_id,
				] );
			}
		} else {
			// 3. Update the original Event Recurrence meta to add an exclusion on this event date.
			$this->events->add_date_exclusion_to_event( $post_id, $occurrence_date );
		}

		/*
		 * Assign the Occurrence to the single Event to give it a chance to
		 * recycle it.
		 */
		$this->events->transfer_occurrences_from_to(
			$post_id,
			$this->single_post_id,
			'start_date = %s',
			$this->occurrence->start_date
		);

		// 4. Before the Custom Tables are updated, clear the Recurrence rules for this Event.
		add_action( 'tec_events_custom_tables_v1_update_post_before', [ $this, 'delete_recurrence_meta' ] );

		$this->ensure_request_meta( $this->request );
		$this->save_rest_request_recurrence_meta( $this->single_post_id, $this->request );

		return $this->single_post_id;
	}

	/**
	 * Removes the Recurrence meta that might have been set for the created Single Event.
	 *
	 * @since 6.0.0
	 *
	 * @param int $post_id The post ID of the event for which the pre-commit process
	 *                     is running.
	 */
	public function delete_recurrence_meta( $post_id ) {
		if ( $post_id !== $this->single_post_id ) {
			return;
		}

		remove_action( current_action(), [ $this, 'delete_recurrence_meta' ] );

		delete_post_meta( $this->single_post_id, '_EventRecurrence' );
	}
}
