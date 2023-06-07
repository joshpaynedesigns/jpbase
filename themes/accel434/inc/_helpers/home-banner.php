<?php

function ns_do_home_banner($custom_height = null, $banner_height = null, $banner_panels = null, $h1 = true)
{

    $default_banner_height = ns_get_field('default_banner_height', 'option');

    // Set up the banner height class
    $height_class = '';
    if (!empty($banner_height) && $custom_height) {
        $height_class = $banner_height . '-height-banner';
    } elseif (!empty($default_banner_height) && !$custom_height) {
        $height_class = $default_banner_height . '-height-banner';
    } else {
        $height_class = 'medium-height-banner';
    }

    // If we have banner panels set lets go ahead and display them
    if (!empty($banner_panels)) { ?>
        <?php
            $slide_num      = count($banner_panels);
            $display_arrows = false;

        if ($slide_num > 1) {
            $display_arrows = true;
        }
        ?>

        <section class="banner-slider-outer ns-slider-arrows-wrap">
            <div class="banner-slider actual-slider <?php echo $height_class; ?>">
                <?php
                foreach ($banner_panels as $bp) {
                    $type                 = ns_key_value($bp, 'banner_slide_type');
                    $txt_color            = ns_key_value($bp, 'text_color');
                    $overlay              = ns_key_value($bp, 'overlay');
                    $title                = ns_key_value($bp, 'banner_title');
                    $subtitle             = ns_key_value($bp, 'banner_sub_title');
                    $first_button         = ns_key_value($bp, 'first_button');
                    $second_button        = ns_key_value($bp, 'second_button');
                    $bg_image             = ns_key_value($bp, 'banner_background_image');
                    $webm_vid             = ns_key_value($bp, 'webm_video_file');
                    $mp4_vid              = ns_key_value($bp, 'mp4_video_file');
                    $og_vid               = ns_key_value($bp, 'ogg_video_file');
                    $use_default_bg_image = ns_key_value($bp, 'use_default_bg_image');

                    ns_banner_display_slide($type, $txt_color, $overlay, $title, $subtitle, $first_button, $use_default_bg_image, $bg_image, $webm_vid, $mp4_vid, $og_vid, $display_arrows, $h1, $second_button);
                }
                ?>
            </div>
            <?php if ($display_arrows) : ?>
                <?php ns_slider_arrows(32, 32, $txt_color); ?>
            <?php endif; ?>
        </section>
        <?php
    } else {
        ?>
        <section class="banner-slider-outer">
            <div class="banner-slider actual-slider <?php echo $height_class; ?>">
                <?php
                $type                 = 'simple';
                $txt_color            = 'light-text';
                $overlay              = 'dark-overlay';
                $title                = get_the_title();
                $use_default_bg_image = false;
                $display_arrows       = false;
                $first_button          = null;
                $bg_image             = null;
                $webm_vid             = null;
                $mp4_vid              = null;
                $og_vid               = null;
                $subtitle             = null;
                $second_button        = false;

                ns_banner_display_slide($type, $txt_color, $overlay, $title, $subtitle, $first_button, $use_default_bg_image, $bg_image, $webm_vid, $mp4_vid, $og_vid, $display_arrows, $h1, $second_button);
                ?>
            </div>
        </section>
        <?php
    }
}

// Function to output a single slide
function ns_banner_display_slide($type, $txt_color, $overlay, $title, $subtitle, $first_button, $use_default_bg_image, $incoming_bg_image, $webm_vid, $mp4_vid, $og_vid, $display_arrows, $h1, $second_button)
{
    $bg_image_url = ns_key_value($incoming_bg_image, 'url');

    if ($use_default_bg_image || empty($bg_image_url)) {
        $default_bg_image = ns_get_field('default_banner_image', 'options');
        $bg_image_url = ns_key_value($default_bg_image, 'url');
    }

    ?>

    <?php if ($type === 'simple') : ?>
        <div class="banner_slide <?php echo $txt_color; ?>" style="background-image: url(<?php echo $bg_image_url; ?>)">
            <?php display_slide_content($title, $subtitle, $first_button, $overlay, $display_arrows, $h1, $second_button); ?>
        </div>

    <?php elseif ($type === 'video') : ?>
        <div class="banner_slide <?php echo $txt_color; ?> video-slide" style="background-image: url(<?php echo $bg_image_url; ?>)">

            <?php if (!empty($webm_vid) || !empty($mp4_vid || !empty($ogg_video_file))) : ?>
                <video muted loop autoplay id="cta-slide-video" poster="<?php echo $bg_image_url; ?>">
                    <?php if (!empty($webm_vid)) : ?>
                        <source src="<?php echo $webm_vid; ?>" type="video/webm">
                    <?php endif; ?>
                    <?php if (!empty($og_vid)) : ?>
                        <source src="<?php echo $og_vid; ?>" type="video/ogg">
                    <?php endif; ?>
                    <?php if (!empty($mp4_vid)) : ?>
                        <source src="<?php echo $mp4_vid; ?>" type="video/mp4">
                    <?php endif; ?>
                </video>
            <?php endif; ?>

            <?php display_slide_content($title, $subtitle, $first_button, $overlay, $display_arrows, $h1, $second_button); ?>

        </div>
    <?php endif; ?>
    <?php
}

function display_slide_content($title = null, $subtitle = null, $first_button = null, $overlay = null, $display_arrows = null, $h1 = true, $second_button = false, $image = null)
{
    $image_id = ns_key_value($image, 'id');
    ?>
    <div class="wrap">
        <div class="banner_content">
            <?php if (!empty($image_id)) : ?>
                <div class="banner_image">
                    <?php echo wp_get_attachment_image($image_id, 'large', false, array('class' => '')); ?>
                </div>
            <?php endif; ?>
            <div class="">
                <?php if ($h1) : ?>
                    <h1 class="banner_title"><?php echo $title; ?></h1>
                <?php else : ?>
                    <h2 class="banner_title"><?php echo $title; ?></h2>
                <?php endif; ?>
                <?php if (!empty($subtitle)) : ?>
                    <div class="banner_subtitle max-width-760 mx-auto leading-normal"><?php echo $subtitle; ?></div>
                <?php endif; ?>
                <?php if (!empty($first_button) || !empty($second_button)) : ?>
                    <div class="flex button-wrap flex-wrap">
                        <?php if (!empty($first_button)) : ?>
                            <?php echo ns_link_button($first_button, 'blue-button'); ?>
                        <?php endif; ?>
                        <?php if (!empty($second_button)) : ?>
                            <?php echo ns_link_button($second_button, 'blue-button'); ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if ($overlay != 'none') : ?>
        <div class="overlay <?php echo $overlay; ?>"></div>
    <?php endif; ?>

    <?php
}

// Displays the slider arrows
function ns_slider_arrows($height = 22, $width = 22, $classes = "")
{
    ?>
    <div class="arrows-wrap <?php echo $classes ?>">
        <div class="left-arrow">
            <?php echo get_svg_icon('arrow-white', '', $height, $width); ?>
        </div>
        <div class="right-arrow">
            <?php echo get_svg_icon('arrow-white', '', $height, $width); ?>
        </div>
    </div>
    <?php
}
