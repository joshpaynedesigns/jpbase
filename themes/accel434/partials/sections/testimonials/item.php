<?php

$args = array(
    'post_type' => 'testimonial',
    'posts_per_page' => -1,
    'orderby' => 'menu_order',
    'order'   => 'ASC',
);

$loop = new WP_Query($args);
?>

<?php if ($loop->have_posts()) : ?>
    <section class="pfsection testimonials-section <?php echo $padding_classes; ?>">
        <div class="wrap">
            <div class="testimonial-section-inner special-offset-border">
                <?php ns_section_header($section_title, 'text-center basemb'); ?>
                <div class="testimonials-slider-wrap ns-slider-arrows-wrap arrows-white">
                    <div class="testimonials-slider">
                        <?php while ($loop->have_posts()) :
                            $loop->the_post(); ?>
                            <div class="">
                                <div class="testimonial text-center text-white">
                                    <?php echo wp_trim_words(get_the_content(), 64, '...'); ?>
                                    <div class="testimonial-title smallmt font-bold f18">
                                        <span class=""><?php the_title(); ?></span>
                                        <?php if (! empty(get_field('testimonial_company'))) : ?>
                                            <span class=""> | <?php the_field('testimonial_company'); ?></span>
                                        <?php endif; ?>
                                        </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                    </div>
                    <?php ns_slider_arrows(32, 32); ?>
                </div>
                <?php if ($show_view_all_button) : ?>
                    <div class="testimonials-feed-bottom flex justify-center basemt">
                        <span class="white-button">
                            <a href="<?php echo $arch_link ?>">View All</a>
                        </span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
