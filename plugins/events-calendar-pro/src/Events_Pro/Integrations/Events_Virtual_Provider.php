<?php
/**
 * The Events Calendar Pro Integration with merge of Events Virtual plugin into Pro.
 *
 * @since   7.0.0
 * @package TEC\Events_Pro\Integrations
 */

namespace TEC\Events_Pro\Integrations;

use TEC\Common\Integrations\Plugin_Merge_Provider_Abstract;
use Tribe__Events__Pro__Main;

/**
 * Class Events_Virtual_Provider
 *
 * @since   7.0.0
 *
 * @package TEC\Events_Pro\Integrations
 */
class Events_Virtual_Provider extends Plugin_Merge_Provider_Abstract {

	/**
	 * Load the Virtual Events framework.
	 *
	 * @since 7.0.0
	 *
	 * @return void
	 */
	public function init_merged_plugin(): void {
		// Include the file that defines the constants for the plugin. Don't add more!
		require_once EVENTS_CALENDAR_PRO_DIR . '/src/deprecated/constants.php';

		// Include the file that defines the functions handling the plugin load operations.
		require_once EVENTS_CALENDAR_PRO_DIR . '/src/functions/template-tags/virtual.php';

		tribe_events_virtual_load();

		/**
		 * Fires when Events Virtual is fully loaded.
		 *
		 * @since 7.5.0
		 */
		do_action( 'tec_events_virtual_fully_loaded' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_plugin_updated_name(): string {
		return sprintf(
			/* Translators: %1$s: The new plugin version */
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
	public function get_merge_notice_slug(): string {
		return 'events-pro-virtual-events-merge';
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
		return 'events-virtual/events-virtual.php';
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
	public function get_child_plugin_text_domain(): string {
		return 'events-virtual';
	}

	/**
	 * @inheritDoc
	 */
	public function get_updated_merge_notice_message(): string {
		return sprintf(
			/* Translators: %1$s is the plugin that was deactivated, %2$s is the plugin name, %3$s is the opening anchor tag, %4$s is the closing anchor tag. */
			_x(
				'%1$s has been deactivated as it\'s now bundled into %2$s. %3$sLearn More%4$s.',
				'Notice message for the forced deactivation of the Virtual Events plugin after updating Events Pro to the merged version.',
				'tribe-events-calendar-pro'
			),
			'Virtual Events',
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
			/* Translators: %1$s: Virtual Events, %2$s: Virtual Events, %3$s: he Events Calendar Pro, %4$s: Open anchor tag to the learn more page, %5$s: Closing tag. */
			_x(
				'%1$s could not be activated. The %2$s functionality has been merged into %3$s. %4$sLearn More%5$s.',
				'Notice message for the forced deactivation of the Virtual Events plugin after attempting to activate, and the plugin was merged to Events Pro.',
				'tribe-events-calendar-pro'
			),
			'Virtual Events',
			'Virtual Events',
			'The Events Calendar Pro',
			'<a target="_blank" href="https://evnt.is/1bdy">',
			'</a>'
		);
	}
}
