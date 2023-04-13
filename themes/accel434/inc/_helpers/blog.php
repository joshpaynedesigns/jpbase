<?php

function ns_blog_block( $id ) {
    if ( empty( $id ) ) {
        return;
    }

    $thumbnail = get_the_post_thumbnail_url($id, 'medium');
    if (empty($thumbnail)) {
        $default_img = ns_get_field('default_banner_image_blog', 'option');
        $thumbnail   = $default_img['url'];
    }

    $permalink = get_the_permalink($id);
    $title    = get_the_title($id);

    ?>
        <div class="story">
            <?php if (! empty($thumbnail)) : ?>
                <a href="<?php echo $permalink ?>">
                    <div class="story-img special-offset-border" style="background-image: url(' <?php echo $thumbnail; ?> ')"></div>
                </a>
            <?php endif; ?>

            <div class="story-blurb mx-4">
                <h6 class="story-title mt-8"><a href="<?php echo $permalink ?>"><?php echo $title ?></a></h6>
                <div class="story-blurb-content">
                    <?php echo ns_get_short_description($id, 14); ?>
                </div>
                <a class="read-more arrow-link" href="<?php echo $permalink ?>">Read More</a>
            </div>
        </div>
    <?php
}
