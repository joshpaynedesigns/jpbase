<?php
/**
 * Handles the interaction w/ Microsoft API.
 *
 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
 *
 * @package Tribe\Events\Virtual\Meetings\Microsoft
 */

namespace Tribe\Events\Virtual\Meetings\Microsoft;

use Tribe\Events\Virtual\Encryption;
use Tribe\Events\Virtual\Template_Modifications;

/**
 * Class Api
 *
 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
 *
 * @package Tribe\Events\Virtual\Meetings\Microsoft
 */
class Api extends Account_API {

	/**
	 * {@inheritDoc}
	 */
	public static $api_name = 'Microsoft';

	/**
	 * {@inheritDoc}
	 */
	public static $api_id = 'microsoft';

	/**
	 * The base URL of the Microsoft REST API, v1.
	 *
	 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
	 *
	 * @var string
	 */
	public static $api_base = 'https://graph.microsoft.com/v1.0/';

	/**
	 * The User URL of the Microsoft REST API, v1.
	 *
	 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
	 *
	 * @var string
	 */
	public static $user_base = 'https://graph.microsoft.com/v1.0/me';

	/**
	 * The Encryption provider.
	 *
	 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
	 *
	 * @var Encryption
	 */
	public $encryption;

	/**
	 * {@inheritDoc}
	 */
	const POST_RESPONSE_CODE = 201;

	/**
	 * Api constructor.
	 *
	 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
	 *
	 * @param Encryption             $encryption             An instance of the Encryption handler.
	 * @param Template_Modifications $template_modifications An instance of the Template_Modifications handler.
	 * @param Actions                $actions                An instance of the Actions name handler.
	 * @param URL                    $url                    An instance of the URL handler.
	 */
	public function __construct( Encryption $encryption, Template_Modifications $template_modifications, Actions $actions, Url $url ) {
		$this->encryption             = ( ! empty( $encryption ) ? $encryption : tribe( Encryption::class ) );
		$this->template_modifications = $template_modifications;
		$this->actions                = $actions;
		$this->url                    = $url;

		// Attempt to load an account.
		$this->load_account();
	}

	/**
	 * {@inheritDoc}
	 */
	public function refresh_access_token( $id, $refresh_token ) {
		$refreshed = false;

		$this->post(
			$this->url::to_refresh(),
			[
				'body'    => [
					'grant_type'    => 'refresh_token',
					'refresh_token' => $refresh_token,
				],
			],
			200
		)->then(
			function ( array $response ) use ( &$id, &$refreshed ) {
				$body     = json_decode( wp_remote_retrieve_body( $response ), true );
				$body_set = $this->has_proper_response_body( $body, [ 'access_token', 'expires_in' ] );
				if ( ! $body_set ) {
					do_action( 'tribe_log', 'error', __CLASS__, [
						'action'   => __METHOD__,
						'message'  => 'Microsoft API access token refresh response is malformed.',
						'response' => $body,
					] );

					return false;
				}

				$refreshed = $this->save_access_and_expiration( $id, $response );

				return $refreshed;
			}
		);

		return $refreshed;
	}

	/**
	 * Get the Meeting by ID from Microsoft and Return the Data.
	 *
	 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
	 *
	 * @param string $web_link The web link to the meeting.
	 *
	 * @return array An array of data from the Microsoft API.
	 */
	public function fetch_event_data( $microsoft_event_id ) {
		if ( ! $this->get_token_authorization_header() ) {
			return [];
		}

		$data = [];

		$this->get(
			self::$api_base . "me/events/{$microsoft_event_id}",
			[
				'headers' => [
					'Authorization' => $this->get_token_authorization_header(),
					'Content-Type'  => 'application/json',
				],
				'body'    => null,
			],
			200
		)->then(
			function ( array $response ) use ( &$data ) {
				$body     = json_decode( wp_remote_retrieve_body( $response ), true );
				$body_set = $this->has_proper_response_body( $body );

				if (! $body_set	) {
					do_action( 'tribe_log', 'error', __CLASS__, [
						'action'   => __METHOD__,
						'message'  => 'Microsoft API meetings is response is malformed.',
						'response' => $body,
					] );

					return [];
				}

				$data = $body;
			}
		)->or_catch(
			function ( \WP_Error $error ) {
				do_action( 'tribe_log', 'error', __CLASS__, [
					'action'  => __METHOD__,
					'code'    => $error->get_error_code(),
					'message' => $error->get_error_message(),
				] );
			}
		);

		return $data;
	}

	/**
	 * {@inheritDoc}
	 */
	public function fetch_user( $user_id = 'me', $settings = false, $access_token = '' ) {
		if ( ! $this->get_token_authorization_header( $access_token ) ) {
			return [];
		}

		$this->get(
			self::$api_base . $user_id,
			[
				'headers' => [
					'Authorization' => $this->get_token_authorization_header( $access_token ),
					'Content-Type'  => 'application/json',
				],
				'body'    => null,
			],
			200
		)->then(
			static function ( array $response ) use ( &$data ) {
				$body     = json_decode( wp_remote_retrieve_body( $response ), true );
				$body_set = self::has_proper_response_body( $body );

				if ( ! $body_set ) {
					do_action( 'tribe_log', 'error', __CLASS__, [
						'action'   => __METHOD__,
						'message'  => 'Microsoft API user response is malformed.',
						'response' => $body,
					] );

					return [];
				}
				$data = $body;
			}
		)->or_catch(
			static function ( \WP_Error $error ) {
				do_action( 'tribe_log', 'error', __CLASS__, [
					'action'  => __METHOD__,
					'code'    => $error->get_error_code(),
					'message' => $error->get_error_message(),
				] );
			}
		);

		return $data;
	}

	/**
	 * Get the List of all Users
	 *
	 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
	 *
	 * @return array An array of users from the Microsoft API.
	 */
	public function fetch_users() {
		if ( ! $this->get_token_authorization_header() ) {
			return [];
		}

		$args = [];

		/**
		 * Filters the arguments for fetching users.
		 *
		 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
		 * @todo [plugin-consolidation] Merge VE into ECP, hook to be deprecated and renamed.
		 *
		 * @param array<string|string> $args The default arguments to fetch users.
		 */
		$args = (array) apply_filters( 'tec_events_virtual_microsoft_get_users_arguments', $args );

		// Get the initial page of users.
		$users = $this->fetch_users_with_args( $args );

		return $users;
	}

	/**
	 * Get the List of Users by arguments.
	 *
	 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
	 *
	 * @return array An array of data from the Microsoft API.
	 */
	public function fetch_users_with_args( $args ) {
		if ( ! $this->get_token_authorization_header() ) {
			return [];
		}

		$data = [];

		$this->get(
			self::$user_base,
			[
				'headers' => [
					'Authorization' => $this->get_token_authorization_header(),
					'Content-Type'  => 'application/json',
				],
				'body'    => ! empty( $args ) ? $args : null,
			],
			200
		)->then(
			static function ( array $response ) use ( &$data ) {
				$body     = json_decode( wp_remote_retrieve_body( $response ), true );
				$body_set = self::has_proper_response_body( $body );

				if ( ! $body_set ) {
					do_action( 'tribe_log', 'error', __CLASS__, [
						'action'   => __METHOD__,
						'message'  => 'Microsoft API users response is malformed.',
						'response' => $body,
					] );

					return [];
				}

				if ( isset( $body['userPrincipalName'] ) ) {
					$data[] = $body;
				} else {
					$data = $body;
				}
			}
		)->or_catch(
			static function ( \WP_Error $error ) {
				do_action( 'tribe_log', 'error', __CLASS__, [
					'action'  => __METHOD__,
					'code'    => $error->get_error_code(),
					'message' => $error->get_error_message(),
				] );
			}
		);

		return $data;
	}

	/**
	 * Get the no Microsoft account found message.
	 *
	 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
	 *
	 * @return string The message returned when no account found.
	 */
	public function get_no_account_message() {
		return sprintf(
			'%1$s <a href="%2$s" target="_blank">%3$s</a>',
			esc_html_x(
				'No Microsoft account found.',
			'The start of the message for smart url/autodetect when there is no Microsoft account found.',
			'tribe-events-calendar-pro'
			),
			Settings::admin_url(),
			esc_html_x(
				'Please check your account connection.',
			'The link in of the message for smart url/autodetect when no Microsoft account is found.',
			'tribe-events-calendar-pro'
			)
		);
	}

	/**
	 * Filters the API error message.
	 *
	 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
	 *
	 * @param string              $api_message The API error message.
	 * @param array<string,mixed> $body        The json_decoded request body.
	 * @param Api_Response        $response    The response that will be returned. A non `null` value
	 *                                         here will short-circuit the response.
	 *
	 * @return string              $api_message        The API error message.
	 */
	public function filter_api_error_message( $api_message, $body, $response ) {
		if ( ! isset( $body['error']['message'] ) ) {
			return $api_message;
		}

		$api_message .=  ' API Error: ' . $body['error']['message'];

		return $api_message;
	}
}
