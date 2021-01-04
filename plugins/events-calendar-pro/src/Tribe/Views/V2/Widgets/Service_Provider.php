<?php
/**
 * The main service provider for the version 2 of the Pro Widgets.
 *
 * @since   5.2.0
 *
 * @package Tribe\Events\Pro\Views\V2\Widgets
 */

namespace Tribe\Events\Pro\Views\V2\Widgets;

use Tribe\Events\Views\V2\Widgets\Widget_List;

/**
 * Class Service_Provider
 *
 * @since   5.2.0
 *
 * @package Tribe\Events\Pro\Views\V2\Widgets
 */
class Service_Provider extends \tad_DI52_ServiceProvider {

	/**
	 * Variable that holds the name of the widgets being created.
	 *
	 * @since 5.2.0
	 *
	 * @var array<string>
	 */
	protected $widgets = [
		// 'widget-mini-calendar',
	];

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 5.2.0
	 */
	public function register() {
		// Activate the compatibility coding for V1 and V2 Event List Widgets.
		add_filter( 'tribe_events_views_v2_advanced_list_widget_primary', '__return_true' );

		// Determine if V2 widgets should load.
		if ( ! tribe_events_widgets_v2_is_enabled() ) {
			return;
		}

		$this->register_hooks();
		$this->hook_widgets();
	}

	/**
	 * Registers the provider handling for first level v2 widgets.
	 *
	 * @since 5.2.0
	 */
	protected function register_hooks() {
		$hooks = new Hooks( $this->container );
		$hooks->register();

		// Allow Hooks to be removed, by having the them registered to the container.
		$this->container->singleton( Hooks::class, $hooks );
		$this->container->singleton( 'pro.views.v2.widgets.hooks', $hooks );
	}

	/**
	 * Function used to attach the hooks associated with this class.
	 *
	 * @since 5.2.0
	 */
	public function hook_widgets() {
		add_filter( 'tribe_widgets', [ $this, 'register_widget' ] );
		add_filter( 'tribe_events_views', [ $this, 'add_views' ] );
		add_filter( 'tribe_events_pro_shortcodes_list_widget_class', [ $this, 'alter_widget_class' ], 10, 2 );
	}

	/**
	 * Add the widgets to register with WordPress.
	 *
	 * @since 5.2.0
	 *
	 * @param array<string,string> $widgets An array of widget classes to register.
	 *
	 * @return array<string,string> An array of registered widget classes.
	 */
	public function register_widget( $widgets ) {
		// $widgets['tribe_events_mini_calendar_widget'] = Widget_Mini_Calendar::class;

		return $widgets;
	}

	/**
	 * Add the widget views to the view manager.
	 *
	 * @since 5.2.0
	 *
	 * @param array<string,string> $views An associative array of views in the shape `[ <slug> => <class> ]`.
	 *
	 * @return array<string,string> $views The modified array of views in the shape `[ <slug> => <class> ]`.
	 */
	public function add_views( $views ) {
		// $views['widget-mini-calendar'] = Widget_Mini_Calendar_View::class;

		return $views;
	}

	/**
	 * Swaps in the new V2 widget for the old one in the widget shortcode.
	 *
	 * @since 5.2.0
	 *
	 * @param string              $widget_class The widget class name we're currently implementing.
	 * @param array<string,mixed> $arguments    The widget arguments.
	 *
	 * @return string             $widget_class The modified (V2) widget class name we want to implement.
	 */
	public function alter_widget_class( $widget_class, $arguments ) {
		return Widget_List::class;
	}
}
