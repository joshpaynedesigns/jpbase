<?php

function ns_get_location_full_address($id)
{
    if (empty($id)) {
        return;
    }

    $ad1 = ns_get_field('loc_address_line_1', $id);
    $ad2 = ns_get_field('loc_address_line_2', $id);

    if ($ad1 && $ad2) {
        return $ad1 . ', ' . $ad2;
    }

    return $ad1;
}

function ns_locations_archive_location($id = null)
{


    if (empty($id)) {
        return;
    }

    $title     = get_the_title($id);
    $permalink      = get_the_permalink($id);
    $address   = ns_get_location_full_address($id);

    $thumbnail = get_the_post_thumbnail_url($id, 'large');
    if (empty($thumbnail)) {
        $default_img = ns_get_field('default_banner_image_locations', 'option');
        $thumbnail   = $default_img['url'];
    }

    $permalink = get_the_permalink($id);
    $title    = get_the_title($id);

    ?>
        <div class="story">
            <?php if (! empty($thumbnail)) : ?>
                <a href="<?php echo $permalink ?>">
                    <div class="story-img" style="background-image: url(' <?php echo $thumbnail; ?> ')"></div>
                </a>
            <?php endif; ?>

            <div class="story-blurb">
                <h6 class="story-title mt-4"><a href="<?php echo $permalink ?>"><?php echo $title ?></a></h6>
                <?php if (! empty($address)) : ?>
                    <div class="smallmb"><?php echo $address ?></div>
                <?php endif; ?>
                <div class="story-blurb-content">
                    <?php echo ns_get_short_description($id, 22); ?>
                </div>
                <a class="read-more arrow-link" href="<?php echo $permalink ?>">Learn More</a>
            </div>
        </div>
    <?php
}

function ns_locations_archive_custom_loop()
{
    global $wp_query;
    $wp_query->set('posts_per_page', 99999);
    $wp_query->query($wp_query->query_vars);
    ?>
    <div class="">
        <h1 class="post-title f32"><?php echo get_the_archive_title() ?></h1>
        <?php if (have_posts()) : ?>
            <div class="locations-list basemt">
                <?php while (have_posts()) : ?>
                    <?php the_post(); ?>
                    <?php ns_locations_archive_location(get_the_ID()); ?>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            Sorry, no matching locations.
        <?php endif; ?>
    </div>
    <?php
}
