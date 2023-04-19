<?php

function ns_flexible_sections()
{
    echo '<section id="flexible-section-repeater">';
    if (post_password_required()) {
        echo '<div id="password-protected" class="wrap">';
        the_content();
        echo '</div>';
    } else {
        if (have_rows('page_flexible_sections')) {
            while (have_rows('page_flexible_sections')) {
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
        $classes .= " bg-" . $bg_color;
    }

    return $classes;
}
