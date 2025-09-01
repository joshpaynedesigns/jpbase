<?php
/**
 * Controller class for WP All Export plugin.
 *
 * @since 7.6.1
 *
 * @package TEC\Events_Pro\Integrations\Plugins\WP_All_Export
 */

namespace TEC\Events_Pro\Integrations\Plugins\WP_All_Export;

use TEC\Common\Integrations\Traits\Plugin_Integration;
use TEC\Events\Custom_Tables\V1\Models\Occurrence;
use TEC\Events_Pro\Custom_Tables\V1\Series\Post_Type as Series_Post_Type;
use TEC\Events_Pro\Integrations\Integration_Abstract;

/**
 * Class Controller
 *
 * @since 7.6.1
 *
 * @package TEC\Events_Pro\Integrations\Plugins\WP_All_Export
 */
class Controller extends Integration_Abstract {
	use Plugin_Integration;

	/**
	 * {@inheritDoc}
	 *
	 * @since 7.6.1
	 *
	 * @return string
	 */
	public static function get_slug(): string {
		return 'wp-all-export';
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 6.4.0
	 *
	 * @return bool Whether or not integrations should load.
	 */
	public function load_conditionals(): bool {
		return defined( 'PMXE_EDITION' );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 7.6.1
	 *
	 * @return void
	 */
	public function load(): void {
		add_filter( 'wp_all_export_csv_rows', [ $this, 'filter_csv_rows' ], 10, 3 );
	}

	/**
	 * Filter the CSV rows and add the post IDs linked to the series to the CSV.
	 *
	 * @since 7.6.1
	 *
	 * @param array<int, array>   $articles       The articles to filter.
	 * @param array<mixed, mixed> $export_options The export options.
	 * @param int                 $export_id      The export ID.
	 *
	 * @return array<int, array>
	 */
	public function filter_csv_rows( $articles, $export_options, $export_id ) {
		// Bail if $articles is not an array.
		if ( ! is_array( $articles ) ) {
			return $articles;
		}

		foreach ( $articles as $key => $values ) {
			// Skip if $values is not an array.
			if ( ! is_array( $values ) ) {
				continue;
			}

			// Get the post ID of the article being imported.
			// Note, the free version of WP All Export uses `id`, while the Pro version users 'ID'.
			$post_id = $values['id'] ?? $values['ID'];

			// Bail from the whole process if not a series.
			if ( get_post_type( $post_id ) !== Series_Post_Type::POSTTYPE ) {
				return $articles;
			}

			// Get the post IDs of the events in the series.
			$post_ids = tribe_events()->where( 'in_series', $post_id )->pluck( 'ID' );

			// Normalize the IDs.
			$normalized_ids = array_unique(
				array_map(
					[ Occurrence::class, 'normalize_id' ],
					$post_ids
				)
			);

			// Add the post IDs to the article.
			$articles[ $key ]['posts_in_series'] = implode( ',', $normalized_ids );
		}

		return $articles;
	}
}
