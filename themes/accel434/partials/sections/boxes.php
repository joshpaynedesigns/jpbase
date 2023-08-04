<?php

$background_color = get_sub_field('section_bg_color');
$section_title = get_sub_field('section_title');
$boxes = get_sub_field('boxes');

$section_classes = ns_decide_section_classes($background_color);
?>

<section class="boxes-section <?php echo $section_classes; ?>" >
    <div class="wrap">
        <?php ns_section_header($section_title, 'basemb text-center'); ?>

        <div class="boxes">
            <?php
            foreach ($boxes as $box) :
                $box_bg_color    = ns_key_value($box, 'box_bg_color');
                $icon    = ns_key_value($box, 'icon');
                $box_title    = ns_key_value($box, 'box_title');
                $box_text    = ns_key_value($box, 'box_text');
                $box_url    = ns_key_value($box, 'box_url');
                $show_button    = ns_key_value($box, 'show_button');
                $button_color    = ns_key_value($box, 'button_color');
                $dark_bg = ns_is_bg_dark($box_bg_color);

                $block_classes = "box bg-$box_bg_color";

                if ($show_button) {
                    $block_classes .= " has-button";
                }

                ?>
                <?php if (! empty($box_url)) : ?>
                    <a class="linked <?php echo $block_classes ?>" href="<?php echo $box_url['url']; ?>" target="<?php echo $box_url['target']; ?>">
                <?php else : ?>
                    <div class="box <?php echo $block_classes ?>">
                <?php endif; ?>

                    <div class="box-info relative">

                        <?php if (! empty($icon)) : ?>
                            <div class="icon-wrap basemb"><?php echo wp_get_attachment_image($icon['id'], 'large', false, array( 'class' => '' )); ?></div>
                        <?php endif; ?>

                        <h5 class="box-title mb0 <?php echo $dark_bg ? "text-white" : 'text-dark-gray' ?>"><?php echo $box_title; ?></h5>

                        <?php if (! empty($box_text)) : ?>
                            <p class="box-text mb0 basemt <?php echo $dark_bg ? "text-white" : 'text-dark-gray' ?>"><?php echo $box_text; ?></p>
                        <?php endif; ?>

                        <?php if (! empty($box_url) && $show_button) : ?>
                            <div class="button-wrap basemt">
                                <div class="<?php echo $button_color ?> small-button fake-button"><span><?php echo $box_url['title'] ?></span></div>
                            </div>
                        <?php endif; ?>

                    </div>

                <?php if (! empty($box_url)) : ?>
                    </a>
                <?php else : ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
