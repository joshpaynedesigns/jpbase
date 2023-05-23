<?php

function ns_blog_block($id, $display_date = true, $image_class = null)
{
    if (empty($id)) {
        return;
    }

    $thumbnail = get_the_post_thumbnail_url($id, 'medium');
    if (empty($thumbnail)) {
        $default_img = ns_get_field('default_banner_image_blog', 'option');
        $thumbnail   = ns_key_value($default_img, 'url');
    }

    $permalink = get_the_permalink($id);
    $title    = get_the_title($id);
    $date = get_the_date('F j, Y', $id);
    $short_description = ns_get_short_description($id, 20);

    ?>
        <div class="story">
            <?php if (! empty($thumbnail)) : ?>
                <a href="<?php echo $permalink ?>">
                    <div class="story-img <?php echo $image_class ?>" style="background-image: url(' <?php echo $thumbnail; ?> ')"></div>
                </a>
            <?php endif; ?>

            <div class="story-blurb">
                <?php if ($display_date) : ?>
                    <div class="story-date"><?php echo $date ?></div>
                <?php endif; ?>
                <h6 class="story-title mt-4"><a href="<?php echo $permalink ?>"><?php echo $title ?></a></h6>
                <?php if (! empty($short_description)) : ?>
                    <div class="story-blurb-content">
                        <?php echo $short_description ?>
                    </div>
                <?php endif; ?>
                <a class="read-more arrow-link" href="<?php echo $permalink ?>">Read More</a>
            </div>
        </div>
    <?php
}
