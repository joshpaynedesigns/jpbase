<?php
/**
 * View: Organizer meta details - Phone
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/organizer/meta/details/phone.php
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
 * @var WP_Post $organizer        The organizer post object.
 * @var string  $icon_description The description of the icon. Used for the accessible label. (optional)
 *
 */
if ( ! tec_events_pro_organizer_phone_is_visible( 'organizer-single' ) ) {
	return;
}

$phone = tribe_get_organizer_phone( $organizer->ID );

if ( empty( $phone ) ) {
	return;
}

if ( empty( $icon_description ) ) {
	$icon_description = __( 'Phone', 'tribe-events-calendar-pro' );
}
?>
<div class="tribe-events-pro-organizer__meta-phone tribe-common-b1 tribe-common-b2--min-medium">
	<?php $this->template( 'components/icons/phone', [ 'classes' => [ 'tribe-events-pro-organizer__meta-phone-icon-svg' ] ] ); ?>
	<span class="tribe-common-a11y-visual-hide">
		<?php echo esc_html( $icon_description ); ?>
	</span>
	<span class="tribe-events-pro-organizer__meta-phone-text"><?php echo esc_html( $phone ); ?></span>
</div>
