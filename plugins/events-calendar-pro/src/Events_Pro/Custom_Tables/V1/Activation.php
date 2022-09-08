<?php
/**
 * Handles the code that should be executed when the plugin is activated or deactivated.
 *
 * @since   6.0.0
 *
 * @package TEC\Events_Pro\Custom_Tables\V1
 */

namespace TEC\Events_Pro\Custom_Tables\V1;

use TEC\Events\Custom_Tables\V1\Schema_Builder\Schema_Builder;
use TEC\Events_Pro\Custom_Tables\V1\Events\Provisional\Provider as Provisional_Post_Provider;
use TEC\Events_Pro\Custom_Tables\V1\Tables\Provider as Tables_Provider;

/**
 * Class Activation
 *
 * @since   6.0.0
 *
 * @package TEC\Events_Pro\Custom_Tables\V1
 */
class Activation {
	/**
	 * The name of the transient that will be used to flag whether the plugin did activate
	 * or not.
	 *
	 * @since 6.0.0
	 */
	const ACTIVATION_TRANSIENT = 'tec_pro_custom_tables_v1_initialized';

	/**
	 * Handles the activation of the feature functions.
	 *
	 * @since 6.0.0
	 */
	public static function activate() {
		// Delete the transient to make sure the activation code will run again.
		delete_transient( self::ACTIVATION_TRANSIENT );

		// Transient will still be found, ensure it is truthy false.
		wp_cache_set( self::ACTIVATION_TRANSIENT, null, 'options' );

		set_transient( \Tribe__Events__Rewrite::KEY_DELAYED_FLUSH_REWRITE_RULES, 1 );

		flush_rewrite_rules();

		// Bail when Common is not loaded.
		if ( ! function_exists( 'tribe_register_provider' ) ) {
			return;
		}

		// Register the provider to add the required schemas.
		tribe_register_provider( Tables_Provider::class );

		self::init();
	}

	/**
	 * Initializes the custom tables required by the feature to work.
	 *
	 * This method will run once a day (using transients) and is idem-potent
	 * in the context of the same day.
	 *
	 * @since 6.0.0
	 */
	public static function init() {
		$initialized = get_transient( self::ACTIVATION_TRANSIENT );

		if ( $initialized ) {
			return;
		}

		set_transient( self::ACTIVATION_TRANSIENT, 1, DAY_IN_SECONDS );

		$services = tribe();
		$schema_builder = $services->make( Schema_Builder::class );

		// Sync any schema changes we may have, based on the existence of TEC tables.
		if ( $schema_builder->all_tables_exist( 'tec' ) ) {
			$schema_builder->up();
		}

		if ( tribe()->getVar( 'ct1_fully_activated' ) ) {
			/**
			 * On new installations the full activation code will find an empty state and
			 * will have not activated at this point, do it now if required.
			 */
			tribe()->register( Full_Activation_Provider::class );
		}

		// Set up the provisional post ID base.
		$services->register( Provisional_Post_Provider::class );
		$services->make( Provisional_Post_Provider::class )->on_activation();
	}

	/**
	 * Handles the feature deactivation.
	 *
	 * @since 6.0.0
	 */
	public static function deactivate() {
		$services = tribe();

		// @todo Do we want to drop tables here?

		$services->make( Provisional_Post_Provider::class )->on_deactivation();
	}
}
