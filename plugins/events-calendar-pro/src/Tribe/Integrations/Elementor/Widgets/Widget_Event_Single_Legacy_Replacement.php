<?php
/**
 * Event Single Elementor Replacement Widget.
 *
 * This widget only displays when the legacy event widget is used in
 * a single-event template, where it is not supported.
 *
 * @since 7.0.1
 *
 * @package Tribe\Events\Pro\Integrations\Elementor\Widgets
 */

namespace Tribe\Events\Pro\Integrations\Elementor\Widgets;

use Elementor\Controls_Manager;

/**
 * Class Widget_Event_Single_Legacy
 *
 * @since 7.0.1
 *
 * @package Tribe\Events\Pro\Integrations\Elementor\Widgets
 */
class Widget_Event_Single_Legacy_Replacement extends Widget_Abstract {
	/**
	 * {@inheritdoc}
	 *
	 * Note this is identical to the original Widget_Event_Single_Legacy class.
	 * It must be for smooth replacement, but collisions could happen!
	 *
	 * @since 7.0.1
	 *
	 * @var string
	 */
	protected static $widget_slug = 'event_single_legacy';

	/**
	 * {@inheritdoc}
	 *
	 * @since 7.0.1
	 *
	 * @var string
	 */
	protected $widget_icon = 'eicon-close-circle';

	/**
	 * {@inheritdoc}
	 *
	 * @since 7.0.1
	 *
	 * @param array  $data Widget data.
	 * @param ?array $args Widget arguments.
	 */
	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		$this->widget_title = __( 'Legacy Event (disabled)', 'tribe-events-calendar-pro' );
	}

	/**
	 * Show in panel.
	 *
	 * Whether to show the widget in the panel or not. By default returns true.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return bool Whether to show the widget in the panel or not.
	 */
	public function show_in_panel() {
		return false;
	}

	/**
	 * Render widget output.
	 *
	 * @since 7.0.1
	 */
	protected function render() {
		// We don't render anything on the front end.
		if ( ! is_admin() ) {
			return;
		}

		printf(
			/* Translators: %1$s and %2$s are the opening and closing bold tags, respectively. */
			esc_html__( '%1$sThe Legacy Event widget is not supported in this layout!%1$s', 'tribe-events-calendar-pro' ),
			'<b>',
			'</b>'
		);
	}

	/**
	 * Register widget controls.
	 *
	 * @since 7.0.1
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'events',
			[
				'label' => esc_html__( 'Important note on Legacy Event Widget', 'tribe-events-calendar-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'legacy_warning',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'raw'             => sprintf(
					/* Translators: %1$s is the url to the documentation */
					__(
						'The Legacy Event widget is not supported in this layout. <a href="%1$s" target="_blank">Learn more</a>.',
						'tribe-events-calendar-pro'
					),
					esc_url( 'https://evnt.is/1q' )
				),
			]
		);

		$this->end_controls_section();
	}
}
