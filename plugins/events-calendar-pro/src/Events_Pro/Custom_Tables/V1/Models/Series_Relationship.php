<?php
/**
 * Represents the relationship between a Series Post and an Event Post.
 *
 * The relationship is a many-to-many one, where a Series could be related to 0+ Events and
 * an Event could be related to 0+ Series.
 *
 * @since   6.0.0
 *
 * @package TEC\Events\Custom_Tables\V1\Models
 */

namespace TEC\Events_Pro\Custom_Tables\V1\Models;

use TEC\Events\Custom_Tables\V1\Models\Formatters\Integer_Key_Formatter;
use TEC\Events\Custom_Tables\V1\Models\Formatters\Numeric_Formatter;
use TEC\Events\Custom_Tables\V1\Models\Model;
use TEC\Events\Custom_Tables\V1\Models\Validators\Integer_Key;
use TEC\Events\Custom_Tables\V1\Models\Validators\Valid_Event;
use TEC\Events\Custom_Tables\V1\Models\Validators\Valid_Event_Model;
use TEC\Events_Pro\Custom_Tables\V1\Models\Validators\Valid_Series;
use WP_Post;

/**
 * Class Series_Relationship
 *
 * @since   6.0.0
 *
 * @package TEC\Events\Custom_Tables\V1\Models
 *
 * @property int relationship_id The unique ID of the Event to Series relationship.
 * @property int series_post_id  The Post ID of the Series side of the relationship.
 * @property int event_id        The ID of the Event, from the events table.
 * @property int event_post_id   The Post ID of the Event side of the relationship.
 */
class Series_Relationship extends Model {
	/**
	 * {@inheritdoc }
	 */
	protected $validations = [
		'relationship_id' => Integer_Key::class,
		'series_post_id'  => Valid_Series::class,
		'event_id'        => Valid_Event_Model::class,
		'event_post_id'   => Valid_Event::class,
	];

	/**
	 * {@inheritdoc }
	 */
	protected $formatters = [
		'relationship_id' => Integer_Key_Formatter::class,
		'series_post_id'  => Numeric_Formatter::class,
		'event_id'        => Numeric_Formatter::class,
		'event_post_id'   => Numeric_Formatter::class,
	];

	/**
	 * {@inheritdoc}
	 */
	protected $table = 'tec_series_relationships';

	/**
	 * {@inheritdoc}
	 */
	protected $primary_key = 'relationship_id';

	/**
	 * Cast the value from the database into an integer as all values are returned as strings.
	 *
	 * @since 6.0.0
	 *
	 * @param  string  $value  The raw value from the database.
	 *
	 * @return int The formatted attribute value.
	 */
	public function get_event_post_id_attribute( $value ) {
		return (int) $value;
	}

	/**
	 * Cast the value from the database into an integer as all values are returned as strings.
	 *
	 * @since 6.0.0
	 *
	 * @param  string  $value  The raw value from the database.
	 *
	 * @return int The formatted attribute value.
	 */
	public function get_event_id_attribute( $value ) {
		return (int) $value;
	}

	/**
	 * Cast the value from the database into an integer as all values are returned as strings.
	 *
	 * @since 6.0.0
	 *
	 * @param  string  $value  The raw value from the database.
	 *
	 * @return int The formatted attribute value.
	 */
	public function get_series_post_id_attribute( $value ) {
		return (int) $value;
	}

	/**
	 * Cast the value from the database into an integer as all values are returned as strings.
	 *
	 * @since 6.0.0
	 *
	 * @param  string  $value  The raw value from the database.
	 *
	 * @return int The formatted attribute value.
	 */
	public function get_series_id_attribute( $value ) {
		return (int) $value;
	}

	/**
	 * Generate a cache key for an Event Post ID.
	 *
	 * @since 6.0.0
	 *
	 * @param string|int $post_id
	 * @param bool       $by_occurrence
	 *
	 * @return string
	 */
	public static function get_cache_key( $post_id, $by_occurrence = false ) {
		$cache_key = 'tec_series_relationships_%1$d' . ( $by_occurrence ? '_occurrence' : '' );
		return sprintf( $cache_key, $post_id );
	}

	/**
	 * Primes the cache for series relationships for a set of posts.
	 *
	 * @since 6.0.0
	 *
	 * @param array<WP_Post> $posts The posts to prime the cache for.
	 *
	 * @return array
	 */
	public static function prime_cache( $posts ) {
		$cache = tribe_cache();

		// Prevents Errors for posts that don't have occurrence ID.
		$posts = array_filter( (array) $posts, static function ( $post ) {
			return $post instanceof \WP_Post && isset( $post->_tec_occurrence );
		} );

		$occurrences = wp_list_pluck( $posts, '_tec_occurrence' );
		$post_ids = array_unique( wp_list_pluck( $occurrences, 'post_id' ) );
		$all = [];

		// Prevent running a Query for already primed cache post IDs.
		foreach ( $post_ids as $i => $id ) {
			$cache_key = static::get_cache_key( $id );
			if ( isset( $cache[ $cache_key ] ) ) {
				$all[] = $cache[ $cache_key ];
				unset( $post_ids[ $i ] );
			} else {
				$cache[ $cache_key ] = null;
			}
		}

		if ( empty( $post_ids ) ) {
			return array_filter( $all );
		}

		$all = array_merge( $all, static::where_in( 'event_post_id', $post_ids )->get() );
		$all = array_filter( $all );

		$series_relationship_ids = wp_list_pluck( $all, 'event_post_id' );
		$series_relationship_ids = array_filter( $series_relationship_ids, 'is_numeric' );
		$series_relationship_ids = array_flip( $series_relationship_ids );

		foreach( $post_ids as $event_post_id ) {
			$relationship = null;
			if ( isset( $series_relationship_ids[ $event_post_id ] ) ) {
				$relationship_key = $series_relationship_ids[ $event_post_id ];
				$relationship = $all[ $relationship_key ];
			}
			$cache_key = static::get_cache_key( $event_post_id );

			// Saves the cache.
			$cache[ $cache_key ] = $relationship;
		}

		return array_filter( $all );
	}
}
