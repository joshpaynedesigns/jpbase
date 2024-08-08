<?php

namespace Tribe\Events\Virtual\Editor\Blocks;

use Tribe\Events\Virtual\Editor\Template\Frontend as Frontend;

class Virtual_Event extends \Tribe__Editor__Blocks__Abstract {

	/**
	 * Which is the name/slug of this block.
	 *
	 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
	 *
	 * @return string
	 */
	public function slug() {
		return 'virtual-event';
	}

	/**
	 * Set the default attributes of this block.
	 *
	 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
	 *
	 * @return array
	 */
	public function default_attributes() {
		$defaults = [
			'title' => esc_html__( 'Virtual Events', 'tribe-events-calendar-pro' ),
		];

		return $defaults;
	}

	public static function register_block() {
		parent::register();
	}

	/**
	 * Since we are dealing with a Dynamic type of Block we need a PHP method to render it.
	 *
	 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
	 *
	 * @param  array $attributes
	 *
	 * @return string
	 */
	public function render( $attributes = [] ) {
		if (
			! tribe( 'editor' )->should_load_blocks()
			|| ! tribe( 'events.editor.compatibility' )->is_blocks_editor_toggled_on()
		) {
			return '';
		}

		$args = [];
		$args['attributes'] = $this->attributes( $attributes );
		$args['post_id'] = $post_id = tribe( 'events.editor.template' )->get( 'post_id', null, false );

		/* @var Frontend $frontend  */
		$frontend = tribe( Frontend::class );

		// Add the rendering attributes into global context.
		$frontend->add_template_globals( $args );

		return $frontend->template( [ 'blocks', $this->slug() ], $args, false );
	}
}
