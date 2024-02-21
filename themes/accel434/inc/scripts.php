<?php

/**
* Scripts / Styles
* Handles front end scripts and styles.
*/

function ns_enqueue_scripts()
{

    // Google Maps API on single locations frontend
    if (is_singular('location')) {
        wp_enqueue_script('gmaps', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyA4stZyfaZsPwdxHmW7STkkOdgjSIIroC0&callback=Function.prototype', array());
    }

    //  Google Fonts
    wp_enqueue_style('g-fonts-mont', 'https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap', array(), ACCEL_VERSION);

    // Slick
    wp_register_script('slick', get_stylesheet_directory_uri() . '/assets/components/slick-carousel/slick/slick.min.js', '', true);
    wp_register_style('slick-css', get_stylesheet_directory_uri() . '/assets/components/slick-carousel/slick/slick.css');

    // Modaal
    wp_register_script('modaal', get_stylesheet_directory_uri() . '/assets/components/modaal/dist/js/modaal.min.js', array('jquery'), false, true);
    wp_register_style('modaal-css', get_stylesheet_directory_uri() . '/assets/components/modaal/dist/css/modaal.min.css');

    // Accessible Menu
    wp_register_script('gamajo-accessible-menu', get_stylesheet_directory_uri() . '/assets/components/accessible-menu/dist/jquery.accessible-menu.min.js', array('jquery'), '1.0.0', true);

    wp_register_script('sitewide', get_bloginfo('stylesheet_directory') . '/dist/front.js', array('jquery'), ACCEL_VERSION, true);

    wp_enqueue_style('slick-css');
    wp_enqueue_script('modaal');
    wp_enqueue_style('modaal-css');
    wp_enqueue_script('sitewide');
    wp_enqueue_script('slick');

    wp_enqueue_style('accel-styles', get_stylesheet_directory_uri() . '/dist/front.css', '', ACCEL_VERSION);

    $data_array = array(
        'stylesheetUrl' => get_stylesheet_directory_uri()
    );

    wp_localize_script('sitewide', 'data', $data_array);
}
add_action('wp_enqueue_scripts', 'ns_enqueue_scripts');

function ns_add_editor_styles_sub_dir()
{
    add_editor_style(get_stylesheet_directory_uri() . '/dist/editor-style.css');
}
add_action('after_setup_theme', 'ns_add_editor_styles_sub_dir');
