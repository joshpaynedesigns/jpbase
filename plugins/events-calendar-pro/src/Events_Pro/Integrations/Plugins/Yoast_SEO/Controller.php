<?php
/**
 * Controller for Events Calendar Pro Yoast SEO integrations.
 *
 * @since   7.4.3
 *
 * @package TEC\Events_Pro\Integrations\Plugins\Yoast_SEO
 */

namespace TEC\Events_Pro\Integrations\Plugins\Yoast_SEO;

use TEC\Common\Contracts\Provider\Controller as Controller_Contract;

/**
 * Class Controller
 *
 * @since   7.4.3
 *
 * @package TEC\Events_Pro\Integrations\Plugins\Yoast_SEO
 */
class Controller extends Controller_Contract {

	/**
	 * The sitemap name used in URLs and registration.
	 *
	 * @since 7.4.3
	 *
	 * @var string
	 */
	protected $sitemap_name = 'tec_recurring_events';

	/**
	 * Output charset used for encoding URLs.
	 *
	 * @since 7.4.3
	 *
	 * @var string
	 */
	protected $output_charset = 'UTF-8';

	/**
	 * Register actions and hooks for the integration.
	 *
	 * @since 7.4.3
	 *
	 * @return void
	 */
	public function do_register(): void {
		$this->container->singleton( static::class, $this );
		add_filter( 'wpseo_sitemap_index', [ $this, 'sitemap_index' ] );
		add_action( 'init', [ $this, 'sitemap_register' ] );
	}

	/**
	 * Unregister actions and hooks for the integration.
	 *
	 * @since 7.4.3
	 *
	 * @return void
	 */
	public function unregister(): void {
		remove_action( 'send_headers', [ $this, 'filter_headers' ] );
	}

	/**
	 * Get the sitemap URL with the sitemap name.
	 *
	 * @since 7.4.3
	 *
	 * @return string The complete sitemap URL.
	 */
	public function get_sitemap_url() {
		$sitemap_url = home_url( $this->sitemap_name . '-sitemap.xml' );

		/**
		 * Filter the sitemap URL for recurring events.
		 *
		 * @since 7.4.3
		 *
		 * @param string     $sitemap_url  The complete sitemap URL.
		 * @param string     $sitemap_name The sitemap name used in the URL.
		 * @param Controller $this         The current controller instance.
		 */
		return (string) apply_filters( 'tec_events_pro_yoast_seo_sitemap_url', $sitemap_url, $this->sitemap_name, $this );
	}

	/**
	 * Add tec_recurring_events-sitemap.xml to Yoast sitemap index.
	 *
	 * @since 7.4.3
	 *
	 * @param string $sitemap_index The current sitemap index content.
	 *
	 * @return string Modified sitemap index with recurring events sitemap added.
	 */
	public function sitemap_index( $sitemap_index ) {
		$sitemap_url  = $this->get_sitemap_url();
		$sitemap_date = $this->get_latest_modified_date();

		$output  = "\t<sitemap>\n";
		$output .= "\t\t<loc>" . htmlspecialchars( $sitemap_url ) . "</loc>\n";
		$output .= "\t\t<lastmod>" . htmlspecialchars( $sitemap_date ) . "</lastmod>\n";
		$output .= "\t</sitemap>\n";

		return $sitemap_index . $output;
	}

	/**
	 * Register the recurring events sitemap with Yoast SEO.
	 *
	 * @since 7.4.3
	 *
	 * @return void
	 */
	public function sitemap_register() {
		global $wpseo_sitemaps;
		if ( isset( $wpseo_sitemaps ) && ! empty( $wpseo_sitemaps ) ) {
			$wpseo_sitemaps->register_sitemap( $this->sitemap_name, [ $this, 'sitemap_generate' ] );
		}
	}

	/**
	 * Generate the XML content for the recurring events sitemap.
	 *
	 * @since 7.4.3
	 *
	 * @return void Directly outputs the sitemap content.
	 */
	public function sitemap_generate() {
		global $wpseo_sitemaps;
		$events = $this->get_recurring_events();
		$links  = [];

		foreach ( $events as $event ) {
			$url = [
				'loc'    => $event->permalink,
				'mod'    => $event->post_modified,
				'images' => [],
			];

			// Add featured image if available.
			if ( has_post_thumbnail( $event->ID ) ) {
				$image_id  = get_post_thumbnail_id( $event->ID );
				$image_url = wp_get_attachment_image_url( $image_id, 'full' );

				if ( $image_url ) {
					$url['images'][] = [
						'src' => $image_url,
					];
				}
			}

			$links[] = $url;
		}

		$xml = $this->get_sitemap( $links, $this->sitemap_name );

		if ( isset( $wpseo_sitemaps ) && ! empty( $wpseo_sitemaps ) ) {
			$wpseo_sitemaps->set_sitemap( $xml );
		}
	}

	/**
	 * Builds the sitemap.
	 * Based off Yoast SEO WPSEO_Sitemaps_Renderer::get_sitemap().
	 *
	 * @since 7.4.3
	 *
	 * @param array<string> $links        Set of sitemap links.
	 * @param string        $type         Sitemap type.
	 *
	 * @return string
	 */
	public function get_sitemap( $links, $type ) {
		$urlset = '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd http://www.google.com/schemas/sitemap-image/1.1 http://www.google.com/schemas/sitemap-image/1.1/sitemap-image.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">"\n"';

		/**
		 * Filters the `urlset` for all sitemaps.
		 *
		 * @since 7.4.3
		 *
		 * @param string $urlset The output for the sitemap's `urlset`.
		 */
		$urlset = (string) apply_filters( 'tec_events_pro_yoast_seo_sitemap_urlset', $urlset );

		/**
		 * Filters the `urlset` for a sitemap by type.
		 *
		 * @since 7.4.3
		 *
		 * @param string $urlset The output for the sitemap's `urlset`.
		 */
		$xml = (string) apply_filters( "tec_events_pro_yoast_seo_sitemap_sitemap_{$type}_urlset", $urlset );
		foreach ( $links as $url ) {
			$xml .= $this->sitemap_url( $url );
		}

		$xml .= '</urlset>';

		return $xml;
	}

	/**
	 * Build the `<url>` tag for a given URL.
	 * Based off Yoast SEO WPSEO_Sitemaps_Renderer::sitemap_url().
	 *
	 * @since 7.4.3
	 *
	 * @param array<string> $url Array of parts that make up this entry.
	 *
	 * @return string
	 */
	public function sitemap_url( $url ) {
		$date = null;

		if ( ! empty( $url['mod'] ) ) {
			// Create a DateTime object date in the correct timezone.
			$date = function_exists( 'YoastSEO' ) ? YoastSEO()->helpers->date->format( $url['mod'] ) : gmdate( DATE_W3C, strtotime( $url['mod'] ) );
		}

		$output  = "\t<url>\n";
		$output .= "\t\t<loc>" . $this->encode_and_escape( $url['loc'] ) . "</loc>\n";
		$output .= empty( $date ) ? '' : "\t\t<lastmod>" . htmlspecialchars( $date, ENT_COMPAT, $this->output_charset, false ) . "</lastmod>\n";

		if ( empty( $url['images'] ) ) {
			$url['images'] = [];
		}

		foreach ( $url['images'] as $img ) {
			if ( empty( $img['src'] ) ) {
				continue;
			}

			$output .= "\t\t<image:image>\n";
			$output .= "\t\t\t<image:loc>" . $this->encode_and_escape( $img['src'] ) . "</image:loc>\n";
			$output .= "\t\t</image:image>\n";
		}
		unset( $img );

		$output .= "\t</url>\n";

		/**
		 * Filters the output for the sitemap URL tag.
		 *
		 * @since 7.4.3
		 *
		 * @param string $output The output for the sitemap url tag.
		 * @param array  $url    The sitemap URL array on which the output is based.
		 */
		return (string) apply_filters( 'tec_events_pro_yoast_seo_sitemap_url', $output, $url );
	}

	/**
	 * Ensure the URL is encoded per RFC3986 and correctly escaped for use in an XML sitemap.
	 * Based off Yoast SEO WPSEO_Sitemaps_Renderer::encode_and_escape().
	 *
	 * @since 7.4.3
	 *
	 * @param string $url URL to encode and escape.
	 *
	 * @return string
	 */
	protected function encode_and_escape( $url ) {
		$url = $this->encode_url_rfc3986( $url );
		$url = esc_url( $url );
		$url = str_replace( '&#038;', '&amp;', $url );
		$url = str_replace( '&#039;', '&apos;', $url );

		if ( strpos( $url, '//' ) === 0 ) {
			// Schema-relative URL for which esc_url() does not add a scheme.
			$url = 'http:' . $url;
		}

		return $url;
	}

	/**
	 * Apply some best effort conversion to comply with RFC3986.
	 * Based off Yoast SEO WPSEO_Sitemaps_Renderer::encode_url_rfc3986().
	 *
	 * @since 7.4.3
	 *
	 * @param string $url URL to encode.
	 *
	 * @return string
	 */
	protected function encode_url_rfc3986( $url ) {
		if ( filter_var( $url, FILTER_VALIDATE_URL ) ) {
			return $url;
		}

		$path = wp_parse_url( $url, PHP_URL_PATH );

		if ( ! empty( $path ) && $path !== '/' ) {
			$encoded_path = explode( '/', $path );

			// First decode the path, to prevent double encoding.
			$encoded_path = array_map( 'rawurldecode', $encoded_path );

			$encoded_path = array_map( 'rawurlencode', $encoded_path );
			$encoded_path = implode( '/', $encoded_path );

			$url = str_replace( $path, $encoded_path, $url );
		}

		$query = wp_parse_url( $url, PHP_URL_QUERY );

		if ( ! empty( $query ) ) {
			parse_str( $query, $parsed_query );

			$parsed_query = http_build_query( $parsed_query, '', '&amp;', PHP_QUERY_RFC3986 );

			$url = str_replace( $query, $parsed_query, $url );
		}

		return $url;
	}

	/**
	 * Retrieve recurring events for the sitemap.
	 *
	 * Fetches recurring events based on date range and limit filters.
	 *
	 * @since 7.4.3
	 *
	 * @return array Array of event objects.
	 */
	public function get_recurring_events() {
		// Get recurring events that start after today, end before a year from now, and limit to 1000 events.
		return tribe_events()
			->where( 'recurring', true )
			->where( 'starts_after', $this->get_starts_after_date() )
			->where( 'ends_before', $this->get_ends_before_date() )
			->per_page( $this->get_per_page_limit() )
			->all();
	}

	/**
	 * Get the most recent modification date from recurring events.
	 *
	 * @since 7.4.3
	 *
	 * @return string The formatted date in W3C format.
	 */
	public function get_latest_modified_date() {
		$latest_event = tribe_events()
			->where( 'recurring', true )
			->order_by( 'post_modified', 'DESC' )
			->first();

		$sitemap_date = $latest_event ? gmdate( DATE_W3C, strtotime( $latest_event->post_modified ) ) : gmdate( DATE_W3C );

		/**
		 * Filter the latest modified date used for sitemap timestamps.
		 *
		 * @since 7.4.3
		 *
		 * @param string        $sitemap_date The formatted date string in W3C format.
		 * @param \WP_Post|null $latest_event The most recently modified event or null if none found.
		 * @param Controller    $this         The current controller instance.
		 */
		return (string) apply_filters( 'tec_events_pro_yoast_seo_sitemap_latest_modified_date', $sitemap_date, $latest_event, $this );
	}

	/**
	 * Get the "starts_after" date for recurring events query.
	 *
	 * @since 7.4.3
	 *
	 * @return string The formatted date string used to filter events by start date.
	 */
	public function get_starts_after_date() {
		$starts_after = 'today';

		/**
		 * Filter the starts_after date for recurring events in sitemap.
		 *
		 * @since 7.4.3
		 *
		 * @param string     $starts_after The date to use for filtering events that start after.
		 * @param Controller $this         The current controller instance.
		 */
		return (string) apply_filters( 'tec_events_pro_yoast_seo_sitemap_starts_after_date', $starts_after, $this );
	}

	/**
	 * Get the "ends_before" date for recurring events query.
	 *
	 * @since 7.4.3
	 *
	 * @return string The formatted date string used to filter events by end date.
	 */
	public function get_ends_before_date() {
		$one_year_from_now = gmdate( 'Y-m-d', strtotime( '+1 year' ) );

		/**
		 * Filter the ends_before date for recurring events in sitemap.
		 *
		 * @since 7.4.3
		 *
		 * @param string     $ends_before The date to use for filtering events that end before.
		 * @param Controller $this        The current controller instance.
		 */
		return (string) apply_filters( 'tec_events_pro_yoast_seo_sitemap_ends_before_date', $one_year_from_now, $this );
	}

	/**
	 * Get the "per_page" limit for recurring events query.
	 *
	 * @since 7.4.3
	 *
	 * @return int The number of events to include in the sitemap.
	 */
	public function get_per_page_limit() {
		$per_page = 1000;

		/**
		 * Filter the per_page limit for recurring events in sitemap.
		 *
		 * @since 7.4.3
		 *
		 * @param int        $per_page The number of events to include in the sitemap.
		 * @param Controller $this     The current controller instance.
		 */
		return (int) apply_filters( 'tec_events_pro_yoast_seo_sitemap_per_page_limit', $per_page, $this );
	}
}
