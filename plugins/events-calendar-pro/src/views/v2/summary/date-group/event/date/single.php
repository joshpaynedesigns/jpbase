<?php
/**
 * View: Summary View - Single day date partial.
 * Used for events that don't span multiple days and aren't all "all-day" events.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/summary/date-group/event/date/single.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 5.7.0
 * @version 7.0.3 Now allows for hiding the event end time with $show_end_time.
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 * @var bool $show_end_time Flag to disable end time from displaying.
 *
 * @see tribe_get_event() For the format of the event object.
 */
$show_end_time ??= true;
?>
<span class="tribe-event-date-start">
	<?php echo esc_html( $event->summary_view->start_time ); ?>
</span>
<?php if ( $show_end_time ) { ?>
- <span class="tribe-event-date-end">
	<?php echo esc_html( $event->summary_view->end_time ); ?>
</span>
<?php } ?>
