<?php
$section_title = get_sub_field('section_title');
$staff_category = get_sub_field('staff_category');

$args = array(
    'post_type'  => 'staff',
    'numberposts' => -1,
    'offset' => 0,
    'post_status' => 'publish',
    'suppress_filters' => true,
    'orderby'=> 'menu_order',
    'order' => 'ASC',
    'tax_query' => array(
        array(
            'taxonomy' => 'staff-cat',
            'field'    => 'term_id',
            'terms'    => $staff_category,
        ),
    ),
);
$staff_members = get_posts($args);

$section_classes = ns_decide_section_classes();
?>

<?php if (! empty($staff_members)) : ?>
    <section class="pfsection staff-cat-grid <?php echo $section_classes; ?>">
        <div class="wrap">
            <?php ns_section_header($section_title, 'basemb2 text-center'); ?>
            <div class="staffTermStaffGrid one24grid">
                <?php foreach ($staff_members as $s) : ?>
                    <?php ns_staff_block($s) ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
