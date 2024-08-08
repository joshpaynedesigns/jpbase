<?php
/**
 * Manages the Webex API Actions.
 *
 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
 *
 * @package Tribe\Events\Virtual\Meetings\Webex
 */

namespace Tribe\Events\Virtual\Meetings\Webex;

use Tribe\Events\Virtual\Integrations\Abstract_Actions;
use Tribe\Events\Virtual\Meetings\Webex\Event_Meta as Webex_Event_Meta;

/**
 * Class Actions
 *
 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
 *
 * @package Tribe\Events\Virtual\Meetings\Webex
 */
class Actions extends Abstract_Actions {

	/**
	 * Actions constructor.
	 *
	 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
	 */
	public function __construct() {
		static::$api_id = Webex_Event_Meta::$key_source_id;

		$this->setup( static::$api_id );
	}
}
