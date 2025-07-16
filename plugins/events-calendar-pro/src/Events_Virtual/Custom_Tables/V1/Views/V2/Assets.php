<?php
/**
 * Handles registering all Assets for the Custom Tables integration of Events Virtual.
 *
 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
 *
 * @package TEC\Events_Virtual\Custom_Tables\V1\Views\V2
 */

namespace TEC\Events_Virtual\Custom_Tables\V1\Views\V2;

use Tribe\Events\Views\V2\Assets as Event_Assets;
use TEC\Common\Contracts\Service_Provider;
use Tribe__Events__Pro__Main as Pro;

/**
 * Register Assets.
 *
 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
 *
 * @package TEC\Events_Virtual\Custom_Tables\V1\Views\V2
 */
class Assets extends Service_Provider {
	/**
	 * Key for this group of assets.
	 *
	 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
	 *
	 * @var string
	 */
	public static $group_key = 'events-virtual-custom-tables-v1';

	/**
	 * Caches the result of the `should_enqueue_series_single` check.
	 *
	 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
	 *
	 * @var bool
	 */
	private $should_enqueue_series_single;

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
	 */
	public function register() {
		$this->container->singleton( static::class, $this );
		$this->container->singleton( 'tec.virtual.custom-tables.v1.views.v2.assets', $this );

		$plugin = Pro::instance();

		tec_asset(
			$plugin,
			'tec-custom-tables-v1-events-virtual-skeleton',
			'events-virtual-skeleton.css',
			[ 'tribe-events-views-v2-skeleton' ],
			'wp_enqueue_scripts',
			[
				'priority'     => 10,
				'conditionals' => [ $this, 'should_enqueue_series_single' ],
				'groups'       => [ static::$group_key ],
			]
		);

		tec_asset(
			$plugin,
			'tec-custom-tables-v1-events-virtual-full',
			'events-virtual-full.css',
			[ 'tribe-events-views-v2-full' ],
			'wp_enqueue_scripts',
			[
				'priority'     => 10,
				'conditionals' => [
					'operator' => 'AND',
					[ $this, 'should_enqueue_series_single' ],
					[ tribe( Event_Assets::class ), 'should_enqueue_full_styles' ],
				],
				'groups'       => [ static::$group_key ],
			]
		);
	}

	/**
	 * Checks if we should enqueue series single assets for the V2 views.
	 *
	 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
	 *
	 * @return bool Whether the series single assets should be enqueued or not.
	 */
	public function should_enqueue_series_single() {
		if ( null !== $this->should_enqueue_series_single ) {
			return $this->should_enqueue_series_single;
		}

		$should_enqueue = class_exists( '\\TEC\\Events_Pro\\Custom_Tables\\V1\\Series\\Post_Type' )
		                  && is_singular( \TEC\Events_Pro\Custom_Tables\V1\Series\Post_Type::POSTTYPE );

		/**
		 * Allow filtering of whether series single assets should be enqueued or not.
		 *
		 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
		 * @todo [plugin-consolidation] Merge VE into ECP, hook to be deprecated and renamed.
		 *
		 * @param bool $should_enqueue Whether the series single assets should be enqueued or not.
		 */
		$should_enqueue = apply_filters( 'tec_custom_tables_v1_events_virtual_assets_should_enqueue_series_single', $should_enqueue );

		$this->should_enqueue_series_single = $should_enqueue;

		return $should_enqueue;
	}
}
