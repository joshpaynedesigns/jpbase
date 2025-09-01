<?php
/**
 * View: Virtual Events Metabox Google API Account Authorization.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/google/api/authorize-fields.php
 *
 * See more documentation about our views templating system.
 *
 * @since 7.0.0 Migrated to Events Pro from Events Virtual.
 *
 * @version 1.9.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var Api    $api     An instance of the Google API handler.
 * @var Url    $url     An instance of the URL handler.
 * @var string $message A message to display above the account list on loading.
 */

$accounts = $api->get_list_of_accounts( true );
?>
<fieldset id="tec-field-google_token" class="tec-meetings-api-fields tribe-field tribe-field-text tribe-size-medium">
	<legend class="tribe-field-label"><?php esc_html_e( 'Connected Accounts', 'tribe-events-calendar-pro' ); ?></legend>
	<p>
		<?php
		printf(
			/* translators: 1: Virtual event label plural lowercase, 2: Opening anchor tag URL to the knowledgebase, 3: Closing anchor tag */
			esc_html_x(
				'You need to connect your site to a Google account to be able to generate Google Meet links for your %1$s. %2$sRead more about adding and managing Google Accounts.%3$s',
				'Settings help text for multiple Google accounts.',
				'tribe-events-calendar-pro'
			),
			esc_html( tribe_get_virtual_event_label_plural_lowercase() ),
			sprintf( '<a href="%s" target="_blank">', 'https://evnt.is/1b8r' ),
			'</a>'
		);
		?>
	</p>
	<div class="tec-api-accounts-messages tec-google-accounts-messages">
		<?php
		$this->template( 'components/message', [
			'message' => $message,
			'type'    => 'standard',
		] );
		?>
	</div>
	<div class="tec-google-accounts-wrap <?php echo is_array( $accounts ) && count( $accounts ) > 4 ? 'long-list' : ''; ?>">
		<?php
		$this->template( 'google/api/accounts/list', [
				'api'      => $api,
				'url'      => $url,
				'accounts' => $accounts,
			] );
		?>
	</div>
	<div class="tec-google-add-wrap">
		<?php
		$this->template( 'google/api/authorize-fields/add-link', [
				'api' => $api,
				'url' => $url,
			] );
		?>
	</div>
</fieldset>
