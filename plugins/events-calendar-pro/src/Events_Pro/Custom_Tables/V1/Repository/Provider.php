<?php
/**
 * Handles the Custom Tables integration, and compatibility, with
 * the Repositories.
 *
 * Here what implementations and filters are not relevant, are disconnected.
 *
 * @since   6.0.0
 *
 * @package TEC\Events_Pro\Custom_Tables\V1\Repository
 */

namespace TEC\Events_Pro\Custom_Tables\V1\Repository;

use tad_DI52_ServiceProvider as Service_Provider;
use TEC\Events\Custom_Tables\V1\Provider_Contract;
use TEC\Events\Custom_Tables\V1\Repository\Provider as TEC_Provider;

/**
 * Class Provider.
 *
 * @since   6.0.0
 *
 * @package TEC\Events_Pro\Custom_Tables\V1\Repository
 */
class Provider extends Service_Provider implements Provider_Contract {
	/**
	 * Connects the methods to the Filters API.
	 *
	 * @since 6.0.0
	 */
	public function register() {
		$this->container->singleton( self::class, $this );
		$this->container->singleton( Events::class, Events::class );
		// Avoid TEC from filtering.
		$this->container->make( TEC_Provider::class )->unregister();
		/*
		 * When the Pro repository needs the callback to create or update an Event recurrences,
		 * let's return one that will avoid the default logic.
		 */
		if ( ! has_filter( 'tribe_repository_event_recurrence_create_callback', [
			$this,
			'create_recurrence_callback'
		] ) ) {
			add_filter( 'tribe_repository_event_recurrence_create_callback', [
				$this,
				'create_recurrence_callback'
			], 20 );
		}
		if ( ! has_filter( 'tribe_repository_event_recurrence_update_callback', [
			$this,
			'create_recurrence_callback'
		] ) ) {
			add_filter( 'tribe_repository_event_recurrence_update_callback', [
				$this,
				'create_recurrence_callback'
			], 20 );
		}
		if ( ! has_filter( 'tribe_repository_events_create_callback', [ $this, 'update_callback' ] ) ) {
			add_filter( 'tribe_repository_events_create_callback', [ $this, 'update_callback' ], 20, 2 );
		}
		if ( ! has_filter( 'tribe_repository_events_update_callback', [ $this, 'update_callback' ] ) ) {
			add_filter( 'tribe_repository_events_update_callback', [ $this, 'update_callback' ], 20, 2 );
		}
	}

	/**
	 * Disconnects the methods handled by the Provider from the Filters API.
	 *
	 * @since 6.0.0
	 */
	public function unregister() {
		remove_filter(
			'tribe_repository_event_recurrence_create_callback',
			[ $this, 'create_recurrence_callback' ],
			20
		);
		remove_filter(
			'tribe_repository_event_recurrence_update_callback',
			[ $this, 'create_recurrence_callback' ],
			20
		);
		remove_filter(
			'tribe_repository_events_create_callback',
			[ $this, 'update_callback' ],
			20
		);
		remove_filter(
			'tribe_repository_events_update_callback',
			[ $this, 'update_callback' ],
			20
		);
	}

	/**
	 * Filters the callback that should be used to create or update the Occurrences
	 * of an Event.
	 *
	 * @since 6.0.0
	 *
	 * @return callable The filtered Occurrence creation callback.
	 */
	public function create_recurrence_callback() {
		return $this->container->make( Events::class )->create_recurrence_callback();
	}

	/**
	 * Replaces the default Event Repository create and update callback with one that will operate on
	 * custom tables.
	 *
	 * @since 6.0.0
	 *
	 * @param callable            $repository_callback The default repository callback.
	 * @param array<string,mixed> $postarr             An array of datat to create or update the Event.
	 *
	 * @return callable The callback that will handle upsertions of an Event custom tables data
	 *                  in the context of a repository call.
	 */
	public function update_callback( $repository_callback, array $postarr = [] ) {
		return $this->container->make( Events::class )->update_callback( $repository_callback, $postarr );
	}
}