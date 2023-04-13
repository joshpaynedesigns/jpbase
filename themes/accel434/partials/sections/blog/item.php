<?php if ( $loop->have_posts() ): ?>
    <section class="page-flexible-section page-section-stories <?php echo $padding_classes; ?>">
    	<div class="wrap">
    		<?php obj_section_header($section_title); ?>
    		<div class="blog-feed-slider">
                <div class="blog-feed-slides">
                	<?php while ( $loop->have_posts() ): $loop->the_post(); ?>
                		<div class="story">
                            <?php
                            $thumbnail = get_the_post_thumbnail_url( $post->ID, 'medium' );
                            if ( empty( $thumbnail ) ) {
                                $default_img = ns_get_field( 'default_banner_image_blog', 'option' );
                                $thumbnail = $default_img['url'];
                            } ?>
                            <?php if ( ! empty( $thumbnail ) ) : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <div class="story-img" style="background-image: url(' <?php echo $thumbnail ?> ')"></div>
                                </a>
                            <?php endif; ?>

                            <div class="story-blurb">
                                <span class="story-date"><?php echo get_the_date(); ?></span>
                                <h6 class="story-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h6>
                                <?php /* <p class="story-cats"><?php echo custom_taxonomies_terms(); ?></p> */ ?>
                                <div class="story-blurb-content">
                                    <?php echo ns_get_short_description( get_the_ID(), 22 ); ?>
                                </div>
                                <a class="read-more" href="<?php the_permalink(); ?>">Read More</a>
                            </div>
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
            <div class="blog-feed-bottom">
                <span class="medium-gray-button small-button">
                    <a href="<?php echo $arch_link ?>">View All Posts</a>
                </span>
            </div>
        </div>
    </section>
<?php endif; ?>
