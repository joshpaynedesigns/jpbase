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

    if (! empty($bg_color) && $bg_color !== 'none') {
        $classes .= " has-bg-color bg-" . $bg_color;
    }

    return $classes;
}

function ns_is_bg_dark($bg_color = null)
{
    $dark_bg_colors = array(
        'medium-gray',
        'dark-gray',
        'blue',
        'light-blue',
    );

    return in_array($bg_color, $dark_bg_colors);
}
