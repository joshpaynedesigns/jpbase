<?php
/**
 * Export functions for Zoom.
 *
 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
 * @package Tribe\Events\Virtual\Service_Providers;
 */

namespace Tribe\Events\Virtual\Meetings\Zoom;

use Tribe\Events\Virtual\Export\Abstract_Export;
use Tribe\Events\Virtual\Meetings\Zoom\Event_Meta as Zoom_Event_Meta;

/**
 * Class Event_Export
 *
 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
 *
 * @package Tribe\Events\Virtual\Export;
 */
class Event_Export extends Abstract_Export {

	/**
	 * Event_Export constructor.
	 *
	 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
	 */
	public function __construct() {
		self::$api_id = Zoom_Event_Meta::$key_source_id;
	}
}
