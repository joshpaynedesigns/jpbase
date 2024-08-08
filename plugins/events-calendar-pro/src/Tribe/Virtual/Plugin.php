<?php
/**
 * The main Events Virtual plugin service provider: it bootstraps the plugin code.
 *
 * @since   1.0.0
 *
 * @package Tribe\Events\Virtual
 */

namespace Tribe\Events\Virtual;

use Tribe\Events\Virtual\Export\Export_Provider;
use Tribe__Autoloader;

/**
 * Class Plugin
 *
 * @since   1.0.0
 *
 * @package Tribe\Events\Virtual
 */
class Plugin extends \tad_DI52_ServiceProvider {
	/**
	 * Stores the version for the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const VERSION = Plugin_Register::VERSION;

	/**
	 * Stores the base slug for the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const SLUG = 'events-virtual';

	/**
	 * Stores the base slug for the extension.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const FILE = EVENTS_CALENDAR_PRO_FILE;

	/**
	 * The slug that will be used to identify HTTP requests the plugin should handle.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public static $request_slug = 'ev_request';

	/**
	 * @since 1.0.0
	 *
	 * @var string Plugin Directory.
	 */
	public $plugin_dir;

	/**
	 * @since 1.0.0
	 *
	 * @var string Plugin path.
	 */
	public $plugin_path;

	/**
	 * @since 1.0.0
	 *
	 * @var string Plugin URL.
	 */
	public $plugin_url;

	/**
	 * Where in the themes we will look for templates.
	 *
	 * @since 7.0.0
	 *
	 * @var string
	 */
	public $template_namespace = 'events-virtual';

	/**
	 * Allows this class to be used as a singleton.
	 *
	 * Note this specifically doesn't have a typing, just a type hinting via Docblocks, it helps
	 * avoid problems with deprecation since this is loaded so early.
	 *
	 * @since 1.15.0
	 *
	 * @var \Tribe__Container
	 */
	protected $container;

	/**
	 * Sets the container for the class.
	 *
	 * Note this specifically doesn't have a typing for the container, just a type hinting via Docblocks, it helps
	 * avoid problems with deprecation since this is loaded so early.
	 *
	 * @since 1.14.0
	 *
	 * @param ?\Tribe__Container $container The container to use, if any. If not provided, the global container will be used.
	 *
	 */
	public function set_container( $container = null ): void {
		$this->container = $container ?: tribe();
	}

	/**
	 * Setup the Extension's properties.
	 *
	 * This always executes even if the required plugins are not present.
	 */
	public function register() {
		// Set up the plugin provider properties.
		$this->plugin_path = trailingslashit( dirname( static::FILE ) );
		$this->plugin_dir  = trailingslashit( basename( $this->plugin_path ) );
		$this->plugin_url  = plugins_url( $this->plugin_dir, $this->plugin_path );

		// Include the file that defines the functions handling the plugin load operations.
		require_once EVENTS_CALENDAR_PRO_DIR . '/src/functions/template-tags/virtual.php';

		// Register this provider as the main one and use a bunch of aliases.
		$this->container->singleton( static::class, $this );
		$this->container->singleton( 'events-virtual', $this );
		$this->container->singleton( 'events-virtual.plugin', $this );

		// Start binds.

		$this->container->singleton( Template::class, new Template() );
		$this->container->singleton( Event_Meta::class, Event_Meta::class );
		$this->container->singleton( Metabox::class, Metabox::class );
		$this->container->singleton( JSON_LD::class, JSON_LD::class );
		$this->container->singleton( Template_Modifications::class, Template_Modifications::class );

		// End binds.

		$this->container->register( Hooks::class );
		$this->container->register( Assets::class );
		$this->container->register( Compatibility::class );
		$this->container->register( Export_Provider::class );
		$this->container->register( Rewrite\Rewrite_Provider::class );
		$this->container->register( Context\Context_Provider::class );
		$this->container->register( ORM\ORM_Provider::class );
		$this->container->register( Views\V2\Views_Provider::class );
		$this->container->register( Editor\Provider::class );

		if ( class_exists( '\\TEC\\Events_Virtual\\Custom_Tables\\V1\\Provider' ) ) {
			tribe_register_provider( '\\TEC\\Events_Virtual\\Custom_Tables\\V1\\Provider' );
		}
		if ( class_exists( '\\TEC\\Events_Virtual\\Compatibility\\Event_Automator\\Zapier\\Zapier_Provider' ) ) {
			tribe_register_provider( '\\TEC\\Events_Virtual\\Compatibility\\Event_Automator\\Zapier\\Zapier_Provider' );
		}

		// Load the new third-party integration system.
		tribe_register_provider( \TEC\Events_Virtual\Integrations\Provider::class );
	}

	/**
	 * Register the Tribe Autoloader in Virtual Events.
	 *
	 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
	 * @deprecated 7.0.0 Pro is handling now.
	 */
	protected function register_autoloader(): void {
		_deprecated_function( __METHOD__, '7.0.0' );
	}

	/**
	 * Returns whether meeting and conference support is enabled in the plugin or not.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Whether meeting and conference support is enabled in the plugin or not.
	 */
	public static function meetings_enabled() {
		// Allow meetings to be disabled using a constant.
		$enabled = defined( 'EVENTS_VIRTUAL_MEETINGS_ENABLE' )
			? EVENTS_VIRTUAL_MEETINGS_ENABLE
			: true;

		/**
		 * Filters whether to enable meetings support in the plugin or not.
		 *
		 * @since 1.0.0
		 * @todo [plugin-consolidation] Merge VE into ECP, hook to be deprecated and renamed.
		 *
		 * @param bool $enable_meetings Whether meetings and conference support is enabled in the plugin or not.
		 */
		return apply_filters( 'tribe_events_virtual_meetings_enabled', $enabled );
	}

	/**
	 * Adds template tags once the plugin has loaded.
	 *
	 * @since      1.0.0
	 * @deprecated 7.0.0
	 */
	protected function load_template_tags() {
		_deprecated_function( __METHOD__, '7.0.0' );
	}
}
