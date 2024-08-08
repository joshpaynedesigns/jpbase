<?php
/**
 * Manages the Webex settings.
 *
 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
 *
 * @package Tribe\Events\Virtual\Meetings\Webex
 */

namespace Tribe\Events\Virtual\Meetings\Webex;

use Tribe\Events\Virtual\Integrations\Abstract_Settings;
use Tribe\Events\Virtual\Meetings\Webex\Event_Meta as Webex_Meta;

/**
 * Class Settings
 *
 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
 *
 * @package Tribe\Events\Virtual\Meetings\Webex
 */
class Settings extends Abstract_Settings {

	/**
	 * {@inheritDoc}
	 */
	public static $option_prefix = 'tec_webex_';

	/**
	 * Settings constructor.
	 *
	 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
	 *
	 * @param Api                    $api                    An instance of the Webex API handler.
	 * @param Template_Modifications $template_modifications An instance of the Template_Modifications handler.
	 * @param Url                    $url                    An instance of the URL handler.
	 */
	public function __construct( Api $api, Template_Modifications $template_modifications, Url $url ) {
		$this->api                    = $api;
		$this->template_modifications = $template_modifications;
		$this->url                    = $url;
		self::$api_id                 = Webex_Meta::$key_source_id;
	}
}
