<?php

/**
 * Displays the main Better Search Replace page under Tools -> Better Search Replace.
 *
 * @link       https://bettersearchreplace.com
 * @since      1.0.0
 *
 * @package    Better_Search_Replace
 * @subpackage Better_Search_Replace/templates
 */

// Prevent direct access.
if ( ! defined( 'BSR_PATH' ) ) exit;

// Determines which tab to display.
$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'bsr_search_replace';

// Bail if not on a BSR page
if ( ! in_array( $active_tab, array( 'bsr_search_replace', 'bsr_backup_import', 'bsr_settings', 'bsr_help' ) ) ) {
	wp_die( 'The requested tab was not found.', 'better-search-replace' );
}

if ( 'bsr_settings' === $active_tab ) {
	$action = get_admin_url() . 'options.php';
} else {
	$action = get_admin_url() . 'admin-post.php';
}

?>

<div class="wrap">

	<h1><?php _e( 'Better Search Replace Pro', 'better-search-replace' ); ?></h1>
	<?php settings_errors(); ?>

	<div id="bsr-error-wrap"></div>

	<?php BSR_Admin::render_result(); ?>

	<h2 class="nav-tab-wrapper">
		<a href="?page=better-search-replace&tab=bsr_search_replace" class="nav-tab <?php echo $active_tab == 'bsr_search_replace' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Search/Replace', 'better-search-replace' ); ?></a>
		<a href="?page=better-search-replace&tab=bsr_backup_import" class="nav-tab <?php echo $active_tab == 'bsr_backup_import' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Backup/Import', 'better-search-replace' ); ?></a>
		<a href="?page=better-search-replace&tab=bsr_settings" class="nav-tab <?php echo $active_tab == 'bsr_settings' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Settings', 'better-search-replace' ); ?></a>
		<a href="?page=better-search-replace&tab=bsr_help" class="nav-tab <?php echo $active_tab == 'bsr_help' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Help', 'better-search-replace' ); ?></a>
	</h2>

	<form class="bsr-action-form" action="<?php echo $action; ?>" method="POST">

		<?php
		// Include the correct tab template.
		$bsr_template = str_replace( '_', '-', sanitize_file_name( $active_tab ) ) . '.php';
		if ( file_exists( BSR_PATH . 'templates/' . $bsr_template ) ) {
			include BSR_PATH . 'templates/' . $bsr_template;
		} else {
			include BSR_PATH . 'templates/bsr-search-replace.php';
		}
		?>

	</form>

</div><!-- /.wrap -->
