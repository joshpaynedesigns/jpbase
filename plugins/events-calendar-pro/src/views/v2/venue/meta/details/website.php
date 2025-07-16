<?php
/**
 * View: Organizer meta details - Website
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/venue/meta/details/website.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @since 5.2.0
 * @since 7.6.1 Added $icon_description parameter and updated the template to use it for the accessible label.
 *
 * @version 7.6.1
 *
 * @var WP_Post $venue            The venue post object.
 * @var string  $icon_description The description of the icon. Used for the accessible label. (optional)
 *
 */

$url = tribe_get_venue_website_url( $venue->ID );

if ( empty( $url ) ) {
	return;
}

if ( empty( $icon_description ) ) {
	$icon_description = __( 'Website', 'tribe-events-calendar-pro' );
}

?>
<div class="tribe-events-pro-venue__meta-website tribe-common-b1 tribe-common-b2--min-medium">
	<?php $this->template( 'components/icons/website', [ 'classes' => [ 'tribe-events-pro-venue__meta-website-icon-svg' ] ] ); ?>
	<span class="tribe-common-a11y-visual-hide">
		<?php echo esc_html( $icon_description ); ?>
	</span>
	<a
		href="<?php echo esc_url( $url ); ?>"
		class="tribe-events-pro-venue__meta-website-link tribe-common-anchor"
	><?php echo esc_html( $url ); ?></a>
</div>
