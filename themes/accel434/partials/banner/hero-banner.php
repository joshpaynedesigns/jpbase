<?php

// Hide Banner
$hide_banner_options = ns_get_field('hide_banner_options');

// Set up the height class
$height_class = decide_banner_height_class();

// Text Color and Overlay
$txt_color = ns_get_field('text_color');
$overlay   = ns_get_field('overlay');

if ($hide_banner_options == 'hide_banner_image') {
    $overlay = 'none';
} elseif (empty($overlay)) {
    $overlay = 'dark-overlay';
}

if ($hide_banner_options == 'hide_banner_image') {
    $txt_color = 'dark-text';
} elseif (empty($txt_color)) {
    $txt_color = 'light-text';
}

// Figure the Title and Sub Title Out
$title = ns_get_field('banner_title');
if (empty($title)) {
    $title = decide_banner_title();
}
$subtitle = ns_get_field('banner_sub_title');
if (empty($subtitle)) {
    $subtitle = decide_banner_subtitle();
}

$button = ns_get_field('banner_button');

// Set up thumbnail
if ($hide_banner_options == 'hide_banner_image') {
    $bg_image_url = '';
} else {
    $bg_image_url = decide_banner_bg_img();
}

?>
<div class="page-hero-wrap">
    <section class="banner-slider <?php echo $height_class; ?> <?php echo $hide_banner_options; ?>">
        <div class="banner_slide <?php echo $txt_color; ?>" style="background-image: url(<?php echo $bg_image_url; ?>)">
            <div class="wrap">
                <div class="banner_content">
                    <?php if (is_singular('location')) : ?>
                        <span id="heading-one" class="banner_title"><?php echo $title; ?></span>
                    <?php else : ?>
                        <h1 class="banner_title"><?php echo $title; ?></h1>
                    <?php endif; ?>
                    <?php if (! empty($subtitle)) : ?>
                        <div class="banner_subtitle"><?php echo $subtitle; ?></div>
                    <?php endif; ?>
                    <?php if ($button) : ?>
                        <div class="flex justify-center items-center basemt">
                            <?php echo ns_link_button($button, 'green-button'); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php if ($overlay != 'none') : ?>
                <div class="overlay <?php echo $overlay; ?>"></div>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php
// Decide the banner image
function decide_banner_bg_img()
{
    $bg_image_url = '';
    $default_bg_image = false;
    // If we are on an archive or post type lets use that defualt image
    if (is_home() || is_category() || is_tag() || is_date() || is_singular('post')) {
        $default_bg_image = ns_get_field('default_banner_image_blog', 'options');
    } elseif (is_page()) {
        $default_bg_image = ns_get_field('banner_image');
    } elseif (is_post_type_archive('testimonial')) {
        $default_bg_image = ns_get_field('default_banner_image_testimonials', 'options');
    } elseif (is_singular('service')) {
        $default_bg_image = ns_get_field('default_banner_image_services', 'options');
    } elseif (is_post_type_archive('staff') || is_singular('staff')) {
        $default_bg_image = ns_get_field('default_banner_image_staff', 'options');
    } elseif (is_post_type_archive('location') || is_singular('location')) {
        $default_bg_image = ns_get_field('default_banner_image_locations', 'options');
    } elseif (is_event_calendar_page()) {
        $default_bg_image = ns_get_field('default_banner_image_events', 'options');
    }

    // If default img not empty use it, if its empty use the base default
    if (! $default_bg_image && empty($bg_image_url)) {
        $default_bg_image = ns_get_field('default_banner_image', 'options');
    }

    if ($default_bg_image) {
        $bg_image_url = wp_get_attachment_image_url($default_bg_image['ID'], 'full');
    }

    return $bg_image_url;
}

// Decide the banner height
function decide_banner_height_class()
{
    $custom_height = ns_get_field('banner_custom_height');
    $banner_height = ns_get_field('banner_height');

    if (is_home() || is_category() || is_tag() || is_date() || is_singular('post')) {
        $default_banner_height = ns_get_field('default_banner_height_blog', 'option');
    } elseif (is_post_type_archive('testimonial')) {
        $default_banner_height = ns_get_field('default_banner_height_testimonials', 'option');
    } elseif (is_post_type_archive('staff') || is_singular('staff')) {
        $default_banner_height = ns_get_field('default_banner_height_staff', 'option');
    } elseif (is_post_type_archive('location') || is_singular('location')) {
        $default_banner_height = ns_get_field('default_banner_height_locations', 'option');
    } elseif (is_event_calendar_page()) {
        $default_banner_height = ns_get_field('default_banner_height_events', 'option');
    } elseif (is_singular('service')) {
        $default_banner_height = ns_get_field('default_banner_height_services', 'option');
    }

    if (empty($default_banner_height)) {
        $default_banner_height = ns_get_field('default_banner_height', 'option');
    }

    // Set up the banner height class
    $height_class = '';
    if (! empty($banner_height) && $custom_height) {
        $height_class = $banner_height . '-height-banner';
    } elseif (! empty($default_banner_height) && ! $custom_height) {
        $height_class = $default_banner_height . '-height-banner';
    } else {
        $height_class = 'medium-height-banner';
    }

    return $height_class;
}

// Decide titles for banners
function decide_banner_title()
{
    $title = '';
    if (is_home() || is_category() || is_tag() || is_date() || is_singular('post')) {
        $page_for_posts = get_option('page_for_posts');
        $custom_title   = ns_get_field('archive_title_blog', 'option');
        $title          = get_the_title($page_for_posts);

        if (! empty($custom_title)) {
            $title = $custom_title;
        }
    } elseif (is_post_type_archive('testimonial')) {
        $title = ns_get_field('archive_title_testimonials', 'option');
        if (empty($title)) {
            $title = post_type_archive_title('', false);
        }
    } elseif (is_post_type_archive('staff') || is_singular('staff')) {
        $title = ns_get_field('archive_title_staff', 'option');
        if (empty($title)) {
            $title = post_type_archive_title('', false);
        }
    } elseif (is_post_type_archive('location') || is_singular('location')) {
        $title = ns_get_field('archive_title_locations', 'option');
        if (empty($title)) {
            $title = post_type_archive_title('', false);
        }
    } elseif (is_event_calendar_page()) {
        $title = ns_get_field('archive_title_events', 'option');
        if (empty($title)) {
            $title = post_type_archive_title('', false);
        }
    } elseif (is_search()) {
        $title = 'Search: ' . get_search_query();
    } elseif (is_404()) {
        $title = 'This page returned a 404';
    } else {
        $title = get_the_title();
    }

    return $title;
}

// Decide Sub titles for banners
function decide_banner_subtitle()
{
    $subtitle = '';
    if (is_home() || is_date() || is_singular('post')) {
        $subtitle = ns_get_field('archive_sub_title_blog', 'option');
    } elseif (is_post_type_archive('testimonial')) {
        $subtitle = ns_get_field('archive_sub_title_testimonials', 'option');
    } elseif (is_post_type_archive('staff') || is_singular('staff')) {
        $subtitle = ns_get_field('archive_sub_title_staff', 'option');
    } elseif (is_post_type_archive('location')) {
        $subtitle = ns_get_field('archive_sub_title_locations', 'option');
    } elseif (is_event_calendar_page()) {
        $subtitle = ns_get_field('archive_sub_title_events', 'option');
    } elseif (is_category() || is_tag()) {
        $subtitle = get_the_archive_title();
    }

    return $subtitle;
}
