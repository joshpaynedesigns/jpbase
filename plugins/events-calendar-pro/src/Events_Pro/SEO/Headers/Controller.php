<?php
/**
 * Manages the legacy view removal and messaging for Week view.
 *
 * @since 7.4.2
 *
 * @package TEC\Events_Pro\SEO\Headers
 */

namespace TEC\Events_Pro\SEO\Headers;

use TEC\Common\Contracts\Provider\Controller as Controller_Contract;
use Tribe__Events__Main as TEC;

/**
 * Class Controller
 *
 * @since 7.4.2
 *
 * @package TEC\Events_Pro\SEO\Headers
 */
class Controller extends Controller_Contract {

	/**
	 * Register actions.
	 *
	 * @since 7.4.2
	 */
	public function do_register(): void {
		$this->container->singleton( static::class, $this );
		add_action( 'send_headers', [ $this, 'filter_headers' ] );
	}

	/**
	 * Unregister actions.
	 *
	 * @since 7.4.2
	 */
	public function unregister(): void {
		remove_action( 'send_headers', [ $this, 'filter_headers' ] );
	}

	/**
	 * Filter the headers based on the query.
	 *
	 * Only supports the week view.
	 *
	 * @since 7.4.2
	 */
	public function filter_headers() {
		global $wp_query;

		if (
			! isset( $wp_query->query['post_type'] )
			|| $wp_query->query['post_type'] !== TEC::POSTTYPE
			|| ! isset( $wp_query->query['eventDisplay'] )
			|| ! isset( $wp_query->query['eventDate'] )
		) {
			return;
		}

		$enabled_views = tribe_get_option( 'tribeEnableViews' );
		$event_display = $wp_query->query['eventDisplay'];

		if ( 'week' === $event_display ) {
			$this->check_week_view( $wp_query, $enabled_views );
		}
	}

	/**
	 * Check the conditions for the week view.
	 *
	 * If either tribe_events_earliest_date() or tribe_events_latest_date() returns
	 * false/empty and the requested week equals the current week, then do not set a 404.
	 * Otherwise, if the requested week is before the earliest or after the latest event week,
	 * mark the query as a 404.
	 *
	 * @since 7.4.2
	 *
	 * @param object $wp_query      The global WP_Query object.
	 * @param array  $enabled_views An array of the enabled views.
	 */
	private function check_week_view( object $wp_query, array $enabled_views ) {
		if ( ! in_array( 'week', $enabled_views, true ) ) {
			$wp_query->set_404();

			return;
		}

		$data = $this->prepare_week_date_check( $wp_query );

		// If either date is missing and the requested week equals the current week,
		// do not set a 404 (new install safeguard).
		if ( ( ! $data['earliest_date_str'] || ! $data['latest_date_str'] ) && ( $data['event_week'] === $data['current_week'] ) ) {
			return;
		}

		if ( $data['earliest_week'] > $data['event_week'] ) {
			$wp_query->set_404();

			return;
		}

		if ( $data['latest_week'] < $data['event_week'] ) {
			$wp_query->set_404();

			return;
		}
	}

	/**
	 * Prepare common week-based date variables for the week view check.
	 *
	 * This method computes:
	 * - event_date_str: The raw event date from the query (YYYY-MM-DD).
	 * - event_timestamp: The event date as a timestamp.
	 * - event_week: The week of the event in "o-W" format (year-week).
	 * - current_week: The current week in "o-W" format.
	 * - earliest_date_str: The earliest event date (YYYY-MM-DD) via tribe_events_earliest_date().
	 * - latest_date_str: The latest event date (YYYY-MM-DD) via tribe_events_latest_date().
	 * - earliest_week: The week of the earliest event.
	 * - latest_week: The week of the latest event.
	 *
	 * @since 7.4.2
	 *
	 * @param object $wp_query The global WP_Query object.
	 *
	 * @return array An array of computed date values.
	 */
	private function prepare_week_date_check( object $wp_query ): array {
		$event_date_str  = $wp_query->query['eventDate']; // Expected format: YYYY-MM-DD.
		$event_timestamp = strtotime( $event_date_str );
		$event_week      = gmdate( 'o-W', $event_timestamp );
		$current_week    = static::get_current_week();

		$earliest_date_str = tribe_events_earliest_date( 'Y-m-d' );
		$latest_date_str   = tribe_events_latest_date( 'Y-m-d' );

		$earliest_week = $earliest_date_str ? gmdate( 'o-W', strtotime( $earliest_date_str ) ) : '';
		$latest_week   = $latest_date_str ? gmdate( 'o-W', strtotime( $latest_date_str ) ) : '';

		return [
			'event_date_str'    => $event_date_str,
			'event_timestamp'   => $event_timestamp,
			'event_week'        => $event_week,
			'current_week'      => $current_week,
			'earliest_date_str' => $earliest_date_str,
			'latest_date_str'   => $latest_date_str,
			'earliest_week'     => $earliest_week,
			'latest_week'       => $latest_week,
		];
	}

	/**
	 * Get the current week.
	 *
	 * This method returns the current week in "o-W" format (year-week)
	 * and can be overridden in tests.
	 *
	 * @since 7.4.2
	 *
	 * @return string The current week.
	 */
	protected static function get_current_week() {
		return gmdate( 'o-W' );
	}
}
