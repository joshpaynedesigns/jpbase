<?php
    $section_title = get_sub_field('section_title');
    $logos = get_sub_field('logos');

    $section_classes = ns_decide_section_classes();
?>

<section class="logos-section <?php echo $section_classes; ?>">
    <div class="wrap">
        <?php ns_section_header($section_title, 'basemb2 text-center'); ?>

        <div class="logos-slider ns-slider-arrows-wrap">
            <div class="logos-slides">
                <?php
                foreach ($logos as $logo) :
                    $logo_img  = $logo['logo'];
                    $logo_link = $logo['logo_link'];
                    ?>
                    <div class="logo-slide">
                        <div class="flex items-center justify-center">
                            <?php if (! empty($logo_link)) : ?>
                                <a class="logo-link" href="<?php echo $logo_link['url']; ?>" target="_blank">
                            <?php endif; ?>
                                <img class="logo" src="<?php echo $logo_img['url']; ?>" alt="<?php echo $logo_img['alt']; ?>" />
                            <?php if (! empty($logo_link)) : ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php ns_slider_arrows(32, 32); ?>
        </div>
    </div>
</section>
