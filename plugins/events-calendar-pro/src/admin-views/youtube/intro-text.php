<?php
/**
 * View: YouTube Settings API auth intro text.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/youtube/intro-text.php
 *
 * See more documentation about our views templating system.
 *
 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
 *
 * @version 1.6.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var string $message A message to display above the account list on loading.
 */

?>
<div class="tec-settings-form__header-block">
	<h3 id="tec-events-pro-youtube-title" class="tec-settings-form__section-header tec-settings-form__section-header--sub">
		<?php echo esc_html_x( 'YouTube Live', 'API connection header', 'tribe-events-calendar-pro' ); ?>
	</h3>
	<?php
		$this->template( 'components/message', [
		'message' => $message,
		'type'    => 'standard',
	] );
	?>
</div>
