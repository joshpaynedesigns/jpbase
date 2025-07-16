<?php
/**
 * The Events Calendar Pro Integration with merge of Event Automator plugin into Pro.
 *
 * @since   7.0.0
 * @package Tribe\Events\Pro\Integrations\Event_Automator
 */

namespace Tribe\Events\Pro\Integrations\Event_Automator;

use TEC\Common\Integrations\Plugin_Merge_Provider_Abstract;
use Tribe__Events__Pro__Main;

/**
 * Class Service_Provider
 *
 * @since   7.0.0
 *
 * @package Tribe\Events\Pro\Integrations\Event_Automator
 */
class Service_Provider extends Plugin_Merge_Provider_Abstract {

	/**
	 * Load the Event Automator framework.
	 *
	 * @since 7.0.0
	 *
	 * @return void
	 */
	public function init_merged_plugin(): void {
		if ( ! class_exists( \TEC\Event_Automator\Plugin::class, true ) ) {
			do_action( 'tribe_log', 'error', __CLASS__, [ 'error' => 'The Event Automator `Plugin` class does not exist.' ] );

			return;
		}

		tribe_register_provider( \TEC\Event_Automator\Plugin::class );

		// Register ECP specific services.
		$this->container->register( Power_Automate_Provider::class );
		$this->container->register( Zapier_Provider::class );

		/**
		 * Fires when Event Automator is fully loaded.
		 *
		 * @since 7.5.0
		 */
		do_action( 'tec_event_automator_fully_loaded' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_plugin_updated_name(): string {
		return sprintf(
			/* translators: %1$s: The plugin version */
			_x(
				'The Events Calendar Pro to %1$s',
				'Plugin name upgraded to version.',
				'tribe-events-calendar-pro'
			),
			Tribe__Events__Pro__Main::VERSION
		);
	}

	/**
	 * @inheritDoc
	 */
	public function get_child_plugin_text_domain(): string {
		return 'event-automator';
	}

	/**
	 * @inheritDoc
	 */
	public function get_merge_notice_slug(): string {
		return 'events-pro-event-automator-merge';
	}

	/**
	 * @inheritDoc
	 */
	public function get_merged_version(): string {
		return '7.0.0-dev';
	}

	/**
	 * @inheritDoc
	 */
	public function get_plugin_file_key(): string {
		return 'event-automator/event-automator.php';
	}

	/**
	 * @inheritDoc
	 */
	public function get_last_version_option_key(): string {
		return 'pro-schema-version';
	}

	/**
	 * @inheritDoc
	 */
	public function get_updated_merge_notice_message(): string {
		return sprintf(
			/* translators: %1$s is the plugin that was deactivated, %2$s is the plugin name, %3$s is the opening anchor tag, %4$s is the closing anchor tag. */
			_x(
				'%1$s has been deactivated as it\'s now bundled into %2$s. %3$sLearn More%4$s.',
				'Notice message for the forced deactivation of the Event Automator plugin after updating Events Pro to the merged version.',
				'tribe-events-calendar-pro'
			),
			'Event Automator',
			'The Events Calendar Pro',
			'<a target="_blank" href="https://evnt.is/1bdy">',
			'</a>'
		);
	}

	/**
	 * @inheritDoc
	 */
	public function get_activating_merge_notice_message(): string {
		return sprintf(
			/* translators: %1$s: Event Automator, %2$s: Event Automator, %3$s: he Events Calendar Pro, %4$s: Open anchor tag to the learn more page, %5$s: Closing tag. */
			_x(
				'%1$s could not be activated. The %1$s functionality has been merged into %2$s. %3$sLearn More%4$s.',
				'Notice message for the forced deactivation of the Event Automator plugin after attempting to activate, and the plugin was merged to Events Pro.',
				'tribe-events-calendar-pro'
			),
			'Event Automator',
			'The Events Calendar Pro',
			'<a target="_blank" href="https://evnt.is/1bdy">',
			'</a>'
		);
	}
}
