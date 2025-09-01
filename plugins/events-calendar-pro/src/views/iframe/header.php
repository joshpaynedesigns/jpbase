<?php
/**
 * View: iFrame Header
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/iframe/header.php
 *
 * See more documentation about our views templating system.
 *
 * @since   7.2.0
 *
 * @version 7.2.0
 *
 * @link    http://evnt.is/1aiy
 */

// Disable the admin bar on the front end.
// phpcs:disable WordPressVIPMinimum.UserExperience.AdminBarRemoval.RemovalDetected
show_admin_bar( false );
// phpcs:enable WordPressVIPMinimum.UserExperience.AdminBarRemoval.RemovalDetected

header( 'Content-Type: ' . get_option( 'html_type' ) . '; charset=' . get_option( 'blog_charset' ) );
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo esc_html_x( 'Calendar Embed Iframe', 'The title for the calendar embed iframe.', 'events-calendar-pro' ); ?></title>
		<?php

		do_action( 'tec_events_pro_calendar_embed_iframe_head' );

		wp_head();
		?>
	</head>
	<body class="tec-events-pro-calendar-embed-iframe__body">
<?php
