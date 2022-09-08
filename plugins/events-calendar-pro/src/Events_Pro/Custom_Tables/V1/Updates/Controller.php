<?php
/**
 * Subscribes to TEC-provided actions and filters to update
 * Event custom tables following scenarios provided by ECP.
 *
 * @since   6.0.0
 *
 * @package TEC\Events_Pro\Custom_Tables\V1\Updates
 */

namespace TEC\Events_Pro\Custom_Tables\V1\Updates;

use TEC\Events\Custom_Tables\V1\Models\Occurrence;
use TEC\Events\Custom_Tables\V1\Updates\Requests;
use TEC\Events_Pro\Custom_Tables\V1\Models\Series_Relationship;
use TEC\Events_Pro\Custom_Tables\V1\Series\Relationship;
use TEC\Events_Pro\Custom_Tables\V1\Traits\With_Event_Recurrence;
use TEC\Events_Pro\Custom_Tables\V1\Updates\Update_Controllers\Update_Controller_Interface as Update_Controller;
use TEC\Events_Pro\Custom_Tables\V1\Updates\Transient_Occurrence_Redirector as Occurrence_Redirector;
use WP_Post;
use WP_REST_Request as Request;
use Tribe__Events__Main as TEC;
use WP_REST_Response;

/**
 * Class Controller
 *
 * @since   6.0.0
 *
 * @package TEC\Events_Pro\Custom_Tables\V1\Updates
 */
class Controller {
	use With_Event_Recurrence;

	/**
	 * A reference to the current Requests factory/repository implementation.
	 *
	 * @since 6.0.0
	 *
	 * @var Requests
	 */
	private $requests;

	/**
	 * A reference to the current Redirector implementation.
	 *
	 * @since 6.0.0
	 *
	 * @var Redirector
	 */
	private $redirector;
	/**
	 * A reference to the current Updates controller factory implementation.
	 *
	 * @since 6.0.0
	 *
	 * @var Updates
	 */
	private $updates;

	/**
	 * A reference to the Update Controller that is handling the main Request update.
	 *
	 * @since 6.0.0
	 *
	 * @var Update_Controller|null
	 */
	private $update_controller;

	/**
	 * A reference to the current Event repository implementation.
	 *
	 * @since 6.0.0
	 *
	 * @var Events
	 */
	private $events;

	/**
	 * A reference to the current Occurrence redirector implementation.
	 *
	 * @since 6.0.0
	 *
	 * @var Occurrence_Redirector
	 */
	private $occurrence_redirector;

	/**
	 * A reference to the current Blocks (Editor) Meta handler.
	 *
	 * @since 6.0.0
	 *
	 * @var Blocks_Meta
	 */
	private $blocks_meta;

	/**
	 * Controller constructor.
	 *
	 * @since 6.0.0
	 *
	 * @param Requests              $requests              A reference to the current Requests factory/repository implementation.
	 * @param Redirector            $redirector            A reference to the current Redirector implementation.
	 * @param Updates               $updates               A reference to the current Updates controller factory implementation.
	 * @param Events                $events                A reference to the current Event repository implementation.
	 * @param Occurrence_Redirector $occurrence_redirector A reference to the current implementation of the
	 *                                                     Occurrence redirector.
	 */
	public function __construct(
		Requests $requests,
		Redirector $redirector,
		Updates $updates,
		Events $events,
		Occurrence_Redirector $occurrence_redirector,
		Blocks_Meta $blocks_meta
	) {
		$this->requests              = $requests;
		$this->redirector            = $redirector;
		$this->updates               = $updates;
		$this->events                = $events;
		$this->occurrence_redirector = $occurrence_redirector;
		$this->blocks_meta = $blocks_meta;
	}

	/**
	 * Delete the Pro data.
	 *
	 * @since 6.0.0
	 *
	 * @param int $post_id Post ID to be deleted.
	 *
	 * @return int Rows affected.
	 */
	public function delete( $post_id ) {
		return $this->events->delete( $post_id );
	}

	/**
	 * Deletes the occurrence transients tied to this post_id.
	 *
	 * @since 6.0.0
	 *
	 * @param numeric $post_id The post ID to delete occurrence transients for.
	 *
	 * @return bool
	 */
	public function delete_occurrence_transients( $post_id ) {
		return $this->events->delete_occurrence_transients( $post_id );
	}

	/**
	 * Redirects a Classic Editor request to either the real
	 * post when editing All Occurrences of a Recurring Event, or
	 * to a new post when applying edits to a Single Event or the
	 * Upcoming ones.
	 *
	 * @since 6.0.0
	 *
	 * @return int|false Either the post ID the request has been redirected to,
	 *                   or `false` if the request was not redirected.
	 */
	public function redirect_classic_editor_request() {
		// Use a REST Request object to model the HTTP Request.
		$request = $this->requests->from_http_request();

		return $this->redirect_request( $request );
	}

	/**
	 * Redirects a REST request to the correct post ID, if required
	 * by the Request and Update types.
	 *
	 * @since 6.0.0
	 *
	 * @param Request $request         A reference to the object modeling
	 *                                 the Request to redirect.
	 *
	 * @return int|false Either the post ID the request has been redirected to,
	 *                   or `false` if the request was not redirected.
	 */
	public function redirect_request( Request $request ) {
		if ( ( $redirected_id = $this->redirect_removed_occurrence( $request ) ) ) {
			$this->redirector->redirect_request( $request, $redirected_id );
		}

		if ( ! $this->requests->is_update_request( $request ) ) {
			// Not the kind of request we need to redirect.
			return false;
		}

		/*
		 * We assume the `id` param will be set as it would not have passed
		 * the previous check.
		 */
		$request_id = (int) $request->get_param( 'id' );

		$update_controller = $this->updates->for_request( $request );

		if ( ! $update_controller instanceof Update_Controller ) {
			return false;
		}

		$this->update_controller = $update_controller;

		$redirected_id = $update_controller->apply_before_identify_step( $request_id );

		if ( $redirected_id === $request_id || empty( $redirected_id ) ) {
			// Nothing to do here.
			return $request_id;
		}

		// Redirect the Request and update the auth.
		return $this->redirector->redirect_request( $request, $redirected_id );
	}

	/**
	 * Prune the just updated Occurrences by sequence number, dropping any one belonging to
	 * the previous sequence.
	 *
	 * @since 6.0.0
	 *
	 * @param int $post_id The ID of the Event post the Occurrences are being saved for.
	 *
	 * @return int|false The number of occurrences deleted or false if missing sequence.
	 */
	public function prune_occurrences_by_sequence( $post_id ) {
		return $this->events->prune_occurrences( $post_id );
	}

	/**
	 * Saves an Event recurrence meta to the database.
	 *
	 * This method is pretty much the same has the
	 * `Tribe__Events__Pro__Recurrence__Meta::updateRecurrenceMeta` one,
	 * minus the children Event generation.
	 *
	 * @since 6.0.0
	 *
	 * @param int                 $event_id The Event post ID.
	 * @param array<string,mixed> $data     The whole Event data, including the Recurrence
	 *                                      data.
	 *
	 * @return bool Whether the meta was saved or not.
	 */
	public function save_recurrence_meta( $event_id, $data ) {
		return $this->events->save_recurrence_meta( $event_id, $data );
	}

	/**
	 * Filter the TEC Occurrence match to return one matched by dates and post ID.
	 *
	 * @since 6.0.0
	 *
	 * @param Occurrence|null $occurrence Either a reference to an existing, matching, Occurrence
	 *                                    or `null`.
	 * @param Occurrence      $result     A reference to the Occurrence model instance that will be inserted
	 *                                    if a matching Occurrence cannot be found.
	 * @param int             $post_id    The post ID of the Event the Occurrence match is being searched for.
	 *
	 * @return Occurrence|null Either the reference to an existing Occcurrence matching the one
	 *                          that should be inserted, or `null` to indicate none was found.
	 */
	public function get_occurrence_match( $occurrence, $result, $post_id ) {
		return $this->events->get_occurrence_match( $occurrence, $result, $post_id );
	}

	/**
	 * After the Events and Occurrences custom tables have been updated following the
	 * request, update the Event to Series relationships with what data is specified
	 * in the Request.
	 *
	 * @since 6.0.0
	 *
	 * @param int     $post_id The Event post ID.
	 * @param Request $request A reference to the Request object that triggered the
	 *                         updated.
	 *
	 * @return true The method will always return `true` to indicate the update was
	 *              successful: Event to Series relationship for an Event post and
	 *              Request couple could not be created for good reasons.
	 */
	public function commit_post_updates_after( $post_id, Request $request ) {
		$this->events->update_relationships( $post_id, $request );

		return true;
	}

	/**
	 * Determines if we should update any of the custom table data for this Request.
	 * This is where we hook into related data such as associating Series to an Event.
	 *
	 * @since 6.0.0
	 *
	 * @param bool    $should_update         Whether the post custom tables should be updated or not,
	 *                                       according to The Events Calendar default logic and previous
	 *                                       methods filtering the value.
	 * @param int     $post_id               The ID of the post currently being updated.
	 * @param Request $request               A reference to object modeling the current Request.
	 *
	 * @return bool Whether the custom tables should be updated or not, taking the input
	 *              value into account.
	 */
	public function should_update_custom_tables( $should_update, $post_id, Request $request ) {
		return $should_update || $request->has_param( Relationship::EVENTS_TO_SERIES_REQUEST_KEY );
	}

	/**
	 * Returns the post ID the request for an Occurrence should be redirected to
	 * if the request targets a removed Occurrence.
	 *
	 * @since 6.0.0
	 *
	 * @param Request $request A reference to the Request to check.
	 *
	 * @return int Either the post ID the request should be redirected to, or `0`
	 *             if the request is not for a removed Occurrence and should not
	 *             be redirected.
	 */
	private function redirect_removed_occurrence( Request $request ) {
		$request_id = $request->get_param( 'id' );

		$data = $this->occurrence_redirector->get_redirect_data( $request_id );

		if ( empty( $data ) ) {
			return 0;
		}

		return isset( $data['redirect_id'] ) ? (int) $data['redirect_id'] : 0;
	}

	/**
	 * Sync's the block recurrence metadata with our classic _EventRecurrence metadata.
	 *
	 * @since 6.0.0
	 *
	 * @param int    $post_id    The ID of the post whose meta is being updated.
	 * @param string $meta_key   The meta key of the filtered update.
	 * @param mixed  $meta_value The value of the filtered update.
	 *
	 * @return bool Whether the meta update was correctly applied or not.
	 */
	public function sync_from_classic_format( $post_id, $meta_key, $meta_value ) {
		if ( $meta_key !== '_EventRecurrence' ) {
			return false;
		}

		$updated   = true;

		$meta_value = $this->add_off_pattern_flag_to_meta_value( $meta_value, $post_id );

		$converted = $this->blocks_meta->from_classic_format( $meta_value );

		if ( empty( $converted ) || 0 === count( array_filter( $converted ) ) ) {
			$this->blocks_meta->delete_blocks_meta( $post_id );
		}

		foreach ( $converted as $key => $value ) {
			$updated &= update_post_meta( $post_id, $key, $value );
		}

		return $updated;
	}

	/**
	 * For recurring events, if the event date is not specified, we should go to the series page
	 * instead of locating the random occurrence to display here.
	 */
	public function redirect_single_view() {
		// Redirect to the Series page
		global $wp_query, $post;

		// If we are on a TEC single event, without the event date defined,
		// we should redirect to the series page if this is a recurring event.
		if ( ! empty( $wp_query->query_vars['eventDate'] )
		     || ! isset( $wp_query->query_vars['eventDisplay'] )
		     || ! isset( $wp_query->query_vars['post_type'] ) ) {
			return;
		}

		if ( $wp_query->query_vars['post_type'] !== TEC::POSTTYPE ) {
			return;
		}

		if ( $wp_query->query_vars['eventDisplay'] !== 'single-event' ) {
			return;
		}

		if ( ! $post instanceof WP_Post ) {
			return;
		}

		// Do we have a Series and is this a Recurring Event?
		$post_id = Occurrence::normalize_id( $post->ID );
		if ( ! $post_id ) {
			return;
		}

		// Is it recurring?
		$count = Occurrence::where( 'post_id', $post_id )->count();
		if ( $count < 2 ) {
			return;
		}

		// In a series?
		$series = Series_Relationship::find( $post_id, 'event_post_id' );
		if ( $series instanceof Series_Relationship ) {
			wp_redirect( get_post_permalink( $series->series_post_id ) );
			exit;
		}
	}

	/**
	 * Redirect an Occurrence when deleted or trashed.
	 *
	 * @since 6.0.0
	 *
	 * @param WP_Post          $post     A reference to the post object that is being deleted or trashed.
	 * @param WP_REST_Response $response A reference to the REST response generated for the delete or trash request.
	 * @param Request          $request  A reference to the REST request that triggered the post trash or deletion.
	 *
	 * @return void The method will modify the response data.
	 */
	public function redirect_deleted_occurrence( WP_Post $post, WP_REST_Response $response, Request $request ): void {
		$occurrence_redirect_data = $this->occurrence_redirector->get_occurrence_redirect_response( $request->get_param( 'id' ) );
		// Either redirect to the correct Occurrence, or to the Events list.
		$location = $occurrence_redirect_data->location
		            ?? admin_url( '/edit.php?post_type=' . TEC::POSTTYPE );
		$response->data['_tec_redirect_url'] = $location;
	}

	/**
	 * Redirects a DELETE request to the correct post ID, if required.
	 *
	 * This method acts as a specialized proxy to the default redirection method
	 * to ensure the Request object will conform to the expected format.
	 *
	 * @since 6.0.0
	 *
	 * @param Request $request A reference to the request that triggered the post deletion.
	 *
	 * @return false|int Either the post ID to redirect to, or `false` if the request should not be redirected.
	 */
	public function redirect_delete_request( Request $request ) {
		$id = $request->has_param( 'id' ) ? $request->get_param( 'id' ) : null;

		if ( empty( $id ) || $request->get_method() !== \WP_REST_Server::DELETABLE ) {
			return false;
		}

		// We can make this check safely using an Occurrence provisional ID or a real post ID.
		if ( get_post_field( 'post_type', $id ) !== TEC::POSTTYPE ) {
			// Not an Event post, bail.
			return false;
		}

		/*
		 * The Blocks editor will not handle a Trash request correctly when posts should be just
		 * deleted; see https://github.com/WordPress/gutenberg/issues/13024
		 * To cope with that, set the `force` parameter of the request to let the deletion
		 * work correctly and not show the error message to the user.
		 */
		$force = defined( 'EMPTY_TRASH_DAYS' ) && (int) EMPTY_TRASH_DAYS === 0;
		$request->set_param( 'force', $force );

		return $this->redirect_request( $request );
	}
}
