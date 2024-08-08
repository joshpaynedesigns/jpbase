<?php
/**
 * Handles the templates modifications required by the Microsoft API integration.
 *
 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
 *
 * @package Tribe\Events\Virtual\Meetings\Microsoft
 */

namespace Tribe\Events\Virtual\Meetings\Microsoft;

use Tribe\Events\Virtual\Integrations\Abstract_Template_Modifications;
use Tribe\Events\Virtual\Meetings\Microsoft\Event_Meta as Microsoft_Event_Meta;

/**
 * Class Template_Modifications
 *
 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
 *
 * @package Tribe\Events\Virtual\Meetings\Microsoft
 */
class Template_Modifications extends Abstract_Template_Modifications {

	/**
	 * {@inheritDoc}
	 */
	public function setup() {
		self::$api_id = Microsoft_Event_Meta::$key_source_id;
		self::$option_prefix = Settings::$option_prefix;
	}
}
