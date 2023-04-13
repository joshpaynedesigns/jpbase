<?php

/*
Template Name: Content Builder
*/

// full width layout
add_filter ( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );

// Remove 'site-inner' from structural wrap
add_theme_support( 'genesis-structural-wraps', array( 'header', 'nav', 'subnav', 'footer-widgets', 'footer' ) );

// Add attributes for site-inner element, since we're removing 'content'.
add_filter( 'genesis_attr_site-inner', 'ns_site_inner_attr' );
function ns_site_inner_attr( $attributes ) {
	$attributes['role']     = 'main';
	$attributes['itemprop'] = 'mainContentOfPage';
	return $attributes;
}

add_action( 'ns_page_content', 'ns_flexible_sections' );
function ns_flexible_sections() {
	echo '<section id="flexible-section-repeater">';
	if ( post_password_required() ) {
		echo '<div id="password-protected" class="wrap">';
		the_content();
		echo '</div>';
	} else {
		$fcs = FlexibleContentSectionFactory::create('page_flexible_sections');
		$fcs->run();
	}
	echo '</section>';
}

// Build the page
get_header();
do_action( 'ns_page_content' );
get_footer();
