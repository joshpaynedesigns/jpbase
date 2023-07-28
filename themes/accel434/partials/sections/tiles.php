<?php
$section_title = get_sub_field('section_title');
$page_tiles = get_sub_field('page_tiles');
$tiles_count = count($page_tiles);

$margin_classes = "sectionmt";
if (empty($section_title)) {
    $margin_classes = "";
}

// $section_classes = ns_decide_section_classes();
?>

<section class="tiles <?php echo $margin_classes; ?>">
    <?php ns_section_header($section_title, 'basemb text-center'); ?>
    <?php if (have_rows('page_tiles')) : ?>
        <div class="tile-blocks tiles-<?php echo $tiles_count; ?>">
            <?php
            while (have_rows('page_tiles')) :
                the_row();
                ?>
                <?php
                // Set all the variables
                $bg_options = get_sub_field('background_options');
                $content    = get_sub_field('content');
                $bg         = get_sub_field('background_image');
                $bg_color   = get_sub_field('background_color');

                $bg_class    = '';
                $solid_class = '';
                $solid_color = '';
                $text_class  = '';

                // If set to be image set up the image and text classes

                if ($bg_options == 'image') {
                    $bg_class   = 'has-image';
                    $text_class = 'text-is-light';
                } elseif ($bg_options == 'solid') {
                    $bg_class    = 'solid';
                    $solid_color = $bg_color;

                    if ('primary' === $bg_color || 'dark-gray' === $bg_color) {
                        $text_class = 'text-is-light';
                    }
                    if ('white' === $bg_color || 'light-gray' === $bg_color) {
                        $text_class = 'text-is-dark';
                    }
                }

                // If set to be solid set up the bg class

                // Check to see if we are automatically pulling the bg image
                if (! empty($bg) && $bg_options == 'image') {
                    $bg_url     = $bg['url'];
                    $bg_image = "style='background-image: url($bg_url);'";
                } else {
                    $bg_image = '';
                }

                ?>
                <?php if (! empty($content)) : ?>
                    <div class="tile-block <?php echo $bg_class; ?> <?php echo $solid_color; ?>" <?php echo $bg_image; ?>>
                        <div class="tile-block-excerpt fcmt0 lcmb0 <?php echo $text_class; ?>">
                            <?php echo $content; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</section>
