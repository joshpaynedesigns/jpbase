<?php
add_action('wp', 'objectiv_remove_shopp_entry_footer_header');

function objectiv_remove_shopp_entry_footer_header() {
	if ( function_exists('is_shopp_page') && is_shopp_page() ) {
		remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
		remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
		remove_action( 'genesis_after_entry', 'genesis_get_comments_template' );
	}
}
