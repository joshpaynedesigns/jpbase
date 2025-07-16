<?php
/**
 * View: Series Relationship marker link.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/custom-tables-v1/components/series-relationship-marker-link.php
 *
 * See more documentation about our views templating system.
 *
 * @link    https://evnt.is/1aiy
 *
 * @since   6.0.0
 *
 * @var WP_Post         $event         The event post object with properties added by the `tribe_get_event` function.
 * @var Tribe__Template $this          A reference to the current base template handler object.
 *
 * @see     tribe_get_event() For the format of the event object.
 *
 * @version 6.0.0
 */

use TEC\Events\Custom_Tables\V1\Models\Occurrence;
use TEC\Events_Pro\Custom_Tables\V1\Templates\Templates;

$id = $id = $event->ID;

// This is an occurrence the real post ID is hold as a reference on the occurrence table.
if ( isset( $event->_tec_occurrence ) && $event->_tec_occurrence instanceof Occurrence ) {
	$id = $event->_tec_occurrence->post_id;
}

$series = tec_event_series( $id );

if ( ! $series instanceof WP_Post ) {
	return;
}

$title_classes = tec_get_series_marker_label_classes( $series, $event );

// translators: %s: event series title.
$aria_label = sprintf( __( 'Event series: %s', 'tribe-events-calendar-pro' ), get_the_title( $series->ID ) );
?>

<span class="tribe-events-calendar-series-archive__container">
	<a
		href="<?php the_permalink( $series->ID ); ?>"
		aria-label="<?php echo esc_attr( $aria_label ); ?>"
		class="tribe-events-calendar-series-archive__link"
	>
		<?php tribe( Templates::class )->template( 'components/icons/series', [ 'classes' => [ 'tribe-events-series-archive__icon' ] ] ); ?>
		<span <?php tec_classes( $title_classes ); ?> ><?php echo esc_html( get_the_title( $series->ID ) ); ?></span>
	</a>
</span>
