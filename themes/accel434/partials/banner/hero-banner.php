<?php

//Hide Banner
$hide_banner_options = ns_get_field('hide_banner_options');

// Set up the height class
$height_class = decide_banner_height_class();

// Text Color and Overlay
$txt_color = ns_get_field( 'text_color' );
$overlay = ns_get_field( 'overlay' );

if ($hide_banner_options == 'hide_banner_image') {
    $overlay = "none";
} elseif ( empty( $overlay ) ) {
    $overlay = "dark-overlay";
}

if ($hide_banner_options == 'hide_banner_image') {
    $txt_color = "dark-text";
} elseif ( empty( $txt_color ) ) {
    $txt_color = "light-text";
}

// Figure the Title and Sub Title Out
$title = ns_get_field( 'banner_title' );
if ( empty( $title ) ) {
    $title = decide_banner_title();
}
$subtitle = ns_get_field( 'banner_sub_title' );
if ( empty( $subtitle ) ) {
    $subtitle = decide_banner_subtitle();
}

// Set up thumbnail
if ($hide_banner_options == 'hide_banner_image') {
    $bg_image_url = "";
} else {
    $bg_image_url = decide_banner_bg_img();
}

?>
<section class="page-banner-slider <?php echo $height_class ?> <?php echo $hide_banner_options ?>">
    <div class="page-banner__slide <?php echo $txt_color; ?>" style="background-image: url(<?php echo $bg_image_url ?>)">
        <div class="wrap">
            <div class="page-banner__content">
                <?php if (is_single() && !is_singular( 'projects' )): ?>
                    <span id="heading-one" class="page-banner__title"><?php echo $title; ?></span>
                <?php else: ?>
                    <h1 class="page-banner__title"><?php echo $title; ?></h1>
                <?php endif; ?>
                <?php if ( ! empty( $subtitle ) ): ?>
                    <h4 class="page-banner__subtitle"><?php echo $subtitle; ?></h4>
                <?php endif; ?>
            </div>
        </div>


        <?php if ( $overlay != 'none' ): ?>
            <div class="overlay <?php echo $overlay; ?>"></div>
        <?php endif; ?>


    </div>
</section>
<div class="skipContentAnchor" id="afterBanner"></div>

<?php
// Decide the banner image
function decide_banner_bg_img() {
    // $thumbnail_id = get_post_thumbnail_id();
    // $thumbnail_url = wp_get_attachment_image_url( $thumbnail_id, 'full' );

    // If we are on an archive or post type lets use that defualt image
    if ( is_home() || is_category() || is_date() || is_singular( 'post' ) ) {
        $default_bg_image_id = ns_get_field( 'default_banner_image_blog', 'options' );
        $bg_image_url = wp_get_attachment_image_url( $default_bg_image_id['ID'], 'full' );
    } elseif ( is_page() ) {
        $default_bg_image_id = ns_get_field( 'banner_image' )['ID'];
        $bg_image_url = wp_get_attachment_image_url( $default_bg_image_id, 'full' );
    } elseif ( is_post_type_archive( 'testimonial' ) || is_singular( 'testimonial' ) ) {
        $default_bg_image_id = ns_get_field( 'default_banner_image_testimonials', 'options' )['ID'];
        $bg_image_url = wp_get_attachment_image_url( $default_bg_image_id, 'full' );
    } elseif ( is_post_type_archive( 'staff' ) || is_singular( 'staff' ) ) {
        $default_bg_image_id = ns_get_field( 'default_banner_image_staff', 'options' )['ID'];
        $bg_image_url = wp_get_attachment_image_url( $default_bg_image_id, 'full' );
    } elseif ( is_post_type_archive( 'projects' ) || is_singular( 'projects' ) ) {
        $default_bg_image_id = ns_get_field( 'default_banner_image_projects', 'options' )['ID'];
        $bg_image_url = wp_get_attachment_image_url( $default_bg_image_id, 'full' );
    } elseif ( is_post_type_archive( 'location' ) || is_singular( 'location' ) ) {
        $default_bg_image_id = ns_get_field( 'default_banner_image_locations', 'options' )['ID'];
        $bg_image_url = wp_get_attachment_image_url( $default_bg_image_id, 'full' );
    } elseif ( is_event_calendar_page() ) {
        $default_bg_image_id = ns_get_field( 'default_banner_image_events', 'options' )['ID'];
        $bg_image_url = wp_get_attachment_image_url( $default_bg_image_id, 'full' );
    }

    // If default img not empty use it, if its empty use the base default
    if ( empty( $bg_image_url ) ) {
        $default_bg_image_id = ns_get_field( 'default_banner_image', 'options' );
        $bg_image_url = wp_get_attachment_image_url( $default_bg_image_id['ID'], 'full' );
    }

    // if ( ! empty( $thumbnail_url )) {
    //  $bg_image_url = $thumbnail_url;
    // }

    return $bg_image_url;
}

// Decide the banner height
function decide_banner_height_class() {
    $custom_height = ns_get_field( 'banner_custom_height' );
    $banner_height = ns_get_field( 'banner_height' );

    if ( is_home() || is_category() || is_date() || is_singular( 'post' ) ) {
        $default_banner_height = ns_get_field( 'default_banner_height_blog', 'option' );
    } elseif ( is_post_type_archive( 'testimonial' ) || is_singular( 'testimonial' ) ) {
        $default_banner_height = ns_get_field( 'default_banner_height_testimonials', 'option' );
    } elseif ( is_post_type_archive( 'staff' ) || is_singular( 'staff' ) ) {
        $default_banner_height = ns_get_field( 'default_banner_height_staff', 'option' );
    } elseif ( is_post_type_archive( 'projects' ) || is_singular( 'projects' ) ) {
        $default_banner_height = ns_get_field( 'default_banner_height_projects', 'option' );
    } elseif ( is_post_type_archive( 'location' ) || is_singular( 'location' ) ) {
        $default_banner_height = ns_get_field( 'default_banner_height_locations', 'option' );
    } elseif ( is_event_calendar_page() ) {
        $default_banner_height = ns_get_field( 'default_banner_height_events', 'option' );
    }

    if ( empty( $default_banner_height ) ) {
        $default_banner_height = ns_get_field( 'default_banner_height', 'option' );
    }

    // Set up the banner height class
    $height_class = '';
    if ( !empty( $banner_height ) && $custom_height ) {
        $height_class = $banner_height . '-height-banner';
    } elseif ( !empty( $default_banner_height ) && !$custom_height ) {
        $height_class = $default_banner_height . '-height-banner';
    } else {
        $height_class = 'medium-height-banner';
    }

    return $height_class;
}

// Decide titles for banners
function decide_banner_title() {
    $title = '';
    if ( is_home() || is_category() || is_date() || is_singular( 'post' ) ) {
        $page_for_posts = get_option( 'page_for_posts' );
        $custom_title = ns_get_field( 'archive_title_blog', 'option' );
        $title = get_the_title( $page_for_posts );

        if ( !empty( $custom_title ) ) {
            $title = $custom_title;
        }
    } elseif ( is_post_type_archive( 'testimonial' ) || is_singular( 'testimonial' ) ) {
        $title = ns_get_field( 'archive_title_testimonials', 'option' );
        if ( empty( $title ) ) {
            $title = post_type_archive_title( '', false );
        }
    } elseif ( is_post_type_archive( 'staff' ) || is_singular( 'staff' ) ) {
        $title = ns_get_field( 'archive_title_staff', 'option' );
        if ( empty( $title ) ) {
            $title = post_type_archive_title( '', false );
        }
    } elseif ( is_post_type_archive( 'projects' ) ) {
        $title = ns_get_field( 'archive_title_projects', 'option' );
        if ( empty( $title ) ) {
            $title = post_type_archive_title( '', false );
        }
    } elseif ( is_post_type_archive( 'location' ) || is_singular( 'location' ) ) {
        $title = ns_get_field( 'archive_title_locations', 'option' );
        if ( empty( $title ) ) {
            $title = post_type_archive_title( '', false );
        }
    } elseif ( is_event_calendar_page() ) {
        $title = ns_get_field( 'archive_title_events', 'option' );
        if ( empty( $title ) ) {
            $title = post_type_archive_title( '', false );
        }
    } elseif ( is_search() ) {
        $title = 'Search: ' . get_search_query();
    } elseif ( is_404() ) {
        $title = "This page returned a 404";
    } else {
        $title = get_the_title();
    }

    return $title;
}

// Decide Sub titles for banners
function decide_banner_subtitle() {
    $subtitle = '';
    if ( is_home() || is_category() || is_date() || is_singular( 'post' ) ) {
        $subtitle = ns_get_field( 'archive_sub_title_blog', 'option' );
    } elseif ( is_post_type_archive( 'testimonial' ) || is_singular( 'testimonial' ) ) {
        $subtitle = ns_get_field( 'archive_sub_title_testimonials', 'option' );
    } elseif ( is_post_type_archive( 'staff' ) || is_singular( 'staff' ) ) {
        $subtitle = ns_get_field( 'archive_sub_title_staff', 'option' );
    } elseif ( is_post_type_archive( 'projects' ) ) {
        $subtitle = ns_get_field( 'archive_sub_title_projects', 'option' );
    } elseif ( is_singular( 'projects' ) ) {
        $subtitle = custom_taxonomies_terms();
    } elseif ( is_post_type_archive( 'location' ) || is_singular( 'location' ) ) {
        $subtitle = ns_get_field( 'archive_sub_title_locations', 'option' );
    } elseif ( is_event_calendar_page() ) {
        $subtitle = ns_get_field( 'archive_sub_title_events', 'option' );
    }

    return $subtitle;
}
