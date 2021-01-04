<?php

if ( ! function_exists('mktg434_location_cpt') ) {

	// Register Custom Post Type
	function mktg434_location_cpt()
	{
	    $labels = array(
	        'name' => _x('Locations', 'Post Type General Name', 'text_domain'),
	        'singular_name' => _x('Location', 'Post Type Singular Name', 'text_domain'),
	        'menu_name' => __('Locations', 'text_domain'),
	        'name_admin_bar' => __('Locations', 'text_domain'),
	        'archives' => __('Locations Archives', 'text_domain'),
	        'parent_item_colon' => __('Parent Item:', 'text_domain'),
	        'all_items' => __('All Locations', 'text_domain'),
	        'add_new_item' => __('Add New Location', 'text_domain'),
	        'add_new' => __('Add New', 'text_domain'),
	        'new_item' => __('New Location', 'text_domain'),
	        'edit_item' => __('Edit Location', 'text_domain'),
	        'update_item' => __('Update Location', 'text_domain'),
	        'view_item' => __('View Location', 'text_domain'),
	        'search_items' => __('Search Locations', 'text_domain'),
	        'not_found' => __('Not found', 'text_domain'),
	        'not_found_in_trash' => __('Not found in Trash', 'text_domain'),
	        'featured_image' => __('Featured Image', 'text_domain'),
	        'set_featured_image' => __('Set featured image', 'text_domain'),
	        'remove_featured_image' => __('Remove featured image', 'text_domain'),
	        'use_featured_image' => __('Use as featured image', 'text_domain'),
	        'insert_into_item' => __('Insert into Location', 'text_domain'),
	        'uploaded_to_this_item' => __('Uploaded to this Location', 'text_domain'),
	        'items_list' => __('Locations list', 'text_domain'),
	        'items_list_navigation' => __('Locations list navigation', 'text_domain'),
	        'filter_items_list' => __('Filter Locations list', 'text_domain'),
	    );
	    $args = array(
	        'label' => __('Location', 'text_domain'),
	        'description' => __('Post Type Description', 'text_domain'),
	        'labels' => $labels,
	        'supports' => array('title', 'revisions'),
	        'taxonomies' => array(),
	        'hierarchical' => true,
	        'public' => true,
	        'show_ui' => true,
	        'show_in_menu' => true,
	        'menu_position' => 5,
	        'menu_icon' => 'dashicons-location',
	        'show_in_admin_bar' => true,
	        'show_in_nav_menus' => true,
	        'can_export' => true,
	        'has_archive' => true,
	        'exclude_from_search' => false,
	        'publicly_queryable' => true,
	        'capability_type' => 'page',
            'rewrite'               => array(
                'slug' => 'locations',
                'with_front' => false,
            ),
	    );
	    register_post_type('location', $args);
	}

    add_action('init', 'mktg434_location_cpt', 0);
}

// Rename the title text on creating a new post
function objectiv_change_title_for_locations($title)
{
    $screen = get_current_screen();

    if ('location' == $screen->post_type) {
        $title = 'Enter Location Title';
    }

    return $title;
}
add_filter('enter_title_here', 'objectiv_change_title_for_locations');

add_post_type_support( 'location', 'genesis-scripts' );