<?php
namespace Tribe\Events\Virtual\Editor\Template;

use Tribe\Events\Virtual\Plugin;
/**
 * Allow including admin templates.
 *
 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
 */
class Admin extends \Tribe__Template {
	/**
	 * Building of the Class template configuration.
	 *
	 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
	 */
	public function __construct() {
		$this->set_template_origin( tribe( Plugin::class ) );

		$this->set_template_folder( 'src/admin-views' );

		// Configures this templating class extract variables.
		$this->set_template_context_extract( true );
	}
}
