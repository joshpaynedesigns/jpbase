<?php

if (! function_exists('mktg434_service_cpt')) {
    // Register Custom Post Type
    function mktg434_service_cpt()
    {
        $labels = array(
            'name'                  => _x('Services', 'Post Type General Name', 'text_domain'),
            'singular_name'         => _x('Service', 'Post Type Singular Name', 'text_domain'),
            'menu_name'             => __('Services', 'text_domain'),
            'name_admin_bar'        => __('Services', 'text_domain'),
            'archives'              => __('Services Archives', 'text_domain'),
            'parent_item_colon'     => __('Parent Item:', 'text_domain'),
            'all_items'             => __('All Services', 'text_domain'),
            'add_new_item'          => __('Add New Service', 'text_domain'),
            'add_new'               => __('Add New', 'text_domain'),
            'new_item'              => __('New Service', 'text_domain'),
            'edit_item'             => __('Edit Service', 'text_domain'),
            'update_item'           => __('Update Service', 'text_domain'),
            'view_item'             => __('View Service', 'text_domain'),
            'search_items'          => __('Search Services', 'text_domain'),
            'not_found'             => __('Not found', 'text_domain'),
            'not_found_in_trash'    => __('Not found in Trash', 'text_domain'),
            'featured_image'        => __('Featured Image', 'text_domain'),
            'set_featured_image'    => __('Set featured image', 'text_domain'),
            'remove_featured_image' => __('Remove featured image', 'text_domain'),
            'use_featured_image'    => __('Use as featured image', 'text_domain'),
            'insert_into_item'      => __('Insert into Service', 'text_domain'),
            'uploaded_to_this_item' => __('Uploaded to this Service', 'text_domain'),
            'items_list'            => __('Services list', 'text_domain'),
            'items_list_navigation' => __('Services list navigation', 'text_domain'),
            'filter_items_list'     => __('Filter Services list', 'text_domain'),
        );
        $args   = array(
            'label'               => __('Service', 'text_domain'),
            'description'         => __('Post Type Description', 'text_domain'),
            'labels'              => $labels,
            'supports'            => array( 'title', 'revisions' ),
            'taxonomies'          => array(),
            'hierarchical'        => true,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-yes',
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => true,
            'can_export'          => true,
            'has_archive'         => false,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'page',
            'rewrite'             => array(
                'slug'       => 'services',
                'with_front' => false,
            ),
        );
        register_post_type('service', $args);
    }

    add_action('init', 'mktg434_service_cpt', 0);
}

// Rename the title text on creating a new post
function ns_change_title_for_Services($title)
{
    $screen = get_current_screen();

    if ('Service' == $screen->post_type) {
        $title = 'Enter Service Title';
    }

    return $title;
}
add_filter('enter_title_here', 'ns_change_title_for_Services');

add_post_type_support('service', 'genesis-scripts');
