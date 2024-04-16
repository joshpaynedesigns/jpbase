<?php
$cta_slider = get_sub_field('cta_slider');
?>

<section class="cta-section">
    <div class="cta-slider-wrap ns-slider-arrows-wrap">
        <div class="cta-slider">
            <?php foreach ($cta_slider as $cta_slide) :
                $bg_type = $cta_slide['background_type'];
                $bg_img = ns_key_value($cta_slide, 'background_image');
                $bg_img = ns_key_value($bg_img, 'url');
                $disable_overlay = $cta_slide['image_overlay'];
                $content = $cta_slide['content'];
                $content_alignment = $cta_slide['content_alignment'];

                $bg_color = null;
                if ($bg_type == "color") {
                    $bg_img = null;
                    $bg_color = "primary-color";
                    $disable_overlay = true;
                }
                ?>

                <div class="cta-slide color <?php echo $bg_color; ?> content-<?php echo $content_alignment ?>" style="background-image: url(<?php echo $bg_img ?>);">
                    <div class="wrap">
                        <div class="cta-content">
                            <?php if ($disable_overlay && $bg_type == "image") : ?>
                                <div class="cta-content-wrap">
                            <?php endif; ?>
                                <?php if (! empty($content)) : ?>
                                    <?php echo $content ?>
                                <?php endif; ?>
                            <?php if ($disable_overlay && $bg_type == "image") : ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if (! $disable_overlay) : ?>
                        <div class="cta-overlay"></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php ns_slider_arrows(32) ?>
    </div>
</section>
