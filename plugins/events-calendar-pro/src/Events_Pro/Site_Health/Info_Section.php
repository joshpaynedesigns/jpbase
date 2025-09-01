<?php
/**
 * Class that handles interfacing with core Site Health.
 *
 * @since 6.1.0
 *
 * @package TEC\Events_Pro\Site_Health
 */

namespace TEC\Events_Pro\Site_Health;

use TEC\Common\Site_Health\Info_Section_Abstract;
use TEC\Common\Site_Health\Factory;
use TEC\Events\Custom_Tables\V1\Tables\Occurrences;
use TEC\Events_Pro\Custom_Tables\V1\Series\Post_Type as Series_Post_Type;
use TEC\Events_Pro\Telemetry\Telemetry;
use Tribe__Events__Google__Maps_API_Key;
use Tribe__Events__Main as TEC;

/**
 * Class Site_Health
 *
 * @since 6.1.0
 *
 * @package TEC\Events_Pro\Site_Health
 */
class Info_Section extends Info_Section_Abstract {
	/**
	 * Slug for the section.
	 *
	 * @since 6.1.0
	 *
	 * @var string $slug
	 */
	protected static string $slug = 'events-calendar-pro';

	/**
	 * Label for the section.
	 *
	 * @since 6.1.0
	 *
	 * @var string $label
	 */
	protected string $label;

	/**
	 * If we should show the count of fields in the site health info page.
	 *
	 * @since 6.1.0
	 *
	 * @var bool $show_count
	 */
	protected bool $show_count = false;

	/**
	 * If this section is private.
	 *
	 * @since 6.1.0
	 *
	 * @var bool $is_private
	 */
	protected bool $is_private = false;

	/**
	 * Description for the section.
	 *
	 * @since 6.1.0
	 *
	 * @var string $description
	 */
	protected string $description;

	/**
	 * Info_Section constructor.
	 *
	 * @since 6.1.0
	 */
	public function __construct() {
		$this->label       = esc_html__( 'The Events Calendar PRO', 'tribe-events-calendar-pro' );
		$this->description = esc_html__( 'This section contains information on the Events Calendar PRO Plugin.', 'tribe-events-calendar-pro' );
		$this->add_fields();
	}

	/**
	 * Adds our default section to the Site Health Info tab.
	 *
	 * @since 6.1.0
	 */
	public function add_fields() {
		global $wpdb;
		// Try to make sure the post type is registered.
		tribe( Series_Post_Type::class )->register_post_type_or_fail();

		$this->add_field(
			Factory::generate_post_status_count_field(
				'series_counts',
				Series_Post_Type::POSTTYPE,
				10
			)
		);

		if ( tribe()->getVar( 'ct1_fully_activated' ) ) {
			// Custom Tables v1 code.
			$occurrences      = Occurrences::table_name( true );
			$recurring_select = "SELECT COUNT( DISTINCT( post_id ) ) FROM $occurrences
				WHERE has_recurrence = 1";
		} else {
			// Legacy code.
			$recurring_select = $wpdb->prepare(
				"SELECT COUNT( DISTINCT `post_parent` ) FROM $wpdb->posts WHERE `post_type` = %s AND `post_parent` != ''",
				TEC::POSTTYPE
			);
		}
		$recurring_events = $wpdb->get_var( $recurring_select ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery

		$this->add_field(
			Factory::generate_generic_field(
				'recurring_events',
				esc_html__( 'Unique recurring count', 'tribe-events-calendar-pro' ),
				$recurring_events,
				20
			)
		);

		$mobile_view = tribe_get_mobile_default_view();

		$this->add_field(
			Factory::generate_generic_field(
				'default_mobile_view',
				esc_html__( 'Default mobile view', 'tribe-events-calendar-pro' ),
				$mobile_view,
				30
			)
		);

		$this->add_field(
			Factory::generate_generic_field(
				'custom_field_count',
				esc_html__( 'Custom field count', 'tribe-events-calendar-pro' ),
				count( tribe_get_option( 'custom-fields', [] ) ),
				40
			)
		);

		$this->add_field(
			Factory::generate_generic_field(
				'google_maps_custom_key',
				esc_html__( 'Using custom Google Maps key', 'tribe-events-calendar-pro' ),
				tec_bool_to_string( tribe_get_option( 'google_maps_js_api_key' ) !== Tribe__Events__Google__Maps_API_Key::$default_api_key ),
				50
			)
		);

		// Add recurrence telemetry data fields.
		$telemetry       = tribe( Telemetry::class );
		$recurrence_data = $telemetry->collect_recurrence_telemetry();

		// Add the exclusion rules usage indicator.
		$this->add_field(
			Factory::generate_generic_field(
				'is_using_exclusion_rules',
				esc_html__( 'Using Exclusion Rules', 'tribe-events-calendar-pro' ),
				tec_bool_to_string( (string) $recurrence_data['is_using_exclusion_rules'] ),
				60
			)
		);

		// Start ordering from 100 to place them after existing fields.
		$this->add_field(
			Factory::generate_generic_field(
				'events_with_exclusions',
				esc_html__( 'Events with exclusions', 'tribe-events-calendar-pro' ),
				$recurrence_data['events_with_exclusions'],
				100
			)
		);

		$recurrence_pattern_counts = [
			'date'    => $recurrence_data['recurrence_pattern_date'],
			'daily'   => $recurrence_data['recurrence_pattern_daily'],
			'weekly'  => $recurrence_data['recurrence_pattern_weekly'],
			'monthly' => $recurrence_data['recurrence_pattern_monthly'],
			'yearly'  => $recurrence_data['recurrence_pattern_yearly'],
			'other'   => $recurrence_data['recurrence_pattern_other'],
		];

		$this->add_field(
			Factory::generate_generic_field(
				'recurrence_patterns',
				esc_html__( 'Recurrence pattern usage', 'tribe-events-calendar-pro' ),
				$recurrence_pattern_counts,
				110
			)
		);

		$exclusion_pattern_counts = [
			'date'    => $recurrence_data['exclusion_pattern_date'],
			'daily'   => $recurrence_data['exclusion_pattern_daily'],
			'weekly'  => $recurrence_data['exclusion_pattern_weekly'],
			'monthly' => $recurrence_data['exclusion_pattern_monthly'],
			'yearly'  => $recurrence_data['exclusion_pattern_yearly'],
			'other'   => $recurrence_data['exclusion_pattern_other'],
		];

		$this->add_field(
			Factory::generate_generic_field(
				'exclusion_patterns',
				esc_html__( 'Exclusion pattern usage', 'tribe-events-calendar-pro' ),
				$exclusion_pattern_counts,
				120
			)
		);

		// Get the average rules per event.
		$this->add_field(
			Factory::generate_generic_field(
				'avg_rules_per_event',
				esc_html__( 'Average rules per recurring event', 'tribe-events-calendar-pro' ),
				number_format( $recurrence_data['avg_rules_per_event'], 2 ),
				130
			)
		);

		// Get the average exclusions per event.
		$this->add_field(
			Factory::generate_generic_field(
				'avg_exclusions_per_event',
				esc_html__( 'Average exclusions per event with exclusions', 'tribe-events-calendar-pro' ),
				number_format( $recurrence_data['avg_exclusions_per_event'], 2 ),
				140
			)
		);

		// Calculate most common recurrence type.
		$recurrence_types = ! is_array( $recurrence_data['recurrence_types_distribution'] )
			? json_decode( $recurrence_data['recurrence_types_distribution'], true )
			: $recurrence_data['recurrence_types_distribution'];

		$most_common_recurrence = ! empty( $recurrence_types )
			? array_search( max( $recurrence_types ), $recurrence_types )
			: esc_html__( 'None', 'tribe-events-calendar-pro' );

		$this->add_field(
			Factory::generate_generic_field(
				'most_common_recurrence',
				esc_html__( 'Most common recurrence type', 'tribe-events-calendar-pro' ),
				ucfirst( $most_common_recurrence ),
				150
			)
		);

		// Calculate most common exclusion type.
		$exclusion_types = ! is_array( $recurrence_data['exclusion_types_distribution'] )
			? json_decode( $recurrence_data['exclusion_types_distribution'], true )
			: $recurrence_data['exclusion_types_distribution'];

		$most_common_exclusion = ! empty( $exclusion_types )
			? array_search( max( $exclusion_types ), $exclusion_types )
			: esc_html__( 'None', 'tribe-events-calendar-pro' );

		$this->add_field(
			Factory::generate_generic_field(
				'most_common_exclusion',
				esc_html__( 'Most common exclusion type', 'tribe-events-calendar-pro' ),
				ucfirst( $most_common_exclusion ),
				160
			)
		);
	}
}
