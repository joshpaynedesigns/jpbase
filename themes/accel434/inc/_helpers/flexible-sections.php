<?php

function ns_flexible_sections()
{
    if (is_page('resources') || is_category() || is_tax('resource_topic')) {
        echo '<section id="flexible-section-repeater" class="resource-blocks">';
    } else {
        echo '<section id="flexible-section-repeater">';
    }
    if (post_password_required()) {
        echo '<div id="password-protected" class="wrap">';
        the_content();
        echo '</div>';
    } else {
        $post_id = false;

        $queried_object = get_queried_object();
        if (is_object($queried_object) && is_a($queried_object, 'WP_Term')) {
            $post_id = $queried_object->taxonomy . '_' . $queried_object->term_id;
        }

        if (have_rows('page_flexible_sections', $post_id)) {
            while (have_rows('page_flexible_sections', $post_id)) {
                the_row();
                $section = get_row_layout();
                get_template_part('partials/sections/' . $section);
            }
        } elseif (have_rows('post_flexible_sections', $post_id)) {
            while (have_rows('post_flexible_sections', $post_id)) {
                the_row();
                $section = get_row_layout();
                get_template_part('partials/sections/' . $section);
            }
        }
    }
    echo '</section>';
}

function ns_decide_section_classes($bg_color = null)
{
    $classes = "";

    if (empty($bg_color) || $bg_color === 'none') {
        $classes .= "sectionmt sectionmb";
    } else {
        $classes .= "sectionpt sectionpb";
    }

    if (! empty($bg_color)) {
        $classes .= " has-bg-color bg-" . $bg_color;

        if (ns_is_bg_dark($bg_color)) {
            $classes .= " dark-background";
        } elseif (ns_is_bg_light($bg_color)) {
            $classes .= " light-background";
        }
    }

    return $classes;
}

$ns_dark_bg_colors = array(
    "medium-gray" => "Medium Gray",
    "dark-gray" => "Dark Gray",
    "blue" => "Blue",
    "light-blue" => "Light Blue",
);

$ns_light_bg_colors = array(
    "none" => "None",
    "light-gray" => "Light Gray",
);

$ns_button_colors = array(
    'blue-button' => "Blue",
    'primary-button' => "Primary",
    'light-blue-button' => "Light Blue",
    'medium-gray-button' => "Medium Gray",
    'dark-gray-button' => "Dark Gray",
    'white-button' => "White",
);

function ns_is_bg_dark($bg_color = null)
{
    global $ns_dark_bg_colors;
    return array_key_exists($bg_color, $ns_dark_bg_colors);
}


function ns_is_bg_light($bg_color = null)
{
    global $ns_light_bg_colors;
    return array_key_exists($bg_color, $ns_light_bg_colors);
}

// Add BG options dynamically to ACF fields
function ns_acf_dynamic_bg_colors($field)
{
    global $ns_dark_bg_colors;
    global $ns_light_bg_colors;
    global $ns_button_colors;

    $field_name = ns_key_value($field, 'name');

    if ($field_name === 'section_bg_color' || $field_name === 'tile_bg_color' ||$field_name === 'box_bg_color') {
        $section_bg_color = array_merge($ns_light_bg_colors, $ns_dark_bg_colors);

        $field['choices'] = $section_bg_color;
        $field['default_value'] = 'none';
    }

    if ($field_name === 'ribbon_cta_bg_color') {
        $field['choices'] = $ns_dark_bg_colors;
        $field['default_value'] = 'blue';
    }

    if ($field_name === 'section_light_bg_color') {
        $field['choices'] = $ns_light_bg_colors;
        $field['default_value'] = 'none';
    }

    if ($field_name === 'button_color') {
        $field['choices'] = $ns_button_colors;
        $field['default_value'] = 'blue-button';
    }

    // return the field
    return $field;
}
add_filter('acf/load_field', 'ns_acf_dynamic_bg_colors');
