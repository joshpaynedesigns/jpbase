<?php

/**
 * Get SVG Icon from Sprite
 * @param  string $file_name  file name of svg icon in /assets/icons/src
 * @param  string $class_name class name to be added to parent svg element
 * @param  integer $height    specify the height of the svg element
 * @param  integer $width     specify the width of the svg element
 * @return string             svg element to return
 */
function get_svg_icon($file_name, $class_name = '', $height = '', $width = '') {
    $prefix = 'ico';
    $path = get_stylesheet_directory_uri() . '/assets/icons/dist/src.svg';
    $svg_class = '';
    $height_attr = '';
    $width_attr = '';

    // Set the class name of the SVG element
    if ( ! empty( $class_name ) ) {
        $svg_class = 'class="' . $class_name . '"';
    }

    // Set the height of the SVG element
    if ( ! empty( $height ) ) {
        $svg_height = intval( $height );
        $height_attr = 'height="' . $height . 'px"';
    }

    // Set the width of the SVG element
    if ( ! empty( $width ) ) {
        $svg_width = intval( $width );
        $width_attr = 'width="' . $width . 'px"';
    }

    // Save the SVG element into var
    $output = '<svg ' . $svg_class . $height_attr . $width_attr . '><use xlink:href="' . $path .'#' . $prefix . '-' . $file_name . '" /></svg>';

    // Return SVG element if file_name exists
    if (! empty( $file_name ) ) {
        return $output;
    } else {
        return 'Sorry that icon can\'t be found.';
    }
}
