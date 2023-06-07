<?php
$section_title = get_sub_field('title');
$posts_per_feed = get_sub_field('posts_per_feed');
$blog_category = get_sub_field('blog_category');

if (!empty($blog_category)) {
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => $posts_per_feed,
        'tax_query' => array(
            array(
                'taxonomy' => 'category',
                'field' => 'term_id',
                'terms' => $blog_category,
            ),
        )
    );
    $arch_link = get_term_link($blog_category);
} else {
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => $posts_per_feed
    );
    $arch_link = get_post_type_archive_link('post');
}

$loop = new WP_Query($args);

$section_classes = ns_decide_section_classes();
?>

<?php if ($loop->have_posts()) : ?>
    <section class="page-section-stories <?php echo $section_classes; ?>">
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
                <span class="blue-button">
                    <a href="<?php echo $arch_link; ?>">View All Posts</a>
                </span>
            </div>
        </div>
    </section>
<?php endif; ?>
