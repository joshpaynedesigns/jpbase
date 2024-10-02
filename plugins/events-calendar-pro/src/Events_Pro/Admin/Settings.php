<?php
/**
 * Settings.
 */

namespace TEC\Events_Pro\Admin;

use Tribe__Field;
use Tribe__Main as Common_Main;
use Tribe__Events__Main as Events__Main;
use TEC\Common\Admin\Entities\Div;
use TEC\Common\Admin\Entities\Field_Wrapper;
use TEC\Common\Admin\Entities\Heading;
use Tribe\Utils\Element_Classes as Classes;

/**
 * Class Settings.
 *
 * This class is used to manage the display settings in the subtab structure.
 *
 * @since   7.0.1
 *
 * @package TEC\Events_Pro\Admin\Settings
 */
class Settings {
	/**
	 * Filter the display settings fields.
	 *
	 * @since 7.0.1
	 *
	 * @param array $fields The current fields.
	 */
	public function tec_events_settings_display_calendar_display_section( $fields ) {
		return Common_Main::array_insert_after_key(
			'tribeDisableTribeBar',
			$fields,
			[
				'hideRelatedEvents'       => [
					'type'            => 'checkbox_bool',
					'label'           => __( 'Hide related events', 'tribe-events-calendar-pro' ),
					'tooltip'         => __( 'Remove related events from the single event view (with classic editor)', 'tribe-events-calendar-pro' ),
					'default'         => false,
					'validation_type' => 'boolean',
				],
				'week_view_hide_weekends' => [
					'type'            => 'checkbox_bool',
					'label'           => __( 'Hide weekends on Week View', 'tribe-events-calendar-pro' ),
					'tooltip'         => __( 'Check this to only show weekdays on Week View. This also affects the Events by Week widget.', 'tribe-events-calendar-pro' ),
					'default'         => false,
					'validation_type' => 'boolean',
				],
			]
		);
	}

	/**
	 * Filter the date time settings fields.
	 *
	 * @since 7.0.1
	 *
	 * @param array $fields The current fields.
	 *
	 * @return array The updated fields.
	 */
	public function filter_tec_events_date_time_settings_section( $fields ): array {
		$sample_date = strtotime( 'January 15 ' . gmdate( 'Y' ) );

		// We add weekDayFormat above, so there are four fields.
		$fields['dateTimeHeaderBlock'] = ( new Div( new Classes( [ 'tec-settings-form__header-block' ] ) ) )->add_children(
			[
				new Heading(
					_x( 'Date & Time', 'Date and Time settings section header', 'the-events-calendar' ),
					2,
					new Classes( [ 'tec-settings-form__section-header' ] )
				),
				// @todo: Need to create a <code> element.
				( new Field_Wrapper(
					new Tribe__Field(
						'tribeEventsDateFormatExplanation',
						[
							'type' => 'html',
							'html' => sprintf(
								/* Translators: %1$s: PHP date function, %2$s: URL to WP knowledgebase. */
								__( '<p>The following four fields accept the date format options available to the PHP %1$s function. <a href="%2$s" target="_blank">Learn how to make your own date format here</a>.</p>', 'the-events-calendar' ),
								'<code>date()</code>',
								'https://wordpress.org/support/article/formatting-date-and-time/'
							),
						]
					)
				) ),
			] );

		return Common_Main::array_insert_after_key(
			'monthAndYearFormat',
			$fields,
			[
				'weekDayFormat' => [
					'type'            => 'text',
					'label'           => __( 'Week day format', 'tribe-events-calendar-pro' ),
					'tooltip'         => sprintf(
						/* Translators: %1$s: Example date */
						esc_html__( 'Enter the format to use for week days. Used when showing days of the week in Week view. Example: %1$s', 'tribe-events-calendar-pro' ),
						gmdate( get_option( 'weekDayFormat', 'D jS' ), $sample_date )
					),
					'default'         => 'D jS',
					'size'            => 'medium',
					'validation_type' => 'not_empty',
				],
			]
		);
	}

	/**
	 * Filter the additional content settings fields.
	 *
	 * @since 7.0.1
	 *
	 * @param array $fields The current fields.
	 *
	 * @return array The updated fields.
	 */
	public function filter_tec_events_additional_content_settings_section( $fields ): array {
		$fields = Common_Main::array_insert_before_key(
			'tribeEventsBeforeHTML',
			$fields,
			[
				'tribeEventsShortcodeBeforeHTML' => [
					'type'            => 'checkbox_bool',
					'label'           => __( 'Enable the Before HTML (below) on shortcodes.', 'tribe-events-calendar-pro' ),
					'tooltip'         => __( 'Check this to show the Before HTML from the text area below on events displayed via shortcode.', 'tribe-events-calendar-pro' ),
					'default'         => false,
					'validation_type' => 'boolean',
				],
			]
		);

		$fields = Common_Main::array_insert_before_key(
			'tribeEventsAfterHTML',
			$fields,
			[
				'tribeEventsShortcodeAfterHTML' => [
					'type'            => 'checkbox_bool',
					'label'           => __( 'Enable the After HTML (below) on shortcodes.', 'tribe-events-calendar-pro' ),
					'tooltip'         => __( 'Check this to show the After HTML from the text area below on events displayed via shortcode.', 'tribe-events-calendar-pro' ),
					'default'         => false,
					'validation_type' => 'boolean',
				],
			]
		);

		return $fields;
	}

	/**
	 * Filter the enable views tooltip.
	 *
	 * @since 7.0.1
	 *
	 * @param string $text The current text.
	 *
	 * @return string The updated text.
	 */
	public function filter_tec_events_settings_display_calendar_enable_views_tooltip( $text ): string {
		if ( tribe_is_using_basic_gmaps_api() ) {
			return $text;
		}

		$text .= sprintf(
			/* Translators: %s:post_type slug for tribe_events. This line starts with a space as this sentence is appended to the existing one. */
			__(
				' Please note that you are using The Events Calendar\'s default Google Maps API key, which will limit the Map View\'s functionality. Visit <a href="edit.php?page=tribe-common&tab=addons&post_type=%s">the Integrations Settings page</a> to learn more and add your own Google Maps API key.',
				'tribe-events-calendar-pro'
			),
			Events__Main::POSTTYPE
		);

		return $text;
	}

	/**
	 * Filter the posts per page tooltip.
	 *
	 * @since 7.0.1
	 *
	 * @return string The tooltip text.
	 */
	public function filter_tec_events_display_calendar_settings_posts_per_page_tooltip(): string {
		return esc_html__(
			'The number of events per page on the List, Photo, and Map Views. Does not affect other views.',
			'tribe-events-calendar-pro'
		);
	}
}
