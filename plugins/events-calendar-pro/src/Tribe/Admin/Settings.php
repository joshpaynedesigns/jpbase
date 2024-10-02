<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
class Tribe__Events__Pro__Admin__Settings {

	/**
	 * Get (and instantiate, if necessary) the instance of the class
	 *
	 * @static
	 * @return self
	 *
	 */
	public static function instance() {
		return tribe( 'events-pro.admin.settings' );
	}

	/**
	 * Hook the required Methods to the correct filters/actions
	 *
	 * @return void
	 */
	public function hook() {
		add_filter( 'tec_events_settings_display_calendar_display_section', [ $this, 'inject_mobile_fields' ] );
	}

	/**
	 * Filters the Settings Fields to add the mobile fields
	 *
	 * @param array  $settings   An Array for The Events Calendar fields.
	 * @param string $deprecated Deprecated argument.
	 *
	 * @return array
	 */
	public function inject_mobile_fields( $settings, $deprecated = null ) {
		if ( null !== $deprecated ) {
			_deprecated_argument( __METHOD__, '7.0.1', 'The second argument is no longer used.' );
		}

		// Include the fields and replace with the return from the include
		$settings = include Tribe__Events__Pro__Main::instance()->pluginPath . 'src/admin-views/tribe-options-mobile.php';

		return $settings;
	}
}
