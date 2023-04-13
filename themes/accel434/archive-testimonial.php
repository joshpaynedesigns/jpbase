<?php

add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
remove_action( 'genesis_before_loop', 'genesis_do_date_archive_title' );

add_action( 'genesis_loop', 'ns_intro_text' );
function ns_intro_text() {
	$arch_cont = ns_get_field( 'archive_intro_text_testimonials', 'option' );
	?>
	<?php if ( $arch_cont ) : ?>
		<section class="archIntroText lastMNone">
			<?php echo $arch_cont; ?>
		</section>
	<?php endif; ?>
	<?php
}

// Lower Half or so of the testimonial page
function ns_testimonials_lower_archive() {
	 $args = array(
		 'posts_per_page' => -1,
		 'post_type'   => 'testimonial',
		 'post_status' => 'publish',
		 'order'       => 'ASC',
		 'orderby'     => 'title',
	 );

	 $testimonials_query = new WP_Query( $args );
		?>

		<section class="testimonial-arch-lower mt-8">
			<?php if ( $testimonials_query->have_posts() ) : ?>
				<div class="testimonial-archive-list">
					<?php while ( $testimonials_query->have_posts() ) : ?>
						<?php $testimonials_query->the_post(); ?>
						<div class="testimonial text-center text-white special-offset-border">
                            <?php echo wp_trim_words(get_the_content(), 64, '...'); ?>
                            <div class="testimonial-title smallmt font-bold f18">
                                <span class=""><?php the_title(); ?></span>
                                <?php if (! empty(get_field('testimonial_company'))) : ?>
                                    <span class=""> | <?php the_field('testimonial_company'); ?></span>
                                <?php endif; ?>
                                </div>
                        </div>
					<?php endwhile; ?>
				</div>
			<?php else : ?>
				Sorry, no matching testimonials.
			<?php endif; ?>
		</section>

	<?php

}

// Remove the loop and replace it with our own.
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'ns_testimonials_archive_custom_loop' );
function ns_testimonials_archive_custom_loop() {
	ns_testimonials_lower_archive();
}

genesis();
