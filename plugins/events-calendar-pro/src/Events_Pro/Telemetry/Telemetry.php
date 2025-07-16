<?php
/**
 * Class that handles interfacing with TEC\Common\Telemetry.
 *
 * @since 6.1.0
 *
 * @package TEC\Events_Pro\Telemetry
 */

namespace TEC\Events_Pro\Telemetry;

/**
 * Class Telemetry.
 *
 * @since 6.1.0

 * @package TEC\Events_Pro\Telemetry
 */
class Telemetry {

	/**
	 * The Telemetry plugin slug for The Events Calendar Pro.
	 *
	 * @since 6.1.0
	 *
	 * @var string
	 */
	protected static $plugin_slug = 'events-calendar-pro';

	/**
	 * The "plugin path" for The Events Calendar Pro main file.
	 *
	 * @since 6.1.0
	 *
	 * @var string
	 */
	protected static $plugin_path = 'events-calendar-pro.php';

	/**
	 * Adds The Events Calendar to the list of plugins to be opted in/out alongside tribe-common.
	 *
	 * @since 6.1.0
	 *
	 * @param array<string,string> $slugs The default array of slugs in the format [ 'plugin_slug' => 'plugin_path' ].
	 *
	 * @see \TEC\Common\Telemetry\Telemetry::get_tec_telemetry_slugs()
	 *
	 * @return array<string,string> $slugs The same array with The Events Calendar added to it.
	 */
	public function filter_tec_telemetry_slugs( $slugs ) {
		$dir                         = trailingslashit( basename( EVENTS_CALENDAR_PRO_DIR ) );
		$slugs[ self::$plugin_slug ] = $dir . self::$plugin_path;

		return array_unique( $slugs, SORT_STRING );
	}

	/**
	 * Collects telemetry data about recurrence and exclusion rules.
	 * This method will be used to gather metrics about how users are configuring
	 * recurring events and exclusions.
	 *
	 * @since 7.5.0
	 *
	 * @return array<string,mixed> The recurrence and exclusion telemetry data.
	 */
	public function collect_recurrence_telemetry() {
		// Check for cached data first.
		$cache_key   = 'tec_events_pro_recurrence_telemetry_data';
		$cached_data = get_transient( $cache_key );

		if ( false !== $cached_data ) {
			return $cached_data;
		}

		global $wpdb;

		// Initialize data structure to hold our metrics.
		$data = [
			'recurring_events_count'            => 0,
			'single_events_count'               => 0,
			'avg_rules_per_event'               => 0,
			'events_with_exclusions'            => 0,
			'avg_exclusions_per_event'          => 0,
			'is_using_exclusion_rules'          => false,

			// Pattern counts - recurrence.
			'recurrence_pattern_date'           => 0,
			'recurrence_pattern_daily'          => 0,
			'recurrence_pattern_weekly'         => 0,
			'recurrence_pattern_monthly'        => 0,
			'recurrence_pattern_yearly'         => 0,
			'recurrence_pattern_other'          => 0,

			// Pattern counts - exclusions.
			'exclusion_pattern_date'            => 0,
			'exclusion_pattern_daily'           => 0,
			'exclusion_pattern_weekly'          => 0,
			'exclusion_pattern_monthly'         => 0,
			'exclusion_pattern_yearly'          => 0,
			'exclusion_pattern_other'           => 0,

			// Distribution data - will be serialized to JSON strings.
			'rules_per_event_distribution'      => [],
			'exclusions_per_event_distribution' => [],
			'recurrence_types_distribution'     => [],
			'exclusion_types_distribution'      => [],
		];

		// Get all events with recurrence meta.
		$events_with_recurrence = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare(
				"SELECT p.ID, pm.meta_value
				FROM {$wpdb->posts} p
				INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
				WHERE p.post_type = %s
				AND pm.meta_key = '_EventRecurrence'
				AND p.post_parent = 0
				LIMIT 1000", // Limit to prevent excessive processing.
				\Tribe__Events__Main::POSTTYPE
			)
		);

		if ( empty( $events_with_recurrence ) ) {
			// Cache empty result for 1 day.
			set_transient( $cache_key, $data, DAY_IN_SECONDS );
			return $data;
		}

		// Process each event.
		foreach ( $events_with_recurrence as $event ) {
			$recurrence_meta = maybe_unserialize( $event->meta_value );

			// Count if it's a recurring event or a single event with recurrence meta.
			if ( ! empty( $recurrence_meta['rules'] ) ) {
				++$data['recurring_events_count'];

				// Count number of rules per event.
				$rules_count = count( $recurrence_meta['rules'] );
				if ( ! isset( $data['rules_per_event_distribution'][ $rules_count ] ) ) {
					$data['rules_per_event_distribution'][ $rules_count ] = 0;
				}
				++$data['rules_per_event_distribution'][ $rules_count ];

				// Record rule types.
				foreach ( $recurrence_meta['rules'] as $rule ) {
					$rule_type   = $rule['type'] ?? 'unknown';
					$custom_type = '';

					if ( $rule_type === 'Custom' && isset( $rule['custom']['type'] ) ) {
						$custom_type = $rule['custom']['type'];
						$rule_type   = $custom_type;
					}

					if ( ! isset( $data['recurrence_types_distribution'][ $rule_type ] ) ) {
						$data['recurrence_types_distribution'][ $rule_type ] = 0;
					}
					++$data['recurrence_types_distribution'][ $rule_type ];

					// Count pattern types.
					if ( $custom_type === 'Date' || $rule_type === 'Date' ) {
						++$data['recurrence_pattern_date'];
					} elseif ( $custom_type === 'Daily' || $rule_type === 'Daily' ) {
						++$data['recurrence_pattern_daily'];
					} elseif ( $custom_type === 'Weekly' || $rule_type === 'Weekly' ) {
						++$data['recurrence_pattern_weekly'];
					} elseif ( $custom_type === 'Monthly' || $rule_type === 'Monthly' ) {
						++$data['recurrence_pattern_monthly'];
					} elseif ( $custom_type === 'Yearly' || $rule_type === 'Yearly' ) {
						++$data['recurrence_pattern_yearly'];
					} else {
						++$data['recurrence_pattern_other'];
					}
				}
			} else {
				++$data['single_events_count'];
			}

			// Process exclusions.
			if ( ! empty( $recurrence_meta['exclusions'] ) ) {
				++$data['events_with_exclusions'];
				$data['is_using_exclusion_rules'] = true;

				// Count number of exclusions per event.
				$exclusions_count = count( $recurrence_meta['exclusions'] );
				if ( ! isset( $data['exclusions_per_event_distribution'][ $exclusions_count ] ) ) {
					$data['exclusions_per_event_distribution'][ $exclusions_count ] = 0;
				}
				++$data['exclusions_per_event_distribution'][ $exclusions_count ];

				// Record exclusion types.
				foreach ( $recurrence_meta['exclusions'] as $exclusion ) {
					$exclusion_type = $exclusion['type'] ?? 'unknown';
					$custom_type    = '';

					if ( $exclusion_type === 'Custom' && isset( $exclusion['custom']['type'] ) ) {
						$custom_type    = $exclusion['custom']['type'];
						$exclusion_type = $custom_type;
					}

					if ( ! isset( $data['exclusion_types_distribution'][ $exclusion_type ] ) ) {
						$data['exclusion_types_distribution'][ $exclusion_type ] = 0;
					}
					++$data['exclusion_types_distribution'][ $exclusion_type ];

					// Count pattern types.
					if ( $custom_type === 'Date' || $exclusion_type === 'Date' ) {
						++$data['exclusion_pattern_date'];
					} elseif ( $custom_type === 'Daily' || $exclusion_type === 'Daily' ) {
						++$data['exclusion_pattern_daily'];
					} elseif ( $custom_type === 'Weekly' || $exclusion_type === 'Weekly' ) {
						++$data['exclusion_pattern_weekly'];
					} elseif ( $custom_type === 'Monthly' || $exclusion_type === 'Monthly' ) {
						++$data['exclusion_pattern_monthly'];
					} elseif ( $custom_type === 'Yearly' || $exclusion_type === 'Yearly' ) {
						++$data['exclusion_pattern_yearly'];
					} else {
						++$data['exclusion_pattern_other'];
					}
				}
			}
		}

		// Calculate averages.
		if ( $data['recurring_events_count'] > 0 ) {
			$total_rules = 0;
			foreach ( $data['rules_per_event_distribution'] as $count => $events ) {
				$total_rules += $count * $events;
			}
			$data['avg_rules_per_event'] = $total_rules / $data['recurring_events_count'];
		}

		if ( $data['events_with_exclusions'] > 0 ) {
			$total_exclusions = 0;
			foreach ( $data['exclusions_per_event_distribution'] as $count => $events ) {
				$total_exclusions += $count * $events;
			}
			$data['avg_exclusions_per_event'] = $total_exclusions / $data['events_with_exclusions'];
		}

		// Convert array distributions to JSON strings.
		$data['rules_per_event_distribution']      = wp_json_encode( $data['rules_per_event_distribution'] );
		$data['exclusions_per_event_distribution'] = wp_json_encode( $data['exclusions_per_event_distribution'] );
		$data['recurrence_types_distribution']     = wp_json_encode( $data['recurrence_types_distribution'] );
		$data['exclusion_types_distribution']      = wp_json_encode( $data['exclusion_types_distribution'] );

		// Cache the data for one week.
		set_transient( $cache_key, $data, WEEK_IN_SECONDS );

		return $data;
	}
}
