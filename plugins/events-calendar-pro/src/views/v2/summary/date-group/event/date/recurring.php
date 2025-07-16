<?php
/**
 * View: Summary View - Single Event Recurring Icon
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/summary/date-group/event/date/recurring.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * Note this view uses classes from the list view event datetime to leverage those styles.
 *
 * @since 5.0.0
 * @since 7.6.1 Added $icon_description parameter and updated the template to use it for the accessible label.
 *
 * @version 7.6.1
 *
 * @var WP_Post $event            The event post object with properties added by the `tribe_get_event` function.
 * @var string  $icon_description The description of the icon. Used for the accessible label. (optional)
 *
 * @see tribe_get_event() For the format of the event object.
 */

if ( empty( $event->recurring ) ) {
	return;
}

if ( empty( $icon_description ) ) {
	$icon_description = __( 'Recurring', 'tribe-events-calendar-pro' );
}
?>
<a
	href="<?php echo esc_url( $event->permalink_all ); ?>"
	class="tribe-events-pro-summary__event-datetime-recurring-link"
>
	<em class="tribe-events-pro-summary__event-datetime-recurring-icon">
		<?php $this->template( 'components/icons/recurring', [ 'classes' => [ 'tribe-events-pro-summary__event-datetime-recurring-icon-svg' ] ] ); ?>
	</em>
	<span class="tribe-events-pro-summary__event-datetime-recurring-text tribe-common-a11y-visual-hide">
		<?php echo esc_html( $icon_description ); ?>
	</span>
</a>
