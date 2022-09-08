<?php
/**
 * Redirects Requests and HTTP super-globals to a specified Event or Occurrence.
 *
 * @since   6.0.0
 *
 * @package TEC\Events_Pro\Custom_Tables\V1\Updates
 */

namespace TEC\Events_Pro\Custom_Tables\V1\Updates;

use WP_REST_Request;

/**
 * Class Redirector
 *
 * @since   6.0.0
 *
 * @package TEC\Events_Pro\Custom_Tables\V1\Updates
 */
class Redirector {

	/**
	 * Redirects a Request object to a new post ID.
	 *
	 * @since 6.0.0
	 *
	 * @param WP_REST_Request $request A reference to the Request object.
	 * @param int             $post_id The post ID to redirect the request to.
	 *
	 * @return int $post_id The post ID the request was redirected to.
	 */
	public function redirect_request( WP_REST_Request $request, int $post_id ): int {
		$request->set_param( 'id', (int) $post_id );
		$this->redirect_http_superglobals( $post_id );
		$action = $request->get_param( 'action' );
		$this->update_request_nonce( $post_id, (string) $action );

		return $post_id;
	}

	/**
	 * Redirects the HTTP super-globals used by WordPress, and our plugins,
	 * to identify the post ID object of a Request.
	 *
	 * @since 6.0.0
	 *
	 * @param int $post_id The post ID to redirect the HTTP super-globals to.
	 */
	public function redirect_http_superglobals( $post_id ) {
		// Point the Classic Editor request to this event.
		if ( isset( $_POST['post_ID'] ) ) {
			// Edit request.
			$_POST['post_ID'] = $post_id;
			$_REQUEST['post_ID'] = $post_id;
		}

		if ( isset( $_GET['post'] ) ) {
			// Trash request.
			$_GET['post'] = $post_id;
			$_REQUEST['post'] = $post_id;
		}
	}

	/**
	 * Updates the Request and HTTP super-globals to provide a valid nonce for the
	 * post ID and action couple.
	 *
	 * @since 6.0.0
	 *
	 * @param int    $post_id The post ID to update the request nonce for.
	 * @param string $action  The action to update the request nonce for.
	 *
	 * @return string The new nonce that has been generated for the Request.
	 */
	public function update_request_nonce( $post_id, $action ) {
		$full_action = $this->get_nonce_action( $action, $post_id );

		/**
		 * Filters the action that will be used to create the nonce
		 * to legitimize a redirected post update request.
		 *
		 * @since 6.0.0
		 *
		 * @param string $full_action The action as worked out by the method.
		 * @param int    $post_id     The ID of the Post object is being redirected to.
		 * @param string $action      The action input of this method.
		 */
		$full_action = apply_filters( 'tec_events_custom_tables_v1_redirect_nonce_action', $full_action, $post_id, $action );

		$nonce                = wp_create_nonce( $full_action );
		$_REQUEST['_wpnonce'] = $nonce;

		return $nonce;
	}

	/**
	 * Returns the action name corresponding to a Classic Editor request and post ID.
	 *
	 * @since 6.0.0
	 *
	 * @param string $action  The request as provided in the Classic Editor Request.
	 * @param int    $post_id The post ID to build the nonce for.
	 *
	 * @return string The name of the action that should be used to verify or create a request
	 *                nonce.
	 */
	private function get_nonce_action( $action, $post_id ) {
		$action_map = [
			'editpost' => 'update-post_' . $post_id,
			'trash'    => 'trash-post_' . $post_id,
			'delete'   => 'delete-post_' . $post_id,
			'untrash'  => 'untrash-post_' . $post_id,
		];

		return isset( $action_map[ $action ] ) ? $action_map[ $action ] : $action . '_' . $post_id;
	}
}