<?php
$section_blurb = get_sub_field('section_blurb');
$announcements_box_title = get_sub_field('announcements_box_title');
$announcements = get_sub_field('announcements');

if (! empty($announcements) && is_array($announcements)) {
    $new_announcements = array();

    $current_count = 1;
    foreach ($announcements as $key => $value) {
        $even = $key % 2 === 0;

        $new_announcements[$current_count][] = $value;

        if (! $even) {
            $current_count++;
        }
    }

    $announcements = $new_announcements;
}
?>

<?php if (! empty($announcements)) : ?>
    <section class="announcements-section relative">
        <div class="bg-stripe"></div>
        <div class="wrap relative">
            <div class="first">
                <?php if (! empty($section_blurb)) : ?>
                    <div class="fcmt0 lcmb0"><?php echo $section_blurb; ?></div>
                <?php endif; ?>
            </div>
            <div class="second">
                <?php if (! empty($announcements_box_title)) : ?>
                    <h2 class="mb0"><?php echo $announcements_box_title; ?></h2>
                <?php endif; ?>
                <div class="announcements-slider ns-slider-arrows-wrap">
                    <div class="slides-wrap">
                        <?php foreach ($announcements as $key => $a) : ?>
                            <div class="slide">
                                <?php foreach ($a as $announcement) : ?>
                                    <?php
                                        $title = ns_key_value($announcement, 'title');
                                        $blurb = ns_key_value($announcement, 'blurb');
                                    ?>
                                    <div class="slide-content">
                                        <?php if ($title) : ?>
                                            <h4 class=""><?php echo $title ?></h4>
                                        <?php endif; ?>
                                        <?php if ($blurb) : ?>
                                            <div class=""><?php echo $blurb ?></div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php ns_slider_arrows(32, 32); ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
