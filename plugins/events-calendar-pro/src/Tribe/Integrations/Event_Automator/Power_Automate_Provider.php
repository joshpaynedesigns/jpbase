<?php
/**
 * The Power Automate service provider.
 *
 * @since 7.0.3
 * @package TEC\Event_Automator\Power_Automate
 */

namespace Tribe\Events\Pro\Integrations\Event_Automator;

use TEC\Common\Contracts\Service_Provider;

use TEC\Event_Automator\Power_Automate\REST\V1\Endpoints\Actions\Create_Events;
use TEC\Event_Automator\Power_Automate\REST\V1\Endpoints\Queue\New_Events;
use TEC\Event_Automator\Power_Automate\REST\V1\Endpoints\Queue\Updated_Events;
use TEC\Event_Automator\Power_Automate\REST\V1\Endpoints\Queue\Canceled_Events;
use TEC\Event_Automator\Power_Automate\Settings;

/**
 * Class Power_Automate_Provider
 *
 * @since 7.0.3
 *
 * @package TEC\Event_Automator\Power_Automate
 */
class Power_Automate_Provider extends Service_Provider {
	/**
	 * Binds and sets up implementations.
	 *
	 * @since 7.0.3
	 */
	public function register() {
		if ( ! self::is_active() ) {
			return;
		}

		// Requires single instance to use the same API class through the call.
		$this->container->singleton( Create_Events::class );

		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * Returns whether the event status should register, thus activate, or not.
	 *
	 * @since 7.0.3
	 *
	 * @return bool Whether the event status should register or not.
	 */
	public static function is_active() {
		return \TEC\Event_Automator\Power_Automate\Power_Automate_Provider::is_active();
	}

	/**
	 * Adds the actions required for event status.
	 *
	 * @since 7.0.3
	 */
	protected function add_actions() {
		add_action( 'rest_api_init', [ $this, 'register_endpoints' ] );

		// Add endpoints to settings dashboard.
		add_action( 'admin_init', [ $this, 'add_endpoints_to_dashboard' ] );
	}

	/**
	 * Adds the filters required by Power Automate.
	 *
	 * @since 7.0.3
	 */
	protected function add_filters() {
		add_filter( 'tec_settings_gmaps_js_api_start', [ $this, 'filter_tec_integrations_tab_fields' ] );
		add_filter( 'rest_pre_dispatch', [ $this, 'pre_dispatch_verification_for_create_events' ], 10, 3 );
		add_filter( 'rest_request_before_callbacks', [ $this, 'modify_rest_api_params_before_validatio_of_create_events' ], 1, 3 );
	}

	/**
	 * Registers the REST API endpoints for Power Automate.
	 *
	 * @since 7.0.3
	 */
	public function register_endpoints() {
		$this->container->make( Canceled_Events::class )->register();
		$this->container->make( New_Events::class )->register();
		$this->container->make( Updated_Events::class )->register();
		$this->container->make( Create_Events::class )->register();
	}

	/**
	 * Adds the endpoint to the Power Automate endpoint dashboard filter.
	 *
	 * @since 7.0.3
	 */
	public function add_endpoints_to_dashboard() {
		$this->container->make( New_Events::class )->add_to_dashboard();
		$this->container->make( Canceled_Events::class )->add_to_dashboard();
		$this->container->make( Updated_Events::class )->add_to_dashboard();
		$this->container->make( Create_Events::class )->add_to_dashboard();
	}

	/**
	 * Filters the fields in the Events > Settings > Integrations tab to Power Automate settings.
	 *
	 * @since 7.0.3 Migrated from Common to Events Calendar Pro.
	 *
	 * @param array<string,array> $fields The current fields.
	 *
	 * @return array<string,array> The fields, as updated by the settings.
	 */
	public function filter_tec_integrations_tab_fields( $fields ) {
		if ( ! is_array( $fields ) ) {
			return $fields;
		}

		return tribe( Settings::class )->add_fields_tec( $fields );
	}

	/**
	 * Verify token and login user before dispatching the request.
	 * Done on `rest_pre_dispatch` to be able to set current user to pass validation capability checks.
	 *
	 * @since 7.0.3 Migrated from Common to Events Calendar Pro.
	 *
	 * @param mixed           $result  Response to replace the requested version with. Can be anything
	 *                                 a normal endpoint can return, or null to not hijack the request.
	 * @param WP_REST_Server  $server  Server instance.
	 * @param WP_REST_Request $request Request used to generate the response.
	 *
	 * @return null With always return null, failure will happen on the can_create permission check.
	 */
	public function pre_dispatch_verification_for_create_events( $result, $server, $request ) {
		return $this->container->make( Create_Events::class )->pre_dispatch_verification( $result, $server, $request );
	}

	/**
	 * Modifies REST API comma seperated  parameters before validation.
	 *
	 * @since 6.0.0 Migrated to Common from Event Automator
	 *
	 * @param WP_REST_Response|WP_Error $result   Response to replace the requested version with. Can be anything
	 *                                            a normal endpoint can return, or a WP_Error if replacing the
	 *                                            response with an error.
	 * @param WP_REST_Server            $server   ResponseHandler instance (usually WP_REST_Server).
	 * @param WP_REST_Request           $request  Request used to generate the response.
	 *
	 * @return WP_REST_Response|WP_Error The response.
	 */
	public function modify_rest_api_params_before_validatio_of_create_events( $result, $server, $request ) {
		return $this->container->make( Create_Events::class )->modify_rest_api_params_before_validation( $result, $server, $request );
	}
}
