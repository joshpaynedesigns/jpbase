<?php
/**
 * Handles the widget integration with Elementor.
 *
 * @since   5.4.0
 *
 * @package Tribe\Events\Pro\Integrations\Elementor
 */

namespace Tribe\Events\Pro\Integrations\Elementor;

use Elementor\Plugin as Elementor_Plugin;
use Tribe__Events__Main as TEC;
use TEC\Events\Integrations\Plugins\Elementor\Controller as Elementor_Integration;
use TEC\Events\Integrations\Plugins\Elementor\Template\Documents\Event_Single_Static;
use TEC\Events\Integrations\Plugins\Elementor\Template\Documents\Event_Single_Dynamic;

/**
 * Class Widget_Manager
 *
 * @since   5.4.0
 *
 * @package Tribe\Events\Pro\Integrations\Elementor
 */
class Widgets_Manager extends Manager_Abstract {
	/**
	 * {@inheritdoc}
	 *
	 * @since 5.4.0
	 *
	 * @var string
	 */
	protected $type = 'widgets';

	/**
	 * Constructor
	 *
	 * @since 5.4.0
	 */
	public function __construct() {
		$this->objects = [
			Widgets\Widget_Countdown::get_slug()           => Widgets\Widget_Countdown::class,
			Widgets\Widget_Event_List::get_slug()          => Widgets\Widget_Event_List::class,
			Widgets\Widget_Event_Single_Legacy::get_slug() => Widgets\Widget_Event_Single_Legacy::class,
			Widgets\Widget_Events_View::get_slug()         => Widgets\Widget_Events_View::class,
		];
	}

	/**
	 * Registers the widgets with Elementor.
	 *
	 * @since 5.4.0
	 */
	public function register() {
		$widgets = $this->get_registered_objects();

		// If we are on a single event, replace the legacy widget with a placeholder one.
		if ( $this->should_swap_legacy() ) {
			$slug             = Widgets\Widget_Event_Single_Legacy::get_slug();
			$widgets[ $slug ] = Widgets\Widget_Event_Single_Legacy_Replacement::class;
		}

		foreach ( $widgets as $slug => $widget_class ) {
			Elementor_Plugin::instance()->widgets_manager->register( tribe( $widget_class ) );
		}
	}

	/**
	 * Determine if we should load the legacy widget
	 * based on the global query and $post post_type.
	 *
	 * @since 7.0.1
	 *
	 * @return boolean
	 */
	public function should_swap_legacy() {
		global $post;

		if ( ! $post instanceof \WP_Post ) {
			return false;
		}

		// Prevent display on singular tribe_event post type. Since `is_singular` is not reliable in the admin, we'll check another way as well.
		if ( is_singular( TEC::POSTTYPE ) ) {
			return true;
		}

		// Second check for singular tribe_event post type as `is_singular` is not always reliable in the admin.
		if ( $post->post_type === TEC::POSTTYPE ) {
			return true;
		}

		$document = Elementor_Plugin::instance()->documents->get_current();

		// No document? Bail early.
		if ( ! $document ) {
			return false;
		}

		// Prevent on our event starter template.
		if ( $document->get_name() === Event_Single_Static::get_type() ) {
			return true;
		}

		// If Elementor Pro is active, prevent on our event dynamic template.
		if (
			tribe( Elementor_Integration::class )->is_elementor_pro_active() &&
			$document->get_name() === Event_Single_Dynamic::get_type()
		) {
			return true;
		}

		return false;
	}
}
