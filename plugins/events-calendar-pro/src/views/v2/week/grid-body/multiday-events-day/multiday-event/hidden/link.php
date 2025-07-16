<?php
/**
 * View: Week View - Single Multiday Event Hidden Link
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/week/grid-body/multiday-events-day/multiday-event/hidden/link.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @since 5.1.1
 * @since 7.5.0 Remove hidden links from tab navigation.
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 *
 * @version 5.1.1
 */

 ?>
<a
	href="<?php echo esc_url( $event->permalink ); ?>"
	class="tribe-events-pro-week-grid__multiday-event-hidden-link"
	tabindex="-1"
>
	<?php $this->template( 'week/grid-body/multiday-events-day/multiday-event/hidden/link/featured', [ 'event' => $event ] ); ?>
	<?php $this->template( 'week/grid-body/multiday-events-day/multiday-event/hidden/link/title', [ 'event' => $event ] ); ?>
	<?php $this->template( 'week/grid-body/multiday-events-day/multiday-event/hidden/link/recurring', [ 'event' => $event ] ); ?>
</a>
