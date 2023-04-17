<section class="boxes-section pfsection <?php echo $padding_classes; ?> background-<?php echo $bg_color ?>">
    <?php if ('wave' === $bg_color) : ?>
        <div class="wave-bg-wrap">
            <svg viewBox="0 0 1416 368" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1416 368V75.724S1049-86.5429 680 63.9939C311 214.531 0 177.385 0 177.385V368h1416Z" fill="url(#a)"/>
                <defs>
                    <linearGradient id="a" x1="708" y1="368" x2="708" y2="2.41068" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#1D6665"/>
                    <stop offset="1" stop-color="#01569F"/>
                    </linearGradient>
                </defs>
            </svg>
        </div>
    <?php endif; ?>
    <div class="wrap">
        <?php ns_section_header($section_title, 'basemb text-center', 'green-accent'); ?>

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
