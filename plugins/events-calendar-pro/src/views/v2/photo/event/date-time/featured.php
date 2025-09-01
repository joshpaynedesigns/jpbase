<?php
/**
 * View: Photo View - Single Event Featured Icon
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/photo/event/date-time/featured.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @since 5.1.1
 * @since 7.6.1 Added $icon_description parameter and updated the template to use it for the accessible label.
 *
 * @version 7.6.1
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 * @var string $icon_description The description of the icon. Used for the accessible label. (optional)
 *
 * @see tribe_get_event() For the format of the event object.
 */

if ( empty( $event->featured ) ) {
	return;
}

if ( empty( $icon_description ) ) {
	$icon_description = __( 'Featured', 'tribe-events-calendar-pro' );
}
?>
<em class="tribe-events-pro-photo__event-datetime-featured-icon">
	<?php $this->template( 'components/icons/featured', [ 'classes' => [ 'tribe-events-pro-photo__event-datetime-featured-icon-svg' ] ] ); ?>
</em>
<span class="tribe-events-pro-photo__event-datetime-featured-text">
	<?php echo esc_html( $icon_description ); ?>
</span>
