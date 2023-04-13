<?php

/**
* Scripts / Styles
* Handles front end scripts and styles.
*/

function ns_enqueue_scripts()
{

    // SVG Shim
    wp_register_script('svg4everybody', get_stylesheet_directory_uri() . '/assets/components/svg4everybody/dist/svg4everybody.min.js', array());
    wp_enqueue_script('svg4everybody');

    // Google Maps
    wp_register_script('gmaps', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyA4stZyfaZsPwdxHmW7STkkOdgjSIIroC0', array());
    wp_enqueue_script('gmaps');

    //  Google Fonts
    wp_enqueue_style('g-fonts-mont', 'https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;700&display=swap', array(), ACCEL_VERSION);
    wp_enqueue_style('g-fonts-prompt', 'https://fonts.googleapis.com/css2?family=Prompt:wght@400;600;700&display=swap', array(), ACCEL_VERSION);

    // Slick
    wp_register_script('slick', get_stylesheet_directory_uri() . '/assets/components/slick-carousel/slick/slick.min.js', '', true);
    wp_register_style('slick-css', get_stylesheet_directory_uri() . '/assets/components/slick-carousel/slick/slick.css');

    // Modaal
    wp_register_script('modaal', get_stylesheet_directory_uri() . '/assets/components/modaal/dist/js/modaal.min.js', array('jquery'), false, true);
    wp_register_style('modaal-css', get_stylesheet_directory_uri() . '/assets/components/modaal/dist/css/modaal.min.css');

    // Accessible Menu
    wp_register_script('gamajo-accessible-menu', get_stylesheet_directory_uri() . '/assets/components/accessible-menu/dist/jquery.accessible-menu.min.js', array('jquery'), '1.0.0', true);

    wp_register_script('sitewide', get_bloginfo('stylesheet_directory') . '/assets/js/build/site-wide.min.js', array('gamajo-accessible-menu', 'jquery'), ACCEL_VERSION, true);

    wp_enqueue_style('slick-css');
    wp_enqueue_script('modaal');
    wp_enqueue_style('modaal-css');
    wp_enqueue_script('sitewide');
    wp_enqueue_script('slick');

    $data_array = array(
        'stylesheetUrl' => get_stylesheet_directory_uri()
    );

    wp_localize_script('sitewide', 'data', $data_array);
}

add_action('wp_enqueue_scripts', 'ns_enqueue_scripts');
