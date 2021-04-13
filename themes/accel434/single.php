<?php

// full width layout
add_filter ( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );

// Force the post title to use h1
add_filter( 'genesis_post_title_output', 'vrec_post_title_output', 15 );
function vrec_post_title_output( $title ) {
	$title = sprintf( '<h1 id="post-title">%s</h1>', get_the_title() );
	return $title;
}

// Customize the post info function
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
add_action( 'genesis_entry_header', 'genesis_post_info', 8 );
add_filter( 'genesis_post_info', 'sp_post_info_filter' );
function sp_post_info_filter($post_info) {
	// $post_info = '[post_date] | [post_author_posts_link before="By: "]';
	$post_info = '[post_date]';
	return $post_info;
}

genesis();