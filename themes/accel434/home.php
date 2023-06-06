<?php

remove_action('genesis_entry_footer', 'genesis_post_meta');

function ns_blog_archive()
{
    if (have_posts()) :
        do_action('genesis_before_while');
        echo "<div class='one2grid'>";
        while (have_posts()) :
            the_post();

            $id = get_the_ID();
            ns_blog_block($id);
        endwhile; // End of one post.
        echo '</div>';
        do_action('genesis_after_endwhile');
    else : // If no posts exist.
        do_action('genesis_loop_else');
    endif; // End loop.
}

// Remove the loop and replace it with our own.
remove_action('genesis_loop', 'genesis_do_loop');
add_action('genesis_loop', 'ns_blog_archive');

genesis();
