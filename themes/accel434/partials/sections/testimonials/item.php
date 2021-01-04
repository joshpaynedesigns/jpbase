<?php

/**
 * Filter the except length to 20 characters.
 *
 * @param int $length Excerpt length.
 * @return int (Maybe) modified excerpt length.
 */
function testimonial_custom_excerpt_length( $length ) {
    return 100;
}

add_filter( 'excerpt_length', 'testimonial_custom_excerpt_length', 999 );

$args = array(
	'post_type' => 'testimonial',
	'posts_per_page' => -1,
    'orderby' => 'menu_order',
    'order'   => 'ASC',
);

$loop = new WP_Query( $args );
?>

<?php if ( $loop->have_posts() ): ?>
    <section class="testimonials-section page-flexible-section <?php echo $padding_classes; ?>">
    	<div class="wrap">
            <?php obj_section_header($section_title); ?>
            <div class="testimonials-slider-wrap">
        		<div class="testimonials-slider">
                	<?php while ( $loop->have_posts() ): $loop->the_post(); ?>
                		<div class="testimonial">
                            <?php echo wp_trim_words( get_the_content(), 40, '' ); ?>
                            <p class="testimonial-title"><b><?php the_title(); ?></b><?php if(!empty(get_field('testimonial_company'))): ?> | <?php the_field('testimonial_company'); ?><?php endif;?></p>
                		</div>
                	<?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
                </div>
                <div class="left-arrow">
                    <?php echo get_svg_icon( 'arrow-white', '', 22, 22 ); ?>
                </div>
                <div class="right-arrow">
                    <?php echo get_svg_icon( 'arrow-white', '', 22, 22 ); ?>
                </div>
	        </div>
            <?php if($show_view_all_button): ?>
                <div class="testimonials-feed-bottom">
                    <span class="medium-gray-button small-button">
                        <a href="<?php echo $arch_link ?>">View All</a>
                    </span>
                </div>
            <?php endif; ?>
	    </div>
	</section>
<?php endif; ?>