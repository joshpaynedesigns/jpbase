<?php
/**
 * Manages the Zoom settings for the extension.
 *
 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
 *
 * @package Tribe\Events\Virtual\Meetings\Zoom
 */

namespace Tribe\Events\Virtual\Meetings\Zoom;

use Tribe\Events\Virtual\Integrations\Abstract_Settings;
use Tribe\Events\Virtual\Meetings\Zoom\Event_Meta as Zoom_Meta;

/**
 * Class Settings
 *
 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
 *
 * @package Tribe\Events\Virtual\Meetings\Zoom
 */
class Settings extends Abstract_Settings {

	/**
	 * {@inheritDoc}
	 */
	public static $option_prefix = 'tribe_zoom_';

	/**
	 * Settings constructor.
	 *
	 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
	 *
	 * @param Api                    $api                    An instance of the Zoom API handler.
	 * @param Url                    $url                    An instance of the URL handler.
	 * @param Template_Modifications $template_modifications An instance of the Template_Modifications handler.
	 */
	public function __construct( Api $api, Url $url, Template_Modifications $template_modifications ) {
		$this->url                    = $url;
		$this->api                    = $api;
		$this->template_modifications = $template_modifications;
		self::$api_id                 = Zoom_Meta::$key_source_id;
	}
}
