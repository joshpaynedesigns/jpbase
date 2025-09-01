<?php

function ns_minify_svg($svg_contents)
{
    // Remove comments
    $svg_contents = preg_replace('/<!--.*?-->/', '', $svg_contents);

    // Remove newlines and tabs
    $svg_contents = preg_replace('/[\r\n\t]/', '', $svg_contents);

    // Remove unnecessary spaces within tags
    $svg_contents = preg_replace('/\s*=\s*/', '=', $svg_contents);

    // Remove multiple consecutive spaces
    $svg_contents = preg_replace('/\s+/', ' ', $svg_contents);

    // Trim leading and trailing spaces
    $svg_contents = trim($svg_contents);

    return $svg_contents;
}

function ns_get_svg_icon($file_name, $width = 20, $class = '')
{
    $file_path = get_stylesheet_directory() . '/assets/icons/' . $file_name . '.svg';

    if (! file_exists($file_path)) {
        return "";
    }

    // Get the contents of the SVG file
    $svg_content = file_get_contents($file_path);

    if (empty($svg_content)) {
        return "";
    }

    // Parse the SVG content into a SimpleXMLElement
    $xml_element = new SimpleXMLElement($svg_content);

    // Add the new width attribute (or replace existing one)
    $xml_element['width'] = $width;

    if (empty($class)) {
        $class = $file_name;
    }

    // Add the new class attribute (or replace the existing one)
    $xml_element['class'] = $class;

    // Minify the SVG
    $svg_string = ns_minify_svg($xml_element->asXML());

    if (! empty($svg_string)) {
        return $svg_string;
    }

    return "";
}
