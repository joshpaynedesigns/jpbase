<?php

namespace TEC\Events_Pro\Linked_Posts\Organizer;

use Tribe__Main as Common_Main;
use Tribe__Settings_Tab as Settings_Tab;

/**
 * Class Settings.
 *
 * This class is used to manage the display settings for the Organizers.
 *
 * @since   6.2.0
 *
 * @package TEC\Events_Pro\Linked_Posts\Organizer
 */
class Settings {

	/**
	 * Get the display settings.
	 *
	 * @since   6.2.0
	 * @return array The display settings for phone and email visibility in the format {organizer-visibility-settings-title => ....
	 */
	public function get_display_settings(): array {
		$phone_visibility = tribe( Phone_Visibility_Modifier::class );
		$email_visibility = tribe( Email_Visibility_Modifier::class );
		$settings         = [
			'organizer-visibility-settings-title' => [
				'type' => 'html',
				'html' => '<h3 id="tec-organizer-display-settings" class="tec-settings-form__section-header">' . esc_html_x( 'Organizers', 'Header for the organizer settings section header', 'tribe-events-calendar-pro' ) . '</h3>',
			],
			$phone_visibility->get_setting_key()  => $phone_visibility->get_setting_definition(),
			$email_visibility->get_setting_key()  => $email_visibility->get_setting_definition(),
		];

		/**
		 * Filters the fields for the organizer settings section.
		 *
		 * @since 7.0.1
		 *
		 * @param array $fields The fields for the organizer settings section.
		 */
		return (array) apply_filters( 'tribe_display_settings_organizers_section', $settings );
	}

	/**
	 * Add the organizer subtab to the display settings page.
	 *
	 * @since 7.0.1
	 *
	 * @param Settings_Tab $parent_tab The parent tab to which the organizer tab should be added.
	 *
	 * @return void
	 */
	public function add_organizer_tab( Settings_Tab $parent_tab ): void {
		$parent_tab->add_child(
			new Settings_Tab(
				'display-organizers-tab',
				esc_html__( 'Organizers', 'the-events-calendar' ),
				[
					'priority' => 5.05,
					'fields'   => $this->get_display_settings(),
				]
			)
		);
	}

	/**
	 * Inject display settings into the provided fields.
	 *
	 * @since 6.2.0
	 * @deprecated
	 *
	 * @param array $fields The fields into which the display settings should be injected.
	 *
	 * @return array The fields with the injected display settings.
	 */
	public function inject_display_settings( array $fields ): array {
		_deprecated_function( __METHOD__, '7.0.1', 'add_organizer_tab' );

		return array_merge( $fields, $this->get_display_settings() );
	}
}
