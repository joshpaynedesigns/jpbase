<?php
$section_title = get_sub_field('section_title');
$section_sub_title = get_sub_field('sub_title');
$icon_blurbs = get_sub_field('icon_blurbs');
$open_in_new_tab = get_sub_field('link_behavior');
$button_details = get_sub_field('section_button');

$section_classes = ns_decide_section_classes();
?>

<section class="icon-blurb-section <?php echo $section_classes; ?>">
    <div class="wrap">
        <div class="icon-blurb-content">

            <div class="upper-content basemb2 fcmt0 lcmb0">
                <?php ns_section_header($section_title, 'text-center'); ?>

                <?php if (! empty($section_sub_title)) : ?>
                    <div class="section-sub-title text-center basemt"><?php echo $section_sub_title ?></div>
                <?php endif; ?>
            </div>

            <div class="icon-blurb-grid one23grid">
                <?php foreach ($icon_blurbs as $ib) :
                    $blurb_link = $ib['icon_blurb_url'] ?? false;
                    $icon = $ib['icon']['url'] ?? false;
                    $blurb_title = $ib['blurb_title'] ?? false;
                    $blurb = $ib['blurb'] ?? false;
                    $show_accordion = $ib['show_accordion'] ?? false;
                    $accordion_title = $ib['accordion_title'] ?? false;
                    $accordion_content = $ib['accordion_content'] ?? false;

                    ?>
                    <div class="blurb tac">
                        <div class="inner-blurb">
                            <?php if (! empty($icon)) : ?>
                                <div class="blurb-image-wrap">
                                    <img class="blurb-image" src="<?php echo $icon ?>" alt="<?php echo $blurb_title ?>">
                                </div>
                            <?php endif; ?>

                            <?php if (! empty($blurb_title)) : ?>
                                <h6 class="blurb-title"><?php echo $blurb_title ?></h6>
                            <?php endif; ?>

                            <?php if (! empty($blurb)) : ?>
                                <p class="blurb-text"><?php echo $blurb ?></p>
                            <?php endif; ?>

                            <?php if (! empty($blurb_link)) : ?>
                                <div class="blurb-link-wrap"><?php echo ns_link_button($blurb_link, 'blue-button small-button') ?></div>
                            <?php endif; ?>

                            <?php if ($show_accordion && ! empty($accordion_title) && ! empty($accordion_content)) : ?>
                                <div class="micro-accordion-wrap smallmt">
                                    <div class="micro-accordion-title fake-arrow-link justify-center"><?php echo $accordion_title ?></div>
                                    <div class="micro-accordion-content">
                                        <div class="micro-accordion-inner-content">
                                            <?php echo $accordion_content ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php endforeach; ?>
            </div>

            <?php if (! empty($button_details)) : ?>
                <div class="bottom-content basemt3 text-center">
                    <?php echo ns_link_button($button_details, 'primary-button') ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
