<?php
/**
 * Handles ECP integration with other premium plugins.
 *
 * @since   6.0.0
 *
 * @package TEC\Events_Pro\Custom_Tables\V1\Integrations
 */

namespace TEC\Events_Pro\Custom_Tables\V1\Integrations;

use tad_DI52_ServiceProvider as Service_Provider;

/**
 * Class Provider
 *
 * @since   6.0.0
 *
 * @package TEC\Events_Pro\Custom_Tables\V1\Integrations
 */
class Provider extends Service_Provider {
	/**
	 * Registers, if required, the plugin integration with other premium plugins.
	 *
	 * @since 6.0.0
	 */
	public function register() {
		// Class defined by The Events Calendar: Filter Bar plugin.
		if ( class_exists( '\\TEC\\Filter_Bar\\Custom_Tables\\V1\\Provider' ) ) {
			$this->container->register( \TEC\Filter_Bar\Custom_Tables\V1\Provider::class );
		}

		// Class defined by The Events Calendar: Community Events plugin.
		if ( class_exists( '\\TEC\\Community_Events\\Custom_Tables\\V1\\Provider' ) ) {
			$this->container->register( \TEC\Events_Community\Custom_Tables\V1\Provider::class );
		}
	}
}
