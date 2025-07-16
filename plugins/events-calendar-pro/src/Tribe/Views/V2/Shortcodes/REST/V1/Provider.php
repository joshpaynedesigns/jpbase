<?php
/**
 * Shortcode REST API provider.
 *
 * @since 7.2.0
 */

namespace Tribe\Events\Pro\Views\V2\Shortcodes\REST\V1;

use TEC\Events\Custom_Tables\V1\Provider_Contract;
use TEC\Common\Contracts\Service_Provider;

/**
 * Class Provider
 *
 * @since   6.0.0
 *
 * @package TEC\Events_Pro\Custom_Tables\V1\WP_Query
 */
class Provider extends Service_Provider implements Provider_Contract {

	/**
	 * Registers the implementations and filters required by the plugin
	 * to integrate with Custom Tables Queries.
	 *
	 * @since 7.2.0
	 */
	public function register() {
		$this->container->singleton( Calender_Embed::class );

		add_action( 'rest_api_init', $this->container->callback( Calender_Embed::class, 'register' ) );
	}

	/**
	 * Unregisters the implementations and filters required by the plugin
	 * to integrate with Custom Tables Queries.
	 *
	 * @since 7.2.0
	 */
	public function unregister() {
		remove_action( 'rest_api_init', $this->container->callback( Calender_Embed::class, 'register' ) );
	}
}
