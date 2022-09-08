<?php
/**
 * Class to replace the results from a list of queries.
 *
 * @since 6.0.0
 */

namespace TEC\Events_Pro\Custom_Tables\V1\WP_Query;


use TEC\Events\Custom_Tables\V1\Traits\With_WP_Query_Introspection;
use TEC\Events_Pro\Custom_Tables\V1\Models\Provisional_Post;
use Tribe__Events__Main as TEC;
use WP_Post;
use WP_Query;

use function get_post;

/**
 * Class Replace_Results
 *
 * @since   6.0.0
 *
 * @package src\WP_Query
 */
class Replace_Results {
	use With_WP_Query_Introspection;

	/**
	 * Instance to the Provisional Post class.
	 *
	 * @since 6.0.0
	 *
	 * @var Provisional_Post provisional_post
	 */
	private $provisional_post;

	/**
	 * Replace_Results constructor.
	 *
	 * @since 6.0.0
	 *
	 * @param  \TEC\Events_Pro\Custom_Tables\V1\Models\Provisional_Post $provisional_post
	 */
	public function __construct( Provisional_Post $provisional_post ) {
		$this->provisional_post = $provisional_post;
	}

	/**
	 * If the query was not able to find results for specific occurrence IDs we hydrate the cache
	 * before the results are returned to the next WP_Query call.
	 *
	 * @since 6.0.0
	 *
	 * @param                 $posts
	 * @param  WP_Query|null  $wp_query
	 *
	 * @return mixed
	 */
	public function replace( $posts, WP_Query $wp_query = null ) {
		// This should affect only to posts.
		if ( ! $this->is_query_for_post_type( $wp_query, TEC::POSTTYPE ) ) {
			return $posts;
		}

		// Prevent to operate on not array type of.
		if ( ! is_array( $posts ) ) {
			return $posts;
		}

		$occurrences = [];
		foreach ( $posts as $index => $post ) {
			if ( $post instanceof WP_Post ) {
				if ( empty( $post->occurrence_id ) ) {
					continue;
				}

				$occurrences[ $post->occurrence_id ] = $index;
				continue;
			}

			if ( $this->provisional_post->is_provisional_post_id( $post ) ) {
				$occurrences[ $post ] = $index;
			}
		}

		$this->provisional_post->hydrate_caches( array_keys( $occurrences ) );

		// Replace the posts with the occurrences instead.
		foreach ( $occurrences as $occurrence_id => $index ) {
			$occurrence_post = get_post( $occurrence_id );

			// When querying for Single Events, just return the Single Event real post ID.
			if ( isset( $occurrence_post->_tec_occurrence ) ) {
				if ( ! empty( $occurrence_post->_tec_occurrence->has_recurrence ) ) {
					// Recurring Event.
					$posts[ $index ] = $occurrence_post;
				} else {
					// Single Event.
					$posts[ $index ] = get_post( $occurrence_post->_tec_occurrence->post_id );
				}
			}
		}

		if ( $wp_query instanceof WP_Query && $wp_query->get( 'fields' ) === 'ids' ) {
			return array_filter(
				array_map(
					static function ( $post ) {
						if ( ! $post instanceof WP_Post ) {
							$post = get_post( $post );
						}

						return $post instanceof WP_Post ? $post->ID : 0;
					},
					$posts
				)
			);
		}

		return $posts;
	}
}
