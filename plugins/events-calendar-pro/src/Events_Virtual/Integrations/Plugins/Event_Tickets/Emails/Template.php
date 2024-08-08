<?php

namespace TEC\Events_Virtual\Integrations\Plugins\Event_Tickets\Emails;

use Tribe\Events\Virtual\Plugin;
use Tribe__Template;

/**
 * Class Template
 *
 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
 *
 * @package TEC\Events_Virtual\Integrations\Plugins\Event_Tickets\Emails
 */
class Template extends Tribe__Template {
	/**
	 * Building of the Class template configuration.
	 *
	 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
	 */
	public function __construct() {
		$this->set_template_origin( tribe( Plugin::class ) );
		$this->set_template_folder( 'src/views/integrations/event-tickets/emails' );

		// Setup to look for theme files.
		$this->set_template_folder_lookup( true );

		// Configures this templating class extract variables.
		$this->set_template_context_extract( true );
	}
}
