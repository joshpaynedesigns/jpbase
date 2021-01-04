<?php
/**
 * Displays the main "Settings" tab.
 *
 * @link       https://bettersearchreplace.com
 * @since      1.1
 *
 * @package    Better_Search_Replace
 * @subpackage Better_Search_Replace/templates
 */

// Prevent direct/unauthorized access.
if ( ! defined( 'BSR_PATH' ) ) exit;

// Get information about the current license.
$license = get_option( 'bsr_license_key' );
$status  = get_option( 'bsr_license_status' );

// Other settings.
$page_size 		= get_option( 'bsr_page_size' ) ? absint( get_option( 'bsr_page_size' ) ) : 20000;
$max_results 	= get_option( 'bsr_max_results' ) ? absint( get_option( 'bsr_max_results' ) ) : 60;

if ( '' === get_option( 'bsr_enable_gzip' ) ) {
	$bsr_enable_gzip = false;
} else {
	$bsr_enable_gzip = true;
}

 ?>

<?php settings_fields( 'bsr_settings_fields' ); ?>

<table class="form-table">

	<tbody>

		<tr valign="top">
			<th scope="row" valign="top">
				<?php _e( 'Max Page Size', 'better-search-replace' ); ?>
			</th>
			<td>
				<div id="bsr-page-size-slider" class="bsr-slider"></div>
				<br><span id="bsr-page-size-info"><?php _e( 'Current Setting: ', 'better-search-replace' ); ?></span><span id="bsr-page-size-value"><?php echo absint( $page_size ); ?></span>
				<input id="bsr_page_size" type="hidden" name="bsr_page_size" value="<?php echo $page_size; ?>" />
				<p class="description"><?php _e( 'If you notice timeouts or are unable to backup/import the database, try decreasing this value.', 'better-search-replace' ); ?></p>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row" valign="top">
				<?php _e( 'Max Results', 'better-search-replace' ); ?>
			</th>
			<td>
				<div id="bsr-max-results-slider" class="bsr-slider"></div>
				<br><span id="bsr-max-results-info"><?php _e( 'Current Setting: ', 'better-search-replace' ); ?></span><span id="bsr-max-results-value"><?php echo absint( $max_results ); ?></span>
				<input id="bsr_max_results" type="hidden" name="bsr_max_results" value="<?php echo $max_results; ?>" />
				<p class="description"><?php _e( 'The maximum amount of results to store when running a search or replace.', 'better-search-replace' ); ?></p>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row" valign="top">
				<?php _e( 'Enable Gzip?', 'better-search-replace' ); ?>
			</th>
			<td>
				<label for="bsr-enable-gzip">
					<input id="bsr-enable-gzip" type="checkbox" name="bsr_enable_gzip" <?php checked( $bsr_enable_gzip, true ); ?> />
					<?php _e( 'If enabled, backups will be compressed to reduce file size.', 'better-search-replace' ); ?>
				</label>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row" valign="top">
				<?php _e( 'License Key', 'better-search-replace' ); ?>
			</th>
			<td>
				<input id="bsr_license_key" name="bsr_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license ); ?>" />
				<p class="description" for="bsr_license_key"><?php _e( 'Enter your license key for support and updates.', 'better-search-replace' ); ?></p>
			</td>
		</tr>

		<?php if( false !== $license ) { ?>
			<tr valign="top">
				<th scope="row" valign="top">
					<?php _e( 'License Status', 'better-search-replace' ); ?>
				</th>
				<td>
					<?php if( $status !== false && $status == 'valid' ) { ?>
						<div id="bsr-license-active" class="bsr-license-status"><?php _e( 'active', 'better-search-replace' ); ?></div>
						<?php wp_nonce_field( 'bsr_license_nonce', 'bsr_license_nonce' ); ?>
						<input type="submit" class="button-secondary" name="bsr_license_deactivate" value="<?php _e( 'Deactivate License', 'better-search-replace' ); ?>"/>
					<?php } else { ?>
						<div id="bsr-license-inactive" class="bsr-license-status"><?php _e( 'inactive', 'better-search-replace' ); ?></div>
						<?php wp_nonce_field( 'bsr_license_nonce', 'bsr_license_nonce' ); ?>
						<input type="submit" class="button-secondary" name="bsr_license_activate" value="<?php _e( 'Activate License', 'better-search-replace' ); ?>"/>
					<?php } ?>
				</td>
			</tr>
		<?php } ?>

	</tbody>

</table>
<?php submit_button(); ?>
