<?php
/**
 * View: Map Event Categories
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/map/event-cards/event-card/event/category.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 7.6.1
 *
 * @var object|null $category_colors_priority_category The highest-priority category for the event, determined using the
 *                                                     `Category_Color_Priority_Category_Provider` class.
 * @var array|null  $category_colors_meta              Array containing the category metadata (primary, secondary, text,
 *                                                     priority, hide_from_legend).
 *
 * @see tribe_get_event() For the format of the event object.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( empty( $category_colors_priority_category ) ) {
	return;
}

?>

<div class="tec-events-calendar-map__event-categories">
	<div class="tec-events-calendar-map__category tribe_events_cat-<?php echo sanitize_html_class( $category_colors_priority_category->slug ); ?>">
		<?php if ( ! empty( $category_colors_meta['primary'] ) ) : ?>
			<span class="tec-events-calendar-map__category-icon"></span>
		<?php endif; ?>
		<?php echo esc_html( $category_colors_priority_category->name ); ?>
	</div>
</div>
