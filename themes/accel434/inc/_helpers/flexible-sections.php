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
                print( '<pre>' . print_r($section, true) . '</pre>' );
                get_template_part('partials/sections/' . $section);
            }
        }
    }
    echo '</section>';
}
