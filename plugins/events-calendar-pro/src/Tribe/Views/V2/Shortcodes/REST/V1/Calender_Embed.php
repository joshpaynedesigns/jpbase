<?php
/**
 * REST API endpoint for the Calendar Embed block.
 *
 * @since 7.2.0
 */

namespace Tribe\Events\Pro\Views\V2\Shortcodes\REST\V1;

use Tribe\Events\Views\V2\Assets as Event_Assets;
use Tribe\Shortcode\Manager;
use Tribe__Events__Pro__Main;
use Tribe__Template as Template;
use Tribe__Utils__Array as Arr;
use WP_REST_Request;
use WP_REST_Server;

/**
 * Class Calender_Embed
 *
 * @since   7.2.0
 *
 * @package TEC\Events_Pro\Custom_Tables\V1\REST\V1
 */
class Calender_Embed {

	/**
	 * The shortcode name.
	 *
	 * @since 7.2.0
	 *
	 * @var string $shortcode The shortcode name.
	 */
	protected $shortcode = 'tribe_events';

	/**
	 * Template instance.
	 *
	 * @since 7.2.0
	 *
	 * @var Template
	 */
	private $template;

	/**
	 * Get the template.
	 *
	 * @since 7.2.0
	 *
	 * @return Template
	 */
	public function get_template(): Template {
		if ( empty( $this->template ) ) {
			$events_pro = Tribe__Events__Pro__Main::instance();
			$template   = new Template();
			$template->set_template_origin( $events_pro );
			$template->set_template_folder( 'src/views/iframe' );
			$template->set_template_folder_lookup( true );
			$template->set_template_context_extract( true );
			$this->template = $template;
		}

		return $this->template;
	}

	/**
	 * Registers the REST API endpoint for the Calendar Embed block.
	 *
	 * @since 7.2.0
	 */
	public function register() {
		register_rest_route(
			'tec/v1',
			'/events/calendar-embed',
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get' ],
				'permission_callback' => [ $this, 'check_permission' ],
				'args'                => $this->read_args(),
			],
		);
	}

	/**
	 * Checks if the current user has the capability to edit events and verifies the nonce.
	 *
	 * @since 7.2.0
	 *
	 * @param WP_REST_Request $request The current REST request.
	 *
	 * @return bool True if the user has the edit events capability and nonce is valid, false otherwise.
	 */
	public function check_permission( WP_REST_Request $request ): bool {
		$nonce = $request->get_param( '_wpnonce' );

		if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			return false;
		}

		// phpcs:disable WordPress.WP.Capabilities.Unknown
		return current_user_can( 'edit_tribe_events' );
		// phpcs:enable WordPress.WP.Capabilities.Unknown
	}

	/**
	 * Define the read arguments for the REST endpoint.
	 *
	 * @since 7.2.0
	 *
	 * @return array<string,array<string,mixed>> The arguments for the REST endpoint.
	 */
	public function read_args() {
		return [
			'view'                 => [
				'required'          => false,
				'sanitize_callback' => [ $this, 'sanitize_callback' ],
				'default'           => tribe_get_option( 'viewOption' ),
				'description'       => esc_html_x( 'Specifies the format in which events are displayed, with options including Month, List, Day, Photo, Week, Map, and Summary view.', 'Description for the view argument in the Calendar Embed REST endpoint.', 'events-calendar-pro' ),
				'type'              => 'string',
			],
			'category'             => [
				'required'          => false,
				'sanitize_callback' => [ $this, 'sanitize_callback' ],
				'default'           => '',
				'description'       => esc_html_x( 'Filters the view to only show events in the specified category.', 'Description for the category argument in the Calendar Embed REST endpoint.', 'events-calendar-pro' ),
				'type'              => 'string',
			],
			'exclude_category'     => [
				'required'          => false,
				'sanitize_callback' => [ $this, 'sanitize_callback' ],
				'default'           => '',
				'description'       => esc_html_x( 'Excludes a specified category from the events displayed.', 'Description for the exclude-category argument in the Calendar Embed REST endpoint.', 'events-calendar-pro' ),
				'type'              => 'string',
			],
			'tag'                  => [
				'required'          => false,
				'sanitize_callback' => [ $this, 'sanitize_callback' ],
				'default'           => '',
				'description'       => esc_html_x( 'Filters the view to only show events in the specified tag.', 'Description for the tag argument in the Calendar Embed REST endpoint.', 'events-calendar-pro' ),
				'type'              => 'string',
			],
			'tag_category'         => [
				'required'          => false,
				'sanitize_callback' => [ $this, 'sanitize_callback' ],
				'default'           => '',
				'description'       => esc_html_x( 'Excludes a specified tag from the events displayed.', 'Description for the tag-category argument in the Calendar Embed REST endpoint.', 'events-calendar-pro' ),
				'type'              => 'string',
			],
			'tax_operand'          => [
				'required'          => false,
				'sanitize_callback' => [ $this, 'sanitize_callback' ],
				'default'           => '',
				'description'       => esc_html_x( 'Determines whether to include events that match all or any of the specified categories/tags.', 'Description for the tax-operand argument in the Calendar Embed REST endpoint.', 'events-calendar-pro' ),
				'type'              => 'string',
			],
			'events_per_page'      => [
				'required'          => false,
				'sanitize_callback' => [ $this, 'sanitize_callback' ],
				'default'           => '',
				'description'       => esc_html_x( 'Controls the number of events displayed per page in list-style views.', 'Description for the events_per_page argument in the Calendar Embed REST endpoint.', 'events-calendar-pro' ),
				'type'              => 'integer',
			],
			'month_events_per_day' => [
				'required'          => false,
				'sanitize_callback' => [ $this, 'sanitize_callback' ],
				'default'           => '',
				'description'       => esc_html_x( 'Limits the number of events displayed per day in Month View.', 'Description for the month_events_per_day argument in the Calendar Embed REST endpoint.', 'events-calendar-pro' ),
				'type'              => 'integer',
			],
			'featured'             => [
				'required'    => false,
				'type'        => 'boolean',
				'description' => esc_html_x( 'Events should be filtered by their featured status.', 'Description for the featured argument in the Calendar Embed REST endpoint.', 'events-calendar-pro' ),
			],
			'past'                 => [
				'required'    => false,
				'type'        => 'boolean',
				'description' => esc_html_x( 'Past events should display.', 'Description for the past argument in the Calendar Embed REST endpoint.', 'events-calendar-pro' ),
			],
			'tribe_bar'            => [
				'required'    => false,
				'type'        => 'boolean',
				'description' => esc_html_x( 'Whether to display the tribe bar above the views.', 'Description for the tribe_bar argument in the Calendar Embed REST endpoint.', 'events-calendar-pro' ),
			],
			'filter_bar'           => [
				'required'    => false,
				'type'        => 'boolean',
				'description' => esc_html_x( 'Whether to display the Filter Bar with the views.', 'Description for the filter_bar argument in the Calendar Embed REST endpoint.', 'events-calendar-pro' ),
			],
			'date'                 => [
				'required'    => false,
				'type'        => 'string',
				'description' => esc_html_x( 'Sets the specific date for the view to start displaying events.', 'Description for the date argument in the Calendar Embed REST endpoint.', 'events-calendar-pro' ),
			],
			'keyword'              => [
				'required'    => false,
				'type'        => 'string',
				'description' => esc_html_x( 'Filters events by a specified keyword in the title or description.', 'Description for the keyword argument in the Calendar Embed REST endpoint.', 'events-calendar-pro' ),
			],
			'author'               => [
				'required'          => false,
				'sanitize_callback' => [ $this, 'sanitize_callback' ],
				'default'           => '',
				'description'       => esc_html_x( 'Filters the view to only show events in the specified category.', 'Description for the category argument in the Calendar Embed REST endpoint.', 'events-calendar-pro' ),
				'type'              => 'integer',
			],
			'organizer'            => [
				'required'          => false,
				'sanitize_callback' => [ $this, 'sanitize_callback' ],
				'default'           => '',
				'description'       => esc_html_x( 'Filters the view to only show events in the specified category.', 'Description for the category argument in the Calendar Embed REST endpoint.', 'events-calendar-pro' ),
				'type'              => 'integer',
			],
			'venue'                => [
				'required'          => false,
				'sanitize_callback' => [ $this, 'sanitize_callback' ],
				'default'           => '',
				'description'       => esc_html_x( 'Filters the view to only show events in the specified category.', 'Description for the category argument in the Calendar Embed REST endpoint.', 'events-calendar-pro' ),
				'type'              => 'integer',
			],
		];
	}

	/**
	 * Get the response for the REST API endpoint.
	 *
	 * @param WP_REST_Request $request The HTTP client request.
	 */
	public function get( WP_REST_Request $request ) {
		$request_params               = $request->get_params();
		$args                         = [];
		$args['view']                 = Arr::get( $request_params, 'view', tribe_get_option( 'viewOption' ) );
		$args['category']             = Arr::get( $request_params, 'category', '' );
		$args['exclude-category']     = Arr::get( $request_params, 'exclude_category', '' );
		$args['tag']                  = Arr::get( $request_params, 'tag', '' );
		$args['exclude-tag']          = Arr::get( $request_params, 'exclude_tag', '' );
		$args['tax-operand']          = Arr::get( $request_params, 'tax_operand', '' );
		$args['events_per_page']      = Arr::get( $request_params, 'events_per_page', '' );
		$args['past']                 = Arr::get( $request_params, 'past', false );
		$args['featured']             = Arr::get( $request_params, 'featured', false );
		$args['tribe-bar']            = Arr::get( $request_params, 'tribe_bar', true );
		$args['filter-bar']           = Arr::get( $request_params, 'filter_bar', false );
		$args['month_events_per_day'] = Arr::get( $request_params, 'month_events_per_day', '' );
		$args['date']                 = Arr::get( $request_params, 'date', '' );
		$args['keyword']              = Arr::get( $request_params, 'keyword', '' );
		$args['author']               = Arr::get( $request_params, 'author', '' );
		$args['organizer']            = Arr::get( $request_params, 'organizer', '' );
		$args['venue']                = Arr::get( $request_params, 'venue', '' );

		// If the featured parameter is set to false, nullify it or no featured events show.
		if ( $args['featured'] === false || $args['featured'] === 0 || $args['featured'] === 'false' ) {
			$args['featured'] = null;
		}

		/**
		 * Filter the arguments used to get tribe_events shortcode.
		 *
		 * @since 7.2.0
		 *
		 * @param array            $args Arguments used to get the events from the archive page.
		 * @param \WP_REST_Request $request
		 */
		$args = apply_filters( 'tec_events_pro_calendar_embed_rest_get_args', $args, $request );

		$manager = tribe( Manager::class );

		ob_start();

		// Define that this is an iframe request.
		define( 'IFRAME_REQUEST', true );

		// Enqueue necessary assets for the iframe content.
		tribe_asset_enqueue_group( Event_Assets::$group_key );
		tribe_asset_enqueue( 'tec-events-pro-iframe-content-resizer' );

		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		// phpcs:disable StellarWP.XSS.EscapeOutput.OutputNotEscaped
		$this->frontend_iframe_header();

		// Output the content for the iframe.
		echo $manager->render_shortcode( $args, '', $this->shortcode );

		$this->frontend_iframe_footer();

		$content = ob_get_clean();

		echo $content;
		// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
		// phpcs:enable StellarWP.XSS.EscapeOutput.OutputNotEscaped

		// Stop further execution.
		tribe_exit();
	}

	/**
	 * Generic Iframe header for front-end use.
	 *
	 * @since 7.2.0
	 */
	protected function frontend_iframe_header() {
		// Disable the admin bar on the front end.
		// phpcs:disable WordPressVIPMinimum.UserExperience.AdminBarRemoval.RemovalDetected
		show_admin_bar( false );
		// phpcs:enable WordPressVIPMinimum.UserExperience.AdminBarRemoval.RemovalDetected

		header( 'Content-Type: ' . get_option( 'html_type' ) . '; charset=' . get_option( 'blog_charset' ) );
		?>
		<!DOCTYPE html>
			<html>
				<head>
					<title><?php echo esc_html_x( 'Calendar Embed Iframe', 'The title for the calendar embed iframe.', 'events-calendar-pro' ); ?></title>
		<?php

		do_action( 'tec_events_pro_calendar_embed_iframe_head' );

		wp_head();
		?>
				</head>
				<body class="tec-events-pro-calendar-embed-iframe__body">
		<?php
	}

	/**
	 * Generic Iframe footer for front-end use.
	 *
	 * @since 7.2.0
	 */
	protected function frontend_iframe_footer() {
		// Hook for adding any additional footer scripts or styles.
		do_action( 'frontend_iframe_footer_scripts' );

		// Output wp_footer if needed.
		wp_footer();
		?>
			</body>
		</html>
		<?php
	}

	/**
	 * Generic Iframe header for front-end use.
	 *
	 * @since 7.2.0
	 */
	protected function frontend_iframe_header_template() {
		return $this->get_template()->template(
			'/header',
			[],
			false
		);
	}

	/**
	 * Generic Iframe footer for front-end use.
	 *
	 * @since 7.2.0
	 */
	protected function frontend_iframe_footer_template() {
		return $this->get_template()->template(
			'/footer',
			[],
			false
		);
	}

	/**
	 * Sanitize a request argument based on details registered to the route.
	 *
	 * @since 7.2.0
	 *
	 * @param mixed $value Value of the 'filter' argument.
	 *
	 * @return string|array<string|string> A text field sanitized string or array.
	 */
	public function sanitize_callback( $value ) {
		if ( is_array( $value ) ) {
			return array_map( 'sanitize_text_field', $value );
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Get the shortcode slug.
	 *
	 * @since 7.2.0
	 *
	 * @return string The shortcode slug.
	 */
	public function get_shortcode_slug() {
		return $this->shortcode;
	}

	/**
	 * Processes the attributes array, extracting IDs from JSON strings for specific fields.
	 *
	 * @since 7.2.0
	 *
	 * @param array $attributes The array of attributes to be processed.
	 *
	 * @return array The processed array with JSON fields converted to arrays of IDs.
	 */
	public function process_attributes( array $attributes ) {
		$fields_to_process = [ 'category', 'exclude_category', 'tag', 'exclude_tag' ];

		// Iterate over the fields that need to be processed.
		foreach ( $fields_to_process as $field ) {
			if ( isset( $attributes[ $field ] ) ) {
				// Process the JSON string and convert to an array of IDs.
				$attributes[ $field ] = $this->extract_ids_from_json( $attributes[ $field ] );
			}
		}

		$attributes['exclude-category'] = Arr::get( $attributes, 'exclude_category', null );
		$attributes['exclude-tag']      = Arr::get( $attributes, 'exclude_tag', null );
		unset( $attributes['exclude_category'] );
		unset( $attributes['exclude_tag'] );

		$attributes['tribe-bar']  = Arr::get( $attributes, 'tribe_bar', null );
		$attributes['filter-bar'] = Arr::get( $attributes, 'filter_bar', null );
		unset( $attributes['tribe_bar'] );
		unset( $attributes['filter_bar'] );

		// If the featured parameter is set to false, nullify it or no featured events show.
		if ( $attributes['featured'] === false || $attributes['featured'] === 0 || $attributes['featured'] === 'false' ) {
			$attributes['featured'] = null;
		}

		// Return the processed attributes array.
		return $attributes;
	}

	/**
	 * Extracts an array of IDs from a JSON string.
	 *
	 * @since 7.2.0
	 *
	 * @param string $json_string The JSON string containing the objects.
	 *
	 * @return array<int> The array of IDs.
	 */
	protected function extract_ids_from_json( $json_string ) {
		if ( empty( $json_string ) ) {
			return [];
		}

		$parsed = json_decode( $json_string, true );

		if ( ! is_array( $parsed ) ) {
			return [];
		}

		return array_filter( array_column( $parsed, 'id' ) );
	}
}
