<?php

// Pull in each of the CPT files
// Don't forget to add your widget to the register widgets function in the functions.php file
include_once(get_stylesheet_directory() . '/inc/_widgets/contact-widget.php');

// Register Widgets
add_action('widgets_init', function () {
    register_widget('ns_Contact_Widget');
});
