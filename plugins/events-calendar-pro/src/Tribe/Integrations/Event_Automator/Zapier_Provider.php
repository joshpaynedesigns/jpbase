<?php
/**
 * The Zapier service provider.
 *
 * @since 7.0.0
 * @package Tribe\Events\Pro\Integrations\Event_Automator
 */

namespace Tribe\Events\Pro\Integrations\Event_Automator;

use TEC\Common\Contracts\Service_Provider;
use TEC\Event_Automator\Zapier\REST\V1\Endpoints\Actions\Create_Events;
use TEC\Event_Automator\Zapier\REST\V1\Endpoints\Actions\Update_Events;
use TEC\Event_Automator\Zapier\REST\V1\Endpoints\Actions\Find_Events;
use TEC\Event_Automator\Zapier\REST\V1\Endpoints\Canceled_Events;
use TEC\Event_Automator\Zapier\REST\V1\Endpoints\New_Events;
use TEC\Event_Automator\Zapier\REST\V1\Endpoints\Updated_Events;
use TEC\Event_Automator\Zapier\Settings;

/**
 * Class Zapier_Provider
 *
 * @since 7.0.0
 *
 * @package Tribe\Events\Pro\Integrations\Event_Automator
 */
class Zapier_Provider extends Service_Provider {
	/**
	 * Binds and sets up implementations.
	 *
	 * @since 7.0.0
	 */
	public function register() {
		if ( ! self::is_active() ) {
			return;
		}

		// Requires single instance to use the same API class through the call.
		$this->container->singleton( Create_Events::class );
		$this->container->singleton( Find_Events::class );
		$this->container->singleton( Update_Events::class );

		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * Returns whether the event status should register, thus activate, or not.
	 *
	 * @since 7.0.0
	 *
	 * @return bool Whether the event status should register or not.
	 */
	public static function is_active() {
		return \TEC\Event_Automator\Zapier\Zapier_Provider::is_active();
	}

	/**
	 * Adds the actions required for event status.
	 *
	 * @since 7.0.0
	 */
	protected function add_actions() {
		add_action( 'rest_api_init', [ $this, 'register_endpoints' ] );

		// Add endpoints to settings dashboard.
		add_action( 'admin_init', [ $this, 'add_endpoints_to_dashboard' ] );
	}

	/**
	 * Adds the filters required by Zapier.
	 *
	 * @since 7.0.0
	 */
	protected function add_filters() {
		add_filter( 'tec_settings_gmaps_js_api_start', [ $this, 'filter_tec_integrations_tab_fields' ] );
		add_filter( 'rest_pre_dispatch', [ $this, 'pre_dispatch_verification_for_create_events' ], 10, 3 );
		add_filter( 'rest_pre_dispatch', [ $this, 'pre_dispatch_verification_for_update_events' ], 10, 3 );
		add_filter( 'rest_request_before_callbacks', [ $this, 'modify_rest_api_params_before_validation' ], 1, 3 );
		add_filter( 'rest_request_before_callbacks', [ $this, 'modify_rest_api_params_before_validation_for_update_events' ], 1, 3 );
	}

	/**
	 * Registers the REST API endpoints for Zapier
	 *
	 * @since 7.0.0
	 */
	public function register_endpoints() {
		$this->container->make( Canceled_Events::class )->register();
		$this->container->make( New_Events::class )->register();
		$this->container->make( Updated_Events::class )->register();
		$this->container->make( Create_Events::class )->register();
		$this->container->make( Update_Events::class )->register();
		$this->container->make( Find_Events::class )->register();
	}

	/**
	 * Adds the endpoint to the Zapier endpoint dashboard filter.
	 *
	 * @since 7.0.0
	 */
	public function add_endpoints_to_dashboard() {
		$this->container->make( New_Events::class )->add_to_dashboard();
		$this->container->make( Canceled_Events::class )->add_to_dashboard();
		$this->container->make( Updated_Events::class )->add_to_dashboard();
		$this->container->make( Create_Events::class )->add_to_dashboard();
		$this->container->make( Update_Events::class )->add_to_dashboard();
		$this->container->make( Find_Events::class )->add_to_dashboard();
	}

	/**
	 * Filters the fields in the Events > Settings > Integrations tab to Zapier settings.
	 *
	 * @since 7.0.0 Migrated to Common from Event Automator
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
	 * @since 6.0.0 Migrated to Common from Event Automator
	 * @since 7.0.1 Migrated from Common to Events Calendar Pro
	 *
	 * @param mixed           $result  Response to replace the requested version with. Can be anything
	 *                                 a normal endpoint can return, or null to not hijack the request.
	 * @param WP_REST_Server  $server  Server instance.
	 * @param WP_REST_Request $request Request used to generate the response.
	 *
	 * @return null Will always return null, failure will happen on the can_create permission check.
	 */
	public function pre_dispatch_verification_for_create_events( $result, $server, $request ) {
		return $this->container->make( Create_Events::class )->pre_dispatch_verification( $result, $server, $request );
	}

	/**
	 * Verify token and login user before dispatching the request.
	 * Done on `rest_pre_dispatch` to be able to set current user to pass validation capability checks.
	 *
	 * @since 6.0.0 Migrated to Common from Event Automator
	 * @since 7.0.1 Migrated from Common to Events Calendar Pro
	 *
	 * @param mixed           $result  Response to replace the requested version with. Can be anything
	 *                                 a normal endpoint can return, or null to not hijack the request.
	 * @param WP_REST_Server  $server  Server instance.
	 * @param WP_REST_Request $request Request used to generate the response.
	 *
	 * @return null Will always return null, failure will happen on the can_create permission check.
	 */
	public function pre_dispatch_verification_for_update_events( $result, $server, $request ) {
		return $this->container->make( Update_Events::class )->pre_dispatch_verification( $result, $server, $request );
	}

	/**
	 * Modifies REST API comma seperated  parameters before validation.
	 *
	 * @since 6.0.0 Migrated to Common from Event Automator
	 * @since 7.0.1 Migrated from Common to Events Calendar Pro
	 *
	 * @param WP_REST_Response|WP_Error $response Response to replace the requested version with. Can be anything
	 *                                            a normal endpoint can return, or a WP_Error if replacing the
	 *                                            response with an error.
	 * @param WP_REST_Server            $server   ResponseHandler instance (usually WP_REST_Server).
	 * @param WP_REST_Request           $request  Request used to generate the response.
	 *
	 * @return WP_REST_Response|WP_Error The response.
	 */
	public function modify_rest_api_params_before_validation( $response, $server, $request ) {
		return $this->container->make( Create_Events::class )->modify_rest_api_params_before_validation( $response, $server, $request );
	}

	/**
	 * Modifies REST API comma seperated  parameters before validation.
	 *
	 * @since 6.0.0 Migrated to Common from Event Automator
	 * @since 7.0.1 Migrated from Common to Events Calendar Pro
	 *
	 * @param WP_REST_Response|WP_Error $response Response to replace the requested version with. Can be anything
	 *                                            a normal endpoint can return, or a WP_Error if replacing the
	 *                                            response with an error.
	 * @param WP_REST_Server            $server   ResponseHandler instance (usually WP_REST_Server).
	 * @param WP_REST_Request           $request  Request used to generate the response.
	 *
	 * @return WP_REST_Response|WP_Error The response.
	 */
	public function modify_rest_api_params_before_validation_for_update_events( $response, $server, $request ) {
		return $this->container->make( Update_Events::class )->modify_rest_api_params_before_validation( $response, $server, $request );
	}
}
