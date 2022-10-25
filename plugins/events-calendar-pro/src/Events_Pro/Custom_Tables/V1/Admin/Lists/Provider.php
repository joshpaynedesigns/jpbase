<?php
/**
 * Handle the registration and hooking of the plugin integration and support of the Admin UI lists.
 *
 * @since   6.0.0
 *
 * @package TEC\Events_Pro\Custom_Tables\V1\Admin\Lists
 */

namespace TEC\Events_Pro\Custom_Tables\V1\Admin\Lists;

use TEC\Events\Custom_Tables\V1\Models\Event;
use TEC\Events\Custom_Tables\V1\Models\Occurrence;
use TEC\Events\Custom_Tables\V1\Updates\Requests;
use TEC\Events_Pro\Custom_Tables\V1\Admin\Links;
use TEC\Events_Pro\Custom_Tables\V1\Models\Series_Relationship;
use TEC\Events_Pro\Custom_Tables\V1\Series\Admin_List;
use TEC\Events_Pro\Custom_Tables\V1\Series\Post_Type as Series;
use Tribe__Date_Utils as Dates;
use Tribe__Events__Main as TEC;
use WP_Post;
use WP_Query;
use WP_Screen;

/**
 * Class Provider
 *
 * @since   6.0.0
 *
 * @package TEC\Events_Pro\Custom_Tables\V1\Admin\Lists
 */
class Provider extends \tad_DI52_ServiceProvider {

	/**
	 * Hooks on the Admin UI post lists to filter the options and values available.
	 *
	 * @since 6.0.0
	 */
	public function register() {
		$this->container->singleton( __CLASS__, $this );
		$this->container->singleton( Admin_List::class, Admin_List::class );
		$this->container->singleton( Links::class, Links::class );

		if ( 'GET' !== tribe( Requests::class )->from_http_request()->get_method() ) {
			// If this is not a GET request, then the methods below should not apply.
			return;
		}

		$series_post_type = Series::POSTTYPE;

		add_filter( 'get_edit_post_link', [ $this, 'update_event_edit_link' ], 10, 2 );

		// To run after PRO
		add_filter( 'post_type_link', [ $this, 'update_recurrence_view_link' ], 20, 4 );
		add_filter( "manage_{$series_post_type}_posts_columns", [ tribe( Admin_List::class ), 'include_custom_columns' ] );
		add_filter( "manage_edit-{$series_post_type}_sortable_columns", [ tribe( Admin_List::class ), 'include_sortable_columns' ] );
		add_filter( "posts_clauses", [ tribe( Admin_List::class ), 'filter_series_rows_clauses' ], 10, 2 );

		add_action( "manage_{$series_post_type}_posts_custom_column", [ tribe( Admin_List::class ), 'custom_column' ], 10, 2 );

		if ( is_admin() ) {
			add_filter( 'the_posts', [ $this, 'populate_admin_caches' ], 10, 2 );
		}

		// Admin UI modifications
		add_filter( 'manage_' . TEC::POSTTYPE . '_posts_columns', [ $this, 'events_columns' ] );
		add_action( 'manage_' . TEC::POSTTYPE . '_posts_custom_column', [ $this, 'event_recurrence_ui' ], 10, 2 );
		add_action( 'all_admin_notices', [ $this, 'render_recurrence_svg' ] );
	}

	/**
	 * Adds the Series columns to the Event admin list.
	 *
	 * @since 6.0.0
	 *
	 * @param array<string,string> $columns A list of the columns that will be shown to the user for
	 *                                      the Event post type as produced by WordPress or previous
	 *                                      filters.
	 *
	 * @return array<string,string> The filtered map of columns for the Event post type.
	 */
	public function events_columns( $columns ) {
		if ( ! is_array( $columns ) ) {
			return $columns;
		}

		$columns['series'] = __( 'Series', 'tribe-events-calendar-pro' );

		return $columns;
	}

	/**
	 * Populate the admin caches for Series.
	 *
	 * @since 6.0.0
	 *
	 * @param array    $posts
	 * @param WP_Query $query
	 *
	 * @return array
	 */
	public function populate_admin_caches( $posts, $query ) {
		if ( ! ( is_array( $posts ) && $query instanceof WP_Query ) ) {
			return $posts;
		}

		// The function might not exist in the context of the Customizer, not at this stage.
		$screen = function_exists( 'get_current_screen' ) ?
			get_current_screen()
			: null;

		if ( ! $screen instanceof WP_Screen ) {
			return $posts;
		}

		if ( $screen->post_type !== TEC::POSTTYPE ) {
			return $posts;
		}

		if ( ! $query->is_main_query() ) {
			return $posts;
		}

		$ids        = [];
		$events_ids = [];

		// TODO: Refactor into a more "elegant solution"
		foreach ( $posts as $post ) {
			if ( isset( $post->_tec_occurrence ) && $post->_tec_occurrence instanceof Occurrence ) {
				if ( ! isset( $ids[ $post->_tec_occurrence->event_id ] ) ) {
					$ids[ $post->_tec_occurrence->event_id ] = [];
				}

				$ids[ $post->_tec_occurrence->event_id ][] = $post->ID;
			} else {
				$events_ids[] = $post->ID;
			}
		}

		$events = [];
		if ( ! empty( $events_ids ) ) {
			$events = Event::where_in( 'post_id', $events_ids )->get();
		}

		foreach ( $events as $event ) {
			if ( ! isset( $ids[ $event->event_id ] ) ) {
				$ids[ $event->event_id ] = [];
			}
			$ids[ $event->event_id ][] = $event->post_id;
		}

		$series     = [];
		$events_ids = array_keys( $ids );
		if ( ! empty( $events_ids ) ) {
			$relationships = Series_Relationship::where_in( 'event_id', $series )->get();
			foreach ( $relationships as $relationship ) {
				$posts_ids = isset( $ids[ $relationship->event_id ] ) ? $ids[ $relationship->event_id ] : [];
				foreach ( $posts_ids as $post_id ) {
					$series[ $post_id ] = $relationship->series_post_id;
				}
			}
		}

		$_REQUEST['series'] = $series;

		return $posts;
	}

	/**
	 * Create a sprite of symbols to resize the SVG using a viewBox property. The sprite is rendered before the full
	 * events table.
	 *
	 * @since 6.0.0
	 */
	public function render_recurrence_svg() {
		// The function might not exist in the context of the Customizer, not at this stage.
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		if ( ! $screen instanceof WP_Screen ) {
			return;
		}

		if ( TEC::POSTTYPE !== $screen->post_type ) {
			return;
		}

		if ( $screen->base !== 'edit' ) {
			return;
		}

		?>
		<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
			<symbol id="recurring" viewBox="0 0 20 20">
				<path
					d="M13.333 3.826c0 .065 0 .13-.02.174 0 .022-.02.065-.02.087a.9.9 0 0 1-.197.37L10.45 7.37a.797.797 0 0 1-.592.26.797.797 0 0 1-.593-.26c-.316-.348-.316-.935 0-1.305l1.225-1.348H6.3C3.753 4.717 1.66 7 1.66 9.827c0 1.369.474 2.651 1.363 3.608.316.348.316.935 0 1.304A.797.797 0 0 1 2.43 15a.797.797 0 0 1-.593-.26C.652 13.434 0 11.695 0 9.847c0-3.826 2.825-6.935 6.301-6.935h4.208L9.284 1.565c-.316-.348-.316-.935 0-1.304.316-.348.85-.348 1.185 0l2.647 2.913c.099.109.158.239.198.37 0 .021.02.065.02.086v.196zM20 10.152c0 3.826-2.825 6.935-6.301 6.935H9.49l1.225 1.348c.336.348.336.935 0 1.304a.797.797 0 0 1-.593.261.83.83 0 0 1-.592-.26l-2.627-2.936a.948.948 0 0 1-.198-.37c0-.021-.02-.064-.02-.086-.02-.065-.02-.109-.02-.174 0-.065 0-.13.02-.174 0-.022.02-.065.02-.087a.9.9 0 0 1 .198-.37L9.55 12.63c.316-.347.849-.347 1.185 0 .336.348.336.935 0 1.305L9.51 15.283h4.208c2.548 0 4.641-2.283 4.641-5.11 0-1.369-.474-2.651-1.362-3.608a.97.97 0 0 1 0-1.304c.316-.348.849-.348 1.185 0C19.348 6.543 20 8.283 20 10.152z"/>
			</symbol>
		</svg>
		<?php
	}

	public function event_recurrence_ui( $column_name, $post_id ) {
		switch ( $column_name ) {
			case 'events-cats':
				$event_cats      = wp_get_post_terms(
					$post_id,
					TEC::TAXONOMY,
					[
						'fields' => 'names',
					]
				);
				$categories_list = '-';
				if ( is_array( $event_cats ) ) {
					$categories_list = implode( ', ', $event_cats );
				}
				echo esc_html( $categories_list );
				break;

			case 'start-date':
				$format = tribe_get_date_format( true );;
				$event = Event::find( $post_id, 'post_id' );
				if ( $event instanceof Event ) {
					$start_date = Dates::immutable( $event->start_date, $event->timezone );
					$total      = Occurrence::where( 'event_id', $event->event_id )->count();
					$title      = sprintf(
					/* translators: %d the number of total occurrences */
						_n( '%d occurrence', '%d occurrences', $total, 'tribe-events-calendar-pro' ),
						$total
					);

					if ( $event->has_recurrence() ) {
						echo '<div style="display: flex; align-items: start;">';
						echo '<span style="flex-grow: 1;">';
						echo esc_html( $start_date->format( $format ) );
						echo '</span>';
						echo '<svg style="margin-left: 10px; margin-top: 3px;" viewBox="0 0 12 12" width="12" height="12"><title>' . $title . '</title><use xlink:href="#recurring" /></svg>';
						echo '</div>';
					} else {
						echo esc_html( $start_date->format( $format ) );
					}
				} else {
					echo tribe_get_start_date( $post_id, false, $format );
				}

				break;

			case 'end-date':
				$format = tribe_get_date_format( true );
				$event  = Event::find( $post_id, 'post_id' );
				if ( $event instanceof Event ) {
					if ( $event->is_infinite() ) {
						echo '—';
					} else {
						$count = Occurrence::where( 'event_id', $event->event_id )
										   ->count();
						// If single occurrence and a multi-day, show end date
						if ( $event->is_multiday() && $count === 1 ) {
							$end_date = $event->end_date;

							// Single and recurring event, show the last occurrences start date
						} else {
							$occurrence = Occurrence::where( 'event_id', $event->event_id )
													->order_by( 'start_date', 'DESC' )
													->limit( 1 )
													->first();
							$end_date   = $occurrence->start_date;
						}
						$start_date = Dates::immutable( $end_date, $event->timezone );
						echo esc_html( $start_date->format( $format ) );
					}
				} else {
					echo tribe_get_display_end_date( $post_id, false, $format );
				}
				break;

			case 'series':
				$series = tribe_get_request_var( 'series', [] );
				if ( isset( $series[ $post_id ] ) ) {
					echo get_the_title( $series[ $post_id ] );
				}
				break;
		}
	}

	/**
	 * Update the edit link for an event to open the next available occurrence when editing a recurring event, for normal
	 * events just keep on using the default edit link.
	 *
	 * @since 6.0.0
	 *
	 * @param string $link    The edit link.
	 * @param int    $post_id Post ID.
	 *
	 * @return string|null The edit post link for the given post. Null if the post type does not exist or does not allow an editing UI.
	 */
	public function update_event_edit_link( string $link, int $post_id ): ?string {
		return $this->container->make( Links::class )->update_event_edit_link( $link, $post_id );
	}

	/**
	 * Updates the link to view an Occurrence in the context of the Administration UI.
	 *
	 * @since 6.0.0
	 *
	 * @param string  $post_link   The View post link, as produced by WordPress and previous filters.
	 * @param WP_Post $post        A reference to the Post instance.
	 * @param bool    $leavename   Whether to leave the post name in the link or not.
	 * @param bool    $sample      Whether the link is being produced for the purpose of providing a sample of
	 *                             the view link, or not.
	 *
	 * @return string The updated view link, if required.
	 */
	public function update_recurrence_view_link( $post_link, $post, $leavename, $sample ) {
		if ( ! $post instanceof WP_Post ) {
			return $post_link;
		}

		return $this->container->make( Links::class )->update_recurrence_view_link( $post_link, $post, $leavename, $sample );

	}
}
