<?php
/**
 * Hooks on the WordPress IDENTIFY, WRITE and READ phases to update
 * an Event and all its Occurrences.
 *
 * @since   6.0.0
 *
 * @package TEC\Events_Pro\Custom_Tables\V1\Updates\Update_Controllers
 */

namespace TEC\Events_Pro\Custom_Tables\V1\Updates\Update_Controllers;

use DateTimeZone;
use TEC\Events_Pro\Custom_Tables\V1\Events\Provisional\ID_Generator;
use TEC\Events\Custom_Tables\V1\Models\Occurrence;
use TEC\Events_Pro\Custom_Tables\V1\Events\Rules\Date_Rule;
use TEC\Events_Pro\Custom_Tables\V1\Models\Provisional_Post;
use TEC\Events_Pro\Custom_Tables\V1\RRule\Occurrence as RRule_Ocurrence;
use TEC\Events_Pro\Custom_Tables\V1\Updates\Events;
use Tribe__Events__Pro__Editor__Recurrence__Blocks;
use WP_Post;
use TEC\Events_Pro\Custom_Tables\V1\Updates\Transient_Occurrence_Redirector as Occurrence_Redirector;
use Tribe__Events__Pro__Editor__Recurrence__Blocks_Meta as Blocks_Meta;
use Tribe__Date_Utils as Dates;

/**
 * Class All
 *
 * @since   6.0.0
 *
 * @package TEC\Events_Pro\Custom_Tables\V1\Updates\Update_Controllers
 */
class All implements Update_Controller_Interface {
	use Update_Controller_Methods;

	/**
	 * The target post ID.
	 *
	 * @since 6.0.0
	 *
	 * @var int
	 */
	private $target_id;

	/**
	 * The request start date, in `Y-m-d H:i:s` format.
	 *
	 * @since 6.0.0
	 *
	 * @var string
	 */
	private $request_start_date;

	/**
	 * A reference to the current Provision Post handler implementation.
	 *
	 * @since 6.0.0
	 *
	 * @var Provisional_Post
	 */
	private $provisional_post;

	/**
	 * A reference to the Events repository.
	 *
	 * @since 6.0.0
	 *
	 * @var Events
	 */
	private $events;

	/**
	 * A reference to the current implementation of the Occurrence redirector.
	 *
	 * @since 6.0.0
	 *
	 * @var Occurrence_Redirector
	 */
	private $occurrence_redirector;

	/**
	 * All constructor.
	 *
	 * @since 6.0.0
	 *
	 * @param Provisional_Post      $provisional_post      A reference to the current Provision Post handler
	 *                                                     implementation.
	 * @param Events                $events                A reference to the current Events repository handler.
	 * @param Occurrence_Redirector $occurrence_redirector A reference to the current implementation of the Occurrence
	 *                                                     redirector.
	 */
	public function __construct(
		Provisional_Post $provisional_post,
		Events $events,
		Occurrence_Redirector $occurrence_redirector
	) {
		$this->provisional_post = $provisional_post;
		$this->events           = $events;
		$this->occurrence_redirector = $occurrence_redirector;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 6.0.0
	 */
	public function apply_before_identify_step( $post_id ) {
		if ( false === $this->check_step_requirements( $post_id ) ) {
			return false;
		}

		// Never redirect an RDATE Occurrence request if it's the first Occurrence or the real post ID.
		if (
			$this->occurrence->is_rdate
			&& ! Occurrence::is_first( $this->occurrence )
			&& $this->provisional_post->is_provisional_post_id( $post_id )
		) {
			$first = Occurrence::where( 'post_id', '=', $this->occurrence->post_id )
				->order_by( 'start_date', 'ASC' )
				->first();

			if ( $first instanceof Occurrence ) {
				$this->redirect_rdate_update_to_occurrence( $this->occurrence, $first, $first->post_id );
			}
		}

		$this->save_request_id( $post_id );

		$target_id        = $this->occurrence->post_id;

		if ( empty( $target_id ) ) {
			$target_id = $post_id;
		}

		$this->target_id = $target_id;

		if ( null !== $this->occurrence->occurrence_id ) {
			// This branch will be taken for new posts.
			$this->request_start_date = $this->occurrence->start_date;
			$adjusted_dates = $this->events->adjust_request_dates( $this->request, $this->occurrence );
			foreach ( $adjusted_dates as $key => $value ) {
				$_REQUEST[ $key ] = $value;
				$_POST [ $key ]   = $value;
			}
		}

		/*
		 * After the custom table updates, make sure the Request is redirected to either an existing
		 * Occurrence, or the real Event post ID.
		 */
		add_filter( 'tec_events_custom_tables_v1_redirect_post_location', [
			$this,
			'redirect_to_existing_occurrence'
		], 20, 2 );

		// After updates are done, the current Occurrence might not exist, if so: redirect it.
		add_action( 'tec_events_custom_tables_v1_update_post_after', [ $this, 'setup_occurrence_redirection' ] );

		$this->save_rest_request_recurrence_meta( $target_id, $this->request );

		return $target_id;
	}

	/**
	 * After the custom tables updates are committed, redirect the Request to
	 * the original Occurrence, if it still exists, else to the real Event ID.
	 *
	 * @since 6.0.0
	 *
	 * @param string $location The HTTP location WordPress, and other intervening
	 *                         methods would redirect the browser to.
	 * @param int    $post_id  The post ID the request should be redirected for.
	 *
	 * @return string The URL the browser should be redirected to for the post.
	 */
	public function redirect_to_existing_occurrence( $location, $post_id ) {
		// Remove this Update Controller from the current action.
		remove_action( current_action(), [ $this, 'redirect_to_existing_occurrence' ] );

		if ( (int) $post_id !== $this->target_id ) {
			// Only apply if the post ID and request are about the same Event.
			return $location;
		}

		if (
			! $this->provisional_post->is_provisional_post_id( $this->request_id )
			&& get_post( $this->request_id ) instanceof WP_Post
		) {
			/*
			 * The Request was for a real Event post ID: let it go to that.
			 */
			return get_edit_post_link( $this->request_id, 'internal' );
		}

		$occurrence_id = $this->provisional_post->normalize_provisional_post_id( $this->request_id );
		$occurrence    = Occurrence::find( $occurrence_id, 'occurrence_id' );

		if (
			! $occurrence instanceof Occurrence && get_post( $this->target_id ) instanceof WP_Post
		) {
			$this->setup_occurrence_redirection();

			/*
			 * The Request was for an Occurrence, but updates have removed the Occurrence: redirect
			 * to real Event post ID.
			 */
			return get_edit_post_link( $this->target_id, 'internal' );
		}

		if ( $occurrence instanceof Occurrence ) {
			/*
			 * Redirect the request to the requested Occurrence.
			 */
			return get_edit_post_link( $this->request_id, 'internal' );
		}

		// Ok, we tried and could not find a fitting Event or Occurrence.
		return $location;
	}

	/**
	 * Sets up the transient that will redirect requests for the Occurrence to the real
	 * post ID for some time.
	 *
	 * @since 6.0.0
	 *
	 * @return void The method does not return any value and will have the side-effect
	 *              of setting up a transient to redirect the Occurrence.
	 */
	public function setup_occurrence_redirection() {
		if ( ! $this->provisional_post->is_provisional_post_id( $this->request_id ) ) {
			return;
		}

		$occurrence_id = tribe( ID_Generator::class )->unprovide_id( $this->request_id );

		if ( 1 === Occurrence::where( 'occurrence_id', '=', $occurrence_id )->count() ) {
			return;
		}

		// Following requests for the Occurrence should be redirected to the Event post ID.
		$this->occurrence_redirector->set_redirected_id( $this->target_id, $this->request_id, $this->request_start_date );
	}
}
