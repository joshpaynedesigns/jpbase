<?php
$content = get_sub_field('fifty_content');
$content_pos = get_sub_field('fifty_position');
$image = get_sub_field('fifty_image');
$video_url = get_sub_field('video_url');
$show_content_over_image = get_sub_field('show_content_over_image');
$over_image_content = get_sub_field('over_image_content');

$bg_class = '';
if ($show_content_over_image && ! empty($over_image_content)) {
    $bg_class = 'has-content-over';
}
?>

<section class="fifty-fifty-section">

    <div class="image item <?php echo $content_pos; ?>">
        <?php
        $fifty_image = wp_get_attachment_image_src($image, 'large')[0];
        ?>
        <?php if (! empty($fifty_image)) : ?>
            <div class="bg <?php echo $bg_class; ?>" style="background: url('<?php echo $fifty_image; ?>') no-repeat 50% 50%; background-size: cover;"></div>
            <?php if (! empty($video_url)) : ?>
                <div class="video-bg-overlay"></div>
                <span class="play-button"></span>
                <?php echo do_shortcode('[arve url="' . $video_url . '" mode="link-lightbox" loop="no" muted="no" /]'); ?>
            <?php endif; ?>
            <?php if ($show_content_over_image) : ?>
                <div class="image-blurb-wrap content-<?php echo $content_pos; ?>">
                    <div class="image-blurb-inner-wrap">
                        <div class="image-blurb-content">
                            <?php echo $over_image_content; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="wrap content-<?php echo $content_pos; ?>">
        <div class="content-section item <?php echo $content_pos; ?>">
            <div class="content-section__inner fcmt0 lcmb0">
                <?php if (! empty($content)) : ?>
                    <?php echo $content; ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="item empty"></div>
    </div>

</section>
