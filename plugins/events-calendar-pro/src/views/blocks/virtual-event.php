<?php
/**
 * Block: Virtual Events
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-virtual/blocks/virtual-event.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 1.7.1
 *
 */

$default_classes = [ 'tribe-block', 'tribe-block--virtual-event' ];

// Add the custom classes from the block attributes.
$classes = isset( $attributes['className'] ) ? array_merge( $default_classes, [ $attributes['className'] ] ) : $default_classes;
?>
<div <?php tec_classes( $classes ); ?>>
    <?php
		/**
		 * Action to allow injecting the block content from various providers.
		 *
		 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
		 * @todo [plugin-consolidation] Merge VE into ECP, hook to be deprecated and renamed.
		 */
		do_action( 'tribe_events_virtual_block_content' );
	?>
</div>
