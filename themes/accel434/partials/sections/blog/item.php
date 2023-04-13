<?php if ($loop->have_posts()) : ?>
    <section class="pfsection page-section-stories <?php echo $padding_classes; ?>">
        <div class="wrap">
            <?php ns_section_header($section_title, 'basemb text-center'); ?>
            <div class="blog-feed-slider ns-slider-arrows-wrap">
                <div class="blog-feed-slides">
                    <?php
                    while ($loop->have_posts()) :
                        $loop->the_post();
                        $id = get_the_ID();
                        ns_blog_block($id);
                        ?>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                </div>
                <?php ns_slider_arrows(32, 32); ?>
            </div>
            <div class="blog-feed-bottom flex justify-center mt-8">
                <span class="green-button">
                    <a href="<?php echo $arch_link; ?>">View All Posts</a>
                </span>
            </div>
        </div>
    </section>
<?php endif; ?>
