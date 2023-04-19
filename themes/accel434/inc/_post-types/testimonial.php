<?php

if (! function_exists('ns_testimonial_cpt')) {
// Register Custom Post Type
    function ns_testimonial_cpt()
    {

        $labels = array(
        'name'                  => _x('Testimonials', 'Post Type General Name', 'text_domain'),
        'singular_name'         => _x('Testimonial', 'Post Type Singular Name', 'text_domain'),
        'menu_name'             => __('Testimonials', 'text_domain'),
        'name_admin_bar'        => __('Testimonials', 'text_domain'),
        'archives'              => __('Testimonials Archives', 'text_domain'),
        'parent_item_colon'     => __('Parent Item:', 'text_domain'),
        'all_items'             => __('All Testimonials', 'text_domain'),
        'add_new_item'          => __('Add New Testimonial', 'text_domain'),
        'add_new'               => __('Add New', 'text_domain'),
        'new_item'              => __('New Testimonial', 'text_domain'),
        'edit_item'             => __('Edit Testimonial', 'text_domain'),
        'update_item'           => __('Update Testimonial', 'text_domain'),
        'view_item'             => __('View Testimonial', 'text_domain'),
        'search_items'          => __('Search Testimonials', 'text_domain'),
        'not_found'             => __('Not found', 'text_domain'),
        'not_found_in_trash'    => __('Not found in Trash', 'text_domain'),
        'featured_image'        => __('Featured Image', 'text_domain'),
        'set_featured_image'    => __('Set featured image', 'text_domain'),
        'remove_featured_image' => __('Remove featured image', 'text_domain'),
        'use_featured_image'    => __('Use as featured image', 'text_domain'),
        'insert_into_item'      => __('Insert into Testimonial', 'text_domain'),
        'uploaded_to_this_item' => __('Uploaded to this Testimonial', 'text_domain'),
        'items_list'            => __('Testimonials list', 'text_domain'),
        'items_list_navigation' => __('Testimonials list navigation', 'text_domain'),
        'filter_items_list'     => __('Filter Testimonials list', 'text_domain'),
        );
        $args = array(
        'label'                 => __('Testimonial', 'text_domain'),
        'description'           => __('Post Type Description', 'text_domain'),
        'labels'                => $labels,
        'supports'              => array( 'title', 'revisions', 'editor' ),
        'taxonomies'            => array( ),
        'hierarchical'          => true,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-format-quote',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'page',
        'rewrite'               => array(
            'slug' => 'testimonials',
            'with_front' => false,
        ),
        );
        register_post_type('testimonial', $args);
    }
    add_action('init', 'ns_testimonial_cpt', 0);
}


// Rename the title text on creating a new post
function ns_change_testimonial_title($title)
{
     $screen = get_current_screen();

    if ('testimonial' == $screen->post_type) {
         $title = 'Name for Testimonial Attribution';
    }

     return $title;
}

add_filter('enter_title_here', 'ns_change_testimonial_title');

// Redirect single testimonials to the testimonials archive
function ns_redirect_single_testimonials()
{
    if (is_singular('testimonial')) {
        wp_redirect(home_url('/testimonials/'));
        die;
    }
}
add_action('template_redirect', 'ns_redirect_single_testimonials');
