<?php

// * Force content-sidebar layout
add_filter('genesis_pre_get_option_site_layout', '__genesis_return_full_width_content');

add_action('ns_page_content', 'ns_flexible_sections');

// Build the page
get_header();
do_action('ns_page_content');
get_footer();
