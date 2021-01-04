<?php
/**
 * Displays the "Backup" tab.
 *
 * @link       https://bettersearchreplace.com
 * @since      1.1
 *
 * @package    Better_Search_Replace
 * @subpackage Better_Search_Replace/templates
 */

// Prevent direct/unauthorized access.
if ( ! defined( 'BSR_PATH' ) ) exit;

$profiles = BSR_Admin::get_profiles();

?>


<div class="metabox-holder">

	<div class="postbox">
		 <h3><?php _e( 'Backup Database', 'better-search-replace' ); ?></h3>
		 <div class="inside">

			<p><?php _e( 'Click the button below to take a backup of your database, which can then be imported into another instance of Better Search Replace.', 'better-search-replace' ); ?></p>

			<div id="bsr-backup-form">

				<table>

					<tr>
						<td><label for="bsr_profile"><strong><?php _e( 'Run Search/Replace profile on backup: ', 'better-search-replace' ); ?></strong></label></td>
						<td>
						<?php
							if ( 0 !== count( $profiles ) ) {

								echo '<select id="bsr_backup_profile" name="bsr_backup_profile">
										<option>' . __( 'Please select...', 'better-search-replace' ) . '</option>';

								foreach ( $profiles as $k => $v ) {
									echo '<option value="' . $k . '">' . $k . '</option>';
								}

								echo '</select>';

							} else {
								printf( '<span class="bsr-no-profiles">%s <a href="%s">%s</a></span>', __( 'No profiles found.', 'better-search-replace' ), get_admin_url() . 'tools.php?page=better-search-replace', __( 'Click here to create one.','better-search-replace' ) );
							}
						?>
				        </td>
					</tr>

				</table>

				<br>
				<?php wp_nonce_field( 'bsr_process_backup', 'bsr_nonce' ); ?>
				<input type="hidden" name="action" value="bsr_process_backup" />
				<button id="bsr-backup-submit" type="submit" class="button"><?php _e( 'Backup Database', 'better-search-replace' ); ?></button>
			</div>

		</div>
	</div>

	<div class="postbox">
		<h3><?php _e( 'Import Database', 'better-search-replace' ); ?></h3>

		<div class="inside">

			<div id="bsr-import-form">

				<p><?php _e( 'Use the form below to import a database backup and run a saved profile on the resulting database.', 'better-search-replace' ); ?></p>
				<p><?php _e( 'Alternatively, you can upload the backup file to the wp-content/uploads/ directory manually and click "Import Database".', 'better-search-replace' ); ?></p>

				<input id="bsr-file-import" type="file" name="bsr_import_file">

				<table>

					<tr>
						<td><label for="bsr_profile"><strong><?php _e( 'Run Search/Replace profile after import: ', 'better-search-replace' ); ?></strong></label></td>
						<td>
						<?php
							if ( 0 !== count( $profiles ) ) {

								echo '<select id="bsr_import_profile" name="bsr_profile">
										<option>' . __( 'Please select...', 'better-search-replace' ) . '</option>';

								foreach ( $profiles as $k => $v ) {
									echo '<option value="' . $k . '">' . $k . '</option>';
								}

								echo '</select>';

							} else {
								printf( '<span class="bsr-no-profiles">%s <a href="%s">%s</a></span>', __( 'No profiles found.', 'better-search-replace' ), get_admin_url() . 'tools.php?page=better-search-replace', __( 'Click here to create one.','better-search-replace' ) );
							}
						?>
				        </td>
					</tr>

				</table>

				<br>
				<?php wp_nonce_field( 'bsr_process_import', 'bsr_nonce' ); ?>
				<input type="hidden" name="action" value="bsr_process_import" />
				<button id="bsr-import-submit" type="submit" class="button"><?php _e( 'Import Database', 'better-search-replace' ); ?></button>

			</div>
		</div>

	</div>


</div>
