<?php
/**
 * Admin Controller.
 */

namespace TEC\Events_Pro\Admin;

use TEC\Common\Contracts\Provider\Controller as Controller_Contract;

/**
 * Class Controller.
 *
 * This class extends the Controller_Contract to provide specific functionalities
 * for the TEC\Events_Pro\Linked_Posts\Organizer package.
 *
 * @since   7.0.1
 * @package TEC\Events_Pro\Linked_Posts\Organizer
 */
class Controller extends Controller_Contract {

	/**
	 * Determines if this controller will register.
	 * This is present due to how UOPZ works, it will fail if method belongs to the parent/abstract class.
	 *
	 * @since 7.0.1
	 *
	 * @return bool
	 */
	public function is_active(): bool {
		return true;
	}

	/**
	 * Register the filters and actions.
	 *
	 * @since   7.0.1
	 */
	public function do_register(): void {
		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * Unregister the filters and actions.
	 *
	 * @since   7.0.1
	 */
	public function unregister(): void {
		$this->remove_actions();
		$this->remove_filters();
	}

	/**
	 * Add actions on activation.
	 *
	 * @since 7.0.1
	 */
	public function add_actions(): void {}

	/**
	 * Remove actions on deactivation.
	 *
	 * @since 7.0.1
	 */
	public function remove_actions(): void {}

	/**
	 * Add filters on activation.
	 *
	 * @since 7.0.1
	 */
	public function add_filters(): void {
		add_filter( 'tec_events_settings_display_calendar_display_section', [ $this, 'tec_events_settings_display_calendar_display_section' ] );
		add_filter( 'tec_events_settings_display_date_time_section', [ $this, 'filter_tec_events_date_time_settings_section' ] );
		add_filter( 'tec_events_settings_display_additional_content_section', [ $this, 'filter_tec_events_additional_content_settings_section' ] );
		add_filter( 'tec_events_settings_display_calendar_enable_views_tooltip', [ $this, 'filter_tec_events_settings_display_calendar_enable_views_tooltip' ] );
		add_filter( 'tec_events_display_calendar_settings_posts_per_page_tooltip', [ $this, 'filter_tec_events_display_calendar_settings_posts_per_page_tooltip' ] );
	}

	/**
	 * Remove filters on deactivation.
	 *
	 * @since 7.0.1
	 */
	public function remove_filters(): void {
		remove_filter( 'tec_events_settings_display_calendar_display_section', [ $this, 'tec_events_settings_display_calendar_display_section' ] );
		remove_filter( 'tec_events_settings_display_date_time_section', [ $this, 'filter_tec_events_date_time_settings_section' ] );
		remove_filter( 'tec_events_settings_display_additional_content_section', [ $this, 'filter_tec_events_additional_content_settings_section' ] );
		remove_filter( 'tec_events_settings_display_calendar_enable_views_tooltip', [ $this, 'filter_tec_events_settings_display_calendar_enable_views_tooltip' ] );
		remove_filter( 'tec_events_display_calendar_settings_posts_per_page_tooltip', [ $this, 'filter_tec_events_display_calendar_settings_posts_per_page_tooltip' ] );
	}

	/**
	 * Filter the additional content settings fields.
	 *
	 * @since 7.0.1
	 *
	 * @param array $fields The fields.
	 *
	 * @return array $fields
	 */
	public function tec_events_settings_display_calendar_display_section( $fields ) {
		return $this->get_settings_class()->tec_events_settings_display_calendar_display_section( $fields );
	}

	/**
	 * Filter the date & time settings fields.
	 *
	 * @since 7.0.1
	 *
	 * @param array $fields The fields.
	 *
	 * @return array $fields
	 */
	public function filter_tec_events_date_time_settings_section( $fields ) {
		return $this->get_settings_class()->filter_tec_events_date_time_settings_section( $fields );
	}

	/**
	 * Add additional content settings fields.
	 *
	 * @since 7.0.1
	 *
	 * @param array $fields The fields.
	 *
	 * @return array $fields
	 */
	public function filter_tec_events_additional_content_settings_section( $fields ) {
		return $this->get_settings_class()->filter_tec_events_additional_content_settings_section( $fields );
	}

	/**
	 * Filter the enable views tooltip
	 *
	 * @since 7.0.1
	 *
	 * @param string $text The text.
	 *
	 * @return string $text
	 */
	public function filter_tec_events_settings_display_calendar_enable_views_tooltip( $text ) {
		return $this->get_settings_class()->filter_tec_events_settings_display_calendar_enable_views_tooltip( $text );
	}

	/**
	 * Overwrite the posts per page tooltip.
	 *
	 * @since 7.0.1
	 *
	 * @return string $text
	 */
	public function filter_tec_events_display_calendar_settings_posts_per_page_tooltip() {
		return $this->get_settings_class()->filter_tec_events_display_calendar_settings_posts_per_page_tooltip();
	}

	/**
	 * Get the settings class from the container.
	 *
	 * @return Settings The settings class instance.
	 */
	protected function get_settings_class() {
		return $this->container->make( Settings::class );
	}
}
