<?php
/**
 * Handles overriding of settings from Online Events extension to Virtual Events plugin.
 *
 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
 *
 * @package Tribe\Events\Virtual\Compatibility\Online_Event_Extension
 */

namespace Tribe\Events\Virtual\Compatibility\Online_Event_Extension;

/**
 * Class Settings
 *
 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
 *
 * @package Tribe\Events\Virtual\Compatibility\Online_Event_Extension
 */
class Settings {

	/**
	 * Injects some additional messaging into the extension
	 *
	 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
	 *
	 * @param array<string,mixed> $fields The current setting fields for the tab.
	 * @param string              $deprecated    The current tab slug.
	 *
	 * @return array<string,mixed> The tab fields, modified if required.
	 */
	public function inject_extension_settings( $fields, $deprecated = null ) {
		$fields['info-box-description']['html'] = wp_kses_post(
			sprintf(
			/* Translators: Opening and closing tags. */
				__(
					'%1$sYou have the %2$sVirtual Events%3$s plugin installed, these settings are superseded by it.%4$s',
					'tribe-events-calendar-pro'
				),
				'<p>',
				'<a href="' . esc_url( '#' ) . '">',
				'</a>',
				'</p>'
			)
			. sprintf(
			/* Translators: Opening and closing tags. */
				__(
					'%1$sYou should deactivate The Events Control extension as all functionality is handled by the Virtual Events plugin.%2$s',
					'tribe-events-calendar-pro'
				),
				'<p>',
				'</p>'
			)
		);

		return $fields;
	}
}
