<?php
/**
 * View: Week View - Grid Body
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/week/grid-body.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 5.0.0
 *
 * @var array $multiday_events     An array of each day multi-day events and more event count, if any, in the shape
 *                                 `[ <Y-m-d> => [ 'events' => [ ...$multiday_events], 'more_events' => <int> ] ]`.
 * @var bool  $has_multiday_events Boolean whether the week has multiday events or not.
 * @var array $events              An array of each day non multi-day events, if any, in the shape `[ <Y-m-d> => [ ...$events ] ]`.
 */
?>
<div class="tribe-events-pro-week-grid__body" role="rowgroup">
	<div class="tribe-events-pro-week-grid__events-scroll-wrapper">
		<?php foreach ( $events as $day => $day_events ) : ?>
			<?php $this->template( 'widget-week/grid-body/events-day', [ 'events' => $day_events ] ); ?>
		<?php endforeach; ?>
	</div>
</div>
