<?php
$section_title = get_sub_field('section_title');
$section_button = get_sub_field('section_button');
$slider = get_sub_field('slider');
$source = get_sub_field('source');
$age_group = get_sub_field('age_group');
$category = get_sub_field('category');
$programs_to_display = get_sub_field('programs_to_display');

$the_programs = array();

$slider_class = 'one24grid';
if ($slider) {
    $slider_class = 'is-slider ns-slider-arrows-wrap';
}

if ('specific' === $source) {
    $the_programs = $programs_to_display;
}

if ('age' === $source && ! empty($age_group)) {
    $args = array(
        'numberposts' => -1,
        'orderby'     => 'title',
        'post_type'   => 'program',
        'post_status' => 'publish',
        'fields'      => 'ids',
        'tax_query'   => array(
            array(
                'taxonomy' => 'program-age-group',
                'field'    => 'term_id',
                'terms'    => $age_group,
            ),
        ),
    );

    $the_programs = get_posts($args);
}

if ('cat' === $source && ! empty($category)) {
    $args = array(
        'numberposts' => -1,
        'orderby'     => 'title',
        'post_type'   => 'program',
        'post_status' => 'publish',
        'fields'      => 'ids',
        'tax_query'   => array(
            array(
                'taxonomy' => 'program-cat',
                'field'    => 'term_id',
                'terms'    => $category,
            ),
        ),
    );

    $the_programs = get_posts($args);
}

if ('all' === $source) {
    $args = array(
        'numberposts' => -1,
        'orderby'     => 'title',
        'post_type'   => 'program',
        'post_status' => 'publish',
        'fields'      => 'ids',
    );

    $the_programs = get_posts($args);
}
?>

<?php if (! empty($the_programs)) : ?>
    <section class="programs-section pfsection <?php echo $padding_classes; ?>">
        <div class="wrap">

            <?php if (! empty($section_title)) : ?>
                <div class="section-header">
                    <h2 class=""><?php echo $section_title; ?></h2>
                    <?php if (! empty($section_button)) : ?>
                        <?php echo ns_link_button($section_button, 'medium-gray-button small-button'); ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="programs-wrap <?php echo $slider_class; ?>">
                <?php if ($slider) : ?>
                    <div class="slides-wrap">
                <?php endif; ?>
                <?php foreach ($the_programs as $program) : ?>
                    <?php ns_program_block($program); ?>
                <?php endforeach; ?>
                <?php if ($slider) : ?>
                    </div>
                    <?php ns_slider_arrows(32, 32); ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
