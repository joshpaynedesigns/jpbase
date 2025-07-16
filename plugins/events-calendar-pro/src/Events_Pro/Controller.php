<?php
/**
 * Handles the registration and un-registration of the Events_Pro namespaced sub-controllers.
 *
 * @since 7.4.4
 *
 * @package TEC\Events_Pro
 */

namespace TEC\Events_Pro;

use TEC\Common\Contracts\Provider\Controller as Controller_Contract;
use TEC\Events_Pro\Compatibility\Event_Automator\Zapier\Zapier_Provider;
use TEC\Events_Pro\Views\Hide_End_Time_Provider;
use TEC\Events_Pro\Admin\Controller as Admin_Controller;

/**
 * Class Controller - Registers and un-registers the Events_Pro namespaced sub-controllers.
 *
 * @since 7.4.4
 *
 * @package TEC\Events_Pro
 */
class Controller extends Controller_Contract {

	/**
	 * Registers the filters and actions hooks added by the controller.
	 *
	 * @since 7.4.4
	 *
	 * @return void
	 */
	public function do_register(): void {
		// Custom tables v1 implementation.
		if ( class_exists( Custom_Tables\V1\Provider::class ) ) {
			$this->container->register( Custom_Tables\V1\Provider::class );
		}

		// Set up Admin Provider.
		$this->container->register( Admin_Controller::class );

		// Set up SEO Headers.
		$this->container->register( SEO\Headers\Controller::class );

		// Set up Site Health.
		$this->container->register( Site_Health\Provider::class );

		// Set up Telemetry.
		$this->container->register( Telemetry\Provider::class );

		$this->container->register( Linked_Posts\Controller::class );

		// Set up Integrations.
		$this->container->register( Integrations\Controller::class );

		// Site Editor Templates.
		$this->container->register( Block_Templates\Controller::class );

		// Blocks Registration.
		$this->container->register( Blocks\Controller::class );

		if ( class_exists( Zapier_Provider::class ) ) {
			$this->container->register( Zapier_Provider::class );
		}

		// Set up Virtual Events via the compatibility layer.
		$this->container->register( Integrations\Events_Virtual_Provider::class );

		// View modifier for end time.
		$this->container->register( Hide_End_Time_Provider::class );

		$this->init_implementations();
	}

	/**
	 * Un-registers the filters and actions hooks added by the controller.
	 *
	 * @since 7.4.4
	 *
	 * @return void
	 */
	public function unregister(): void {
		// Custom_Tables\V1\Provider has no unregister method.
		$this->container->get( Admin_Controller::class )->unregister();
		$this->container->get( SEO\Headers\Controller::class )->unregister();
		// Site_Health\Provider::class has no unregister method.
		// Telemetry\Provider::class has no unregister method.
		$this->container->get( Linked_Posts\Controller::class )->unregister();
		$this->container->get( Integrations\Controller::class )->unregister();
		$this->container->get( Block_Templates\Controller::class )->unregister();
		$this->container->get( Blocks\Controller::class )->unregister();
		// Zapier_Provider::class has no unregister method.
		// Integrations\Events_Virtual_Provider::class has no unregister method.
		// Hide_End_Time_Provider::class has no unregister method.
	}

	/**
	 * Initializes the implementations.
	 *
	 * @since 7.4.4
	 *
	 * @return void
	 */
	public function init_implementations(): void {
		tribe( 'events-pro.admin.settings' );
		tribe( 'events-pro.ical' );
		tribe( 'events-pro.assets' );
	}
}
