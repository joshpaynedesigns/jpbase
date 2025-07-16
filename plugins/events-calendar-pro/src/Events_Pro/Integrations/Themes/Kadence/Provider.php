<?php
/**
 * Controller for Events Calendar Pro Kadence integrations.
 *
 * @since 7.3.0
 *
 * @package TEC\Events_Pro\Integrations\Themes\Kadence
 */

namespace TEC\Events_Pro\Integrations\Themes\Kadence;

use TEC\Common\Integrations\Traits\Theme_Integration;
use TEC\Events_Pro\Integrations\Integration_Abstract;

/**
 * Class Controller
 *
 * @since 7.3.0
 *
 * @package TEC\Events_Pro\Integrations\Themes\Kadence
 */
class Provider extends Integration_Abstract {

	use Theme_Integration;

	/**
	 * @inheritDoc
	 *
	 * @return string The slug of the integration.
	 */
	public static function get_slug(): string {
		return 'kadence';
	}

	/**
	 * @inheritDoc
	 *
	 * @return bool Whether or not integrations should load.
	 */
	public function load_conditionals(): bool {
		$theme             = wp_get_theme();
		$theme_name        = $theme->get( 'Name' );
		$parent_theme_name = $theme->get( 'Parent Theme' );

		$theme_name        = is_string( $theme_name ) ? strtolower( $theme_name ) : '';
		$parent_theme_name = is_string( $parent_theme_name ) ? strtolower( $parent_theme_name ) : '';

		return $theme_name === 'kadence' || $parent_theme_name === 'kadence';
	}

	/**
	 * @inheritDoc
	 *
	 * @return void
	 */
	protected function load(): void {
		add_filter( 'tribe_events_views_v2_view_html_classes', [ $this, 'remove_alignwide_from_class_list' ] );
	}

	/**
	 * Removes 'alignwide' from the class list of the calendar container.
	 *
	 * @since 7.3.0
	 *
	 * @param array $class_list An array of the classes applied to the calendar container.
	 *
	 * @return array
	 */
	public function remove_alignwide_from_class_list( $class_list ) {
		return array_diff( $class_list, [ 'alignwide' ] );
	}
}
