<?php
/**
 * View: Organizer meta details - Email
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/organizer/meta/details/email.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @since 6.2.0 Avoid rendering if the email should not be visible.
 * @since 7.6.1 Added $icon_description parameter and updated the template to use it for the accessible label.
 *
 * @version 7.6.1
 *
 * @var WP_Post $organizer        The organizer post object.
 * @var string  $icon_description The description of the icon. Used for the accessible label. (optional)
 *
 */

if ( ! tec_events_pro_organizer_email_is_visible( 'organizer-single' ) ) {
	return;
}


$email = tribe_get_organizer_email( $organizer->ID );

if ( empty( $email ) ) {
	return;
}

if ( empty( $icon_description ) ) {
	$icon_description = __( 'Email', 'tribe-events-calendar-pro' );
}

?>
<div class="tribe-events-pro-organizer__meta-email tribe-common-b1 tribe-common-b2--min-medium">
	<?php $this->template( 'components/icons/mail', [ 'classes' => [ 'tribe-events-pro-organizer__meta-email-icon-svg' ] ] ); ?>
	<span class="tribe-events-pro-organizer__meta-email-label tribe-common-a11y-visual-hide">
		<?php echo esc_html( $icon_description ); ?>
	</span>
	<a
		href="mailto:<?php echo esc_attr( $email ); ?>"
		class="tribe-events-pro-organizer__meta-email-link tribe-common-anchor"
	><?php echo esc_html( $email ); ?></a>

</div>
