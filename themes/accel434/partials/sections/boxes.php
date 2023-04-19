<?php

$section_title = get_sub_field('section_title');
$boxes = get_sub_field('boxes');

$section_classes = ns_decide_section_classes();
?>

<section class="boxes-section <?php echo $section_classes; ?>" >
    <div class="wrap">
        <?php ns_section_header($section_title, 'basemb text-center'); ?>

        <div class="boxes">
            <?php
            foreach ($boxes as $box) :
                $background_color    = ns_key_value($box, 'background_color');
                $box_type    = ns_key_value($box, 'box_type');
                $title_color    = ns_key_value($box, 'title_color');
                $text_color    = ns_key_value($box, 'text_color');
                $box_title    = ns_key_value($box, 'box_title');
                $box_text    = ns_key_value($box, 'box_text');
                $box_url    = ns_key_value($box, 'box_url');
                $icon    = ns_key_value($box, 'icon');
                $button    = ns_key_value($box, 'button');
                $button_color    = ns_key_value($box, 'button_color');
                $cta_text    = ns_key_value($box, 'cta_text');

                $is_normal_block = false;
                $is_icon_block = false;
                $is_link_block = false;
                if ('normal' === $box_type) {
                    $is_normal_block = true;
                } elseif ('linked' === $box_type) {
                    $is_link_block = true;
                }

                $block_classes = "box bg-$background_color box-type-$box_type";

                ?>
                <?php if (! empty($box_url) && $is_link_block) : ?>
                    <a class="linked <?php echo $block_classes ?>" href="<?php echo $box_url['url']; ?>" target="<?php echo $box_url['target']; ?>">
                <?php else : ?>
                    <div class="box <?php echo $block_classes ?>">
                <?php endif; ?>

                    <div class="box-info relative">

                        <?php if (! empty($icon)) : ?>
                            <div class="icon-wrap basemb"><?php echo wp_get_attachment_image($icon['id'], 'large', false, array( 'class' => '' )); ?></div>
                        <?php endif; ?>

                        <h5 class="box-title mb0 text-<?php echo $title_color ?>"><?php echo $box_title; ?></h5>

                        <?php if (! empty($box_text)) : ?>
                            <p class="box-text mb0 basemt text-<?php echo $text_color ?>"><?php echo $box_text; ?></p>
                        <?php endif; ?>

                        <?php if (! empty($button) && $is_normal_block) : ?>
                            <div class="button-wrap basemt">
                                <?php echo ns_link_button($button, $button_color . '-button') ?>
                                <?php if (! empty($cta_text)) : ?>
                                    <div class="text-<?php echo $text_color ?>"><?php echo $cta_text ?></div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                    </div>

                <?php if (! empty($box_url) && $is_link_block) : ?>
                    </a>
                <?php else : ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
