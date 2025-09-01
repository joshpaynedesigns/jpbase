<?php

$section_title = get_sub_field('section_title');
$category_filter = get_sub_field('category_filter');
$show_view_all_button = get_sub_field('show_view_all_button');
$arch_link = get_post_type_archive_link('testimonial');

if (!empty($category_filter)) {
    $args = array(
        'post_type' => 'testimonial',
        'posts_per_page' => -1,
        'orderby' => 'menu_order',
        'order'   => 'ASC',
        'tax_query' => array(
            array(
                'taxonomy' => 'testimonial-cat',
                'field' => 'term_id',
                'terms' => $category_filter->term_id,
            ),
        )
    );
} else {
    $args = array(
        'post_type' => 'testimonial',
        'posts_per_page' => -1,
        'orderby' => 'menu_order',
        'order'   => 'ASC',
    );
}

$loop = new WP_Query($args);

$section_classes = ns_decide_section_classes('blue');
?>

<?php if ($loop->have_posts()) : ?>
    <section class="testimonials-section <?php echo $section_classes ?>">
        <div class="wrap">
            <div class="testimonial-section-inner">
                <?php ns_section_header($section_title, 'text-center basemb'); ?>
                <div class="testimonials-slider-wrap ns-slider-arrows-wrap arrows-white">
                    <div class="testimonials-slider">
                        <?php while ($loop->have_posts()) :
                            $loop->the_post(); 
                            $testimonial_company = ns_get_field('testimonial_company', get_the_ID());
                            ?>
                            <div class="">
                                <div class="testimonial text-center text-white">
                                    <?php echo wp_trim_words(get_the_content(), 64, '...'); ?>
                                    <div class="testimonial-title smallmt font-bold f18">
                                        <span class=""><?php the_title(); ?></span>
                                            <?php if (! empty($testimonial_company)) : ?>
                                        <span class=""> | <?php echo $testimonial_company ?></span>
                                    <?php endif; ?>
                                        </div>
                                </div>
                            </div>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                    <?php ns_slider_arrows(32); ?>
                </div>
                <?php if ($show_view_all_button) : ?>
                    <div class="testimonials-feed-bottom flex justify-center basemt">
                        <span class="white-button">
                            <a href="<?php echo $arch_link ?>">View All Testimonials</a>
                        </span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
