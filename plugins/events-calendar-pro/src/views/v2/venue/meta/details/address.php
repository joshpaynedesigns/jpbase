<?php
/**
 * View: Venue meta details - Address
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/venue/meta/details/address.php
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
 * @var WP_Post $venue The venue post object.
 * @var string $icon_description The description of the icon. Used for the accessible label. (optional)
 *
 */

if ( ! tribe_address_exists( $venue->ID ) ) {
	return;
}

if ( empty( $icon_description ) ) {
	$icon_description = __( 'Address', 'tribe-events-calendar-pro' );
}

$address = tribe_get_full_address( $venue->ID );

?>
<div class="tribe-events-pro-venue__meta-address tribe-common-b1 tribe-common-b2--min-medium">
	<?php $this->template( 'components/icons/map-pin', [ 'classes' => [ 'tribe-events-pro-venue__meta-address-icon-svg' ] ] ); ?>
	<span class="tribe-common-a11y-visual-hide">
		<?php echo esc_html( $icon_description ); ?>
	</span>
	<div class="tribe-events-pro-venue__meta-address-details">
		<?php echo $address; ?>
		<a
			href="<?php echo esc_url( $venue->directions_link ) ;?>"
			class="tribe-events-pro-venue__meta-address-directions-link tribe-common-anchor"
		><?php esc_html_e( 'Get Directions', 'tribe-events-calendar-pro' ); ?></a>
	</div>
</div>
