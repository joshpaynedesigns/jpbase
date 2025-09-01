<?php
/**
 * Handles hiding Event End Time through the display settings.
 *
 * @since   6.0.2
 *
 * @package TEC\Events_Pro\Views
 */

namespace TEC\Events_Pro\Views;

use TEC\Common\Contracts\Service_Provider;

/**
 * Class Provider
 *
 * @since   6.0.2
 *
 * @package TEC\Events_Pro\Views
 */
class Hide_End_Time_Provider extends Service_Provider {
	/**
	 * Registers the handlers and modifiers required.
	 *
	 * @since 6.0.2
	 */
	public function register(): void {
		add_action( 'tec_events_views_v2_hide_end_time_init', [ $this, 'init_hide_end_time' ] );
		add_filter( 'tec_events_display_remove_event_end_time_options', [ $this, 'add_our_options' ] );
		add_filter( 'tec_events_hide_end_time_modifier_defaults', [ $this, 'add_flag_defaults' ] );
	}

	/**
	 * Hook for the hide end time setting to flag the view accordingly.
	 *
	 * @since 6.0.2
	 *
	 * @param Service_Provider $provider The Events Calendar provider that builds the hide flag.
	 */
	public function init_hide_end_time( $provider ): void {
		// Hook to add the flag for photo view template.
		add_action(
			'tribe_template_pre_html:events-pro/v2/photo/event/date-time',
			[ $provider, 'handle_template_hide_end_time' ],
			10,
			4
		);

		// Hook to add the flag for week view template.
		add_action(
			'tribe_template_pre_html:events-pro/v2/week/grid-body/events-day/event/date',
			[ $provider, 'handle_template_hide_end_time' ],
			10,
			4
		);

		// Hook to add the flag for map view template.
		add_action(
			'tribe_template_pre_html:events-pro/v2/map/event-cards/event-card/event/date-time',
			[ $provider, 'handle_template_hide_end_time' ],
			10,
			4
		);

		// Hook to add the flag for summary view template.
		add_action(
			'tribe_template_pre_html:events-pro/v2/summary/date-group/event/date/single',
			[ $provider, 'handle_template_hide_end_time' ],
			10,
			4
		);
	}

	/**
	 * Filter to add our default flags for the Events Calendar Pro views.
	 *
	 * @since 6.0.2
	 *
	 * @param array $defaults The current default flags.
	 *
	 * @return array The modified defaults.
	 */
	public function add_flag_defaults( array $defaults ): array {
		$defaults['map']     = true;
		$defaults['photo']   = true;
		$defaults['summary'] = true;
		$defaults['week']    = true;

		return $defaults;
	}

	/**
	 * Filter to add our Events Calendar Pro view options to the settings page.
	 *
	 * @since 6.0.2
	 *
	 * @param array $options The settings options.
	 *
	 * @return array The options with Events Calendar Pro views added.
	 */
	public function add_our_options( array $options ): array {
		$options['summary']	= esc_html( _x( 'Summary view', 'The option to remove end times for summary view.', 'tribe-events-calendar-pro' ) );
		$options['photo']   = esc_html( _x( 'Photo view', 'The option to remove end times for photo view.', 'tribe-events-calendar-pro' ) );
		$options['map']     = esc_html( _x( 'Map view', 'The option to remove end times for map view.', 'tribe-events-calendar-pro' ) );
		$options['week']    = esc_html( _x( 'Week view', 'The option to remove end times for week view.', 'tribe-events-calendar-pro' ) );

		return $options;
	}
}
