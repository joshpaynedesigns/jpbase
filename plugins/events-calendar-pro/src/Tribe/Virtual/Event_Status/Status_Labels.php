<?php
/**
 * The Event Status Labels.
 *
 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
 * @package Tribe\Events\Virtual\Event_Status
 */

namespace Tribe\Events\Virtual\Event_Status;

/**
 * Class Statuses
 *
 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
 *
 * @package Tribe\Events\Virtual\Event_Status
 */
class Status_Labels {

	/**
	 * Add the event statuses to select for an event.
	 *
	 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
	 *
	 * @param array<string|mixed> $statuses       The event status options for an event.
	 * @param string              $current_status The current event status for the event or empty string if none.
	 *
	 * @return array<string|mixed> The event status options for an event.
	 */
	public function filter_event_statuses( $statuses, $current_status ) {
		$default_statuses = [
			[
				'text'     => $this->get_moved_online_label(),
				'id'       => 'moved-online',
				'value'    => 'moved-online',
				'selected' => 'moved-online' === $current_status ? true : false,
			]
		];

		$statuses = array_merge( $statuses, $default_statuses );

		return $statuses;
	}

	/**
	 * Get the moved online status label.
	 *
	 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
	 *
	 * @return string The label for the moved online status.
	 */
	public function get_moved_online_label() {

		/**
		 * Filter the moved online label for event status.
		 *
		 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
		 * @todo [plugin-consolidation] Merge VE into ECP, hook to be deprecated and renamed.
		 *
		 * @param string The default translated label for the moved online status.
		 */
		return apply_filters( 'tec_events_virtual_event_status_moved_online_label', _x( 'Moved Online', 'Moved Online event status label', 'tribe-events-calendar-pro' ) );
	}
}
