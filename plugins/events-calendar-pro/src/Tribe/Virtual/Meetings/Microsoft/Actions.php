<?php
/**
 * Manages the Microsoft API Actions.
 *
 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
 *
 * @package Tribe\Events\Virtual\Meetings\Microsoft
 */

namespace Tribe\Events\Virtual\Meetings\Microsoft;

use Tribe\Events\Virtual\Integrations\Abstract_Actions;
use Tribe\Events\Virtual\Meetings\Microsoft\Event_Meta as Microsoft_Event_Meta;

/**
 * Class Actions
 *
 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
 *
 * @package Tribe\Events\Virtual\Meetings\Microsoft
 */
class Actions extends Abstract_Actions {

	/**
	 * Actions constructor.
	 *
	 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
	 */
	public function __construct() {
		static::$api_id = Microsoft_Event_Meta::$key_source_id;

		$this->setup( static::$api_id );
	}
}
