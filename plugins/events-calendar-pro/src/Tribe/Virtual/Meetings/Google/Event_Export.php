<?php
/**
 * Export functions for Google.
 *
 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
 * @package Tribe\Events\Virtual\Meetings\Google;
 */

namespace Tribe\Events\Virtual\Meetings\Google;

use Tribe\Events\Virtual\Export\Abstract_Export;
use Tribe\Events\Virtual\Meetings\Google\Event_Meta as Google_Event_Meta;

/**
 * Class Event_Export
 *
 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
 *
 * @package Tribe\Events\Virtual\Meetings\Google;
 */
class Event_Export extends Abstract_Export {

	/**
	 * Event_Export constructor.
	 *
	 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
	 */
	public function __construct() {
		self::$api_id = Google_Event_Meta::$key_source_id;
	}
}
