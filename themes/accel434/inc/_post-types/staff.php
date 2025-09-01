<?php

if (! function_exists('ns_staff_cpt')) {
// Register Custom Post Type
    function ns_staff_cpt()
    {

        $labels = array(
        'name'                  => _x('Staff', 'Post Type General Name', 'text_domain'),
        'singular_name'         => _x('Staff', 'Post Type Singular Name', 'text_domain'),
        'menu_name'             => __('Staff', 'text_domain'),
        'name_admin_bar'        => __('Staff', 'text_domain'),
        'archives'              => __('Staff Archives', 'text_domain'),
        'parent_item_colon'     => __('Parent Item:', 'text_domain'),
        'all_items'             => __('All Staff', 'text_domain'),
        'add_new_item'          => __('Add New Staff Member', 'text_domain'),
        'add_new'               => __('Add New', 'text_domain'),
        'new_item'              => __('New Staff Member', 'text_domain'),
        'edit_item'             => __('Edit Staff Member', 'text_domain'),
        'update_item'           => __('Update Staff Member', 'text_domain'),
        'view_item'             => __('View Staff Member', 'text_domain'),
        'search_items'          => __('Search Staff', 'text_domain'),
        'not_found'             => __('Not found', 'text_domain'),
        'not_found_in_trash'    => __('Not found in Trash', 'text_domain'),
        'featured_image'        => __('Featured Image', 'text_domain'),
        'set_featured_image'    => __('Set featured image', 'text_domain'),
        'remove_featured_image' => __('Remove featured image', 'text_domain'),
        'use_featured_image'    => __('Use as featured image', 'text_domain'),
        'insert_into_item'      => __('Insert into Staff Member', 'text_domain'),
        'uploaded_to_this_item' => __('Uploaded to this Staff Member', 'text_domain'),
        'items_list'            => __('Staff list', 'text_domain'),
        'items_list_navigation' => __('Staff list navigation', 'text_domain'),
        'filter_items_list'     => __('Filter Staff list', 'text_domain'),
        );
        $args = array(
        'label'                 => __('Staff Member', 'text_domain'),
        'description'           => __('Post Type Description', 'text_domain'),
        'labels'                => $labels,
        'supports'              => array( 'title', 'revisions', 'editor', 'thumbnail' ),
        'taxonomies'            => array( ),
        'hierarchical'          => true,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-groups',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'page',
        'rewrite'               => array(
            'slug' => 'staff',
            'with_front' => false,
        ),
        );
        register_post_type('staff', $args);
    }
    add_action('init', 'ns_staff_cpt', 0);
}


// Rename the title text on creating a new post
function ns_change_staff_member_title($title)
{
     $screen = get_current_screen();

    if ('staff' == $screen->post_type) {
         $title = 'Staff Member Name';
    }

     return $title;
}
add_filter('enter_title_here', 'ns_change_staff_member_title');

// Custom taxonomy for Categories on Staff
if (! function_exists('custom_taxonomy_staff_cat')) {
    // Register Custom Taxonomy
    function custom_taxonomy_staff_cat()
    {

        $labels = array(
            'name'                       => _x('Categories', 'Taxonomy General Name', 'text_domain'),
            'singular_name'              => _x('Category', 'Taxonomy Singular Name', 'text_domain'),
            'menu_name'                  => __('Categories', 'text_domain'),
            'all_items'                  => __('All Categories', 'text_domain'),
            'parent_item'                => __('Parent Category', 'text_domain'),
            'parent_item_colon'          => __('Parent Category:', 'text_domain'),
            'new_item_name'              => __('New Category Name', 'text_domain'),
            'add_new_item'               => __('Add New Category', 'text_domain'),
            'edit_item'                  => __('Edit Category', 'text_domain'),
            'update_item'                => __('Update Category', 'text_domain'),
            'view_item'                  => __('View Category', 'text_domain'),
            'separate_items_with_commas' => __('Separate categories with commas', 'text_domain'),
            'add_or_remove_items'        => __('Add or remove categories', 'text_domain'),
            'choose_from_most_used'      => __('Choose from the most used', 'text_domain'),
            'popular_items'              => __('Popular Categories', 'text_domain'),
            'search_items'               => __('Search Categories', 'text_domain'),
            'not_found'                  => __('Not Found', 'text_domain'),
            'no_terms'                   => __('No categories', 'text_domain'),
            'items_list'                 => __('Categories list', 'text_domain'),
            'items_list_navigation'      => __('Categories list navigation', 'text_domain'),
        );
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'rewrite'               => array(
                'slug' => 'staff-category',
                'with_front' => false,
            ),
        );
        register_taxonomy('staff-cat', array( 'staff' ), $args);
    }
    add_action('init', 'custom_taxonomy_staff_cat', 0);
}
