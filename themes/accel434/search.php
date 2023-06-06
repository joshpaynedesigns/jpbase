<?php

add_filter('genesis_pre_get_option_site_layout', '__genesis_return_full_width_content');
remove_action('genesis_entry_footer', 'genesis_entry_footer_markup_open', 5);
remove_action('genesis_entry_footer', 'genesis_entry_footer_markup_close', 15);
remove_action('genesis_entry_footer', 'genesis_post_meta');

add_filter('genesis_post_info', 'sp_post_info_filter');
function sp_post_info_filter($post_info)
{
    $post_info = '[post_date]';
    return $post_info;
}

remove_action('genesis_entry_content', 'genesis_do_post_content');
add_action('genesis_entry_content', 'ns_custom_excerpt');
function ns_custom_excerpt()
{
    $the_ID = get_the_ID();
    $permalink = get_permalink($the_ID);
    $regular_excerpt = get_the_excerpt($the_ID);
    $acf_excerpt = get_post_meta($the_ID, 'page_flexible_sections_0_content', true);
    $post_type = get_post_type($the_ID);

    if (! empty($regular_excerpt)) {
        $excerpt = $regular_excerpt;
    } elseif (! empty($acf_excerpt)) {
        $excerpt = $acf_excerpt;
    } elseif ($post_type === 'location') {
        $excerpt = ns_get_field('loc_description', $the_ID);
    } else {
        $excerpt = "Sorry, there is no excerpt avaliable for this page/post. Please click the button below to read more.";
    }

    if (! empty($excerpt)) {
        $excerpt = wp_strip_all_tags(wp_trim_words($excerpt, $num_words = 60, $more = null));

        ?>
        <div class="search-excerpt">
            <p>
                <?php echo $excerpt ?>
            </p>
        </div>

        <div class="search-result-footer">
            <span class="blue-button small-button">
                <a href="<?php echo $permalink ?>">Read More</a>
            </span>
        </div>

        <?php
    }
}

genesis();
