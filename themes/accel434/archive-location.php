<?php

add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
remove_action( 'genesis_before_loop', 'genesis_do_date_archive_title' );

add_action( 'genesis_loop', 'ns_intro_text' );
function ns_intro_text() {
	$arch_cont = ns_get_field( 'archive_intro_text_locations', 'option' );
	?>
	<?php if ( $arch_cont ) : ?>
		<section class="archIntroText lastMNone">
			<?php echo $arch_cont; ?>
		</section>
	<?php endif; ?>
	<?php
}

// Upper Half or so of the location page
function ns_locations_upper_archive() {
	?>

	<section class="location-arch-upper sectionmt">
		<div class="filter-ribbon">
            <div class="upper">
                <?php echo do_shortcode( '[facetwp facet="location_services"]' ); ?>
                <?php echo do_shortcode( '[facetwp facet="location_proximity"]' ); ?>
                <?php echo do_shortcode( '[facetwp facet="search"]' ); ?>
                <?php echo do_shortcode( '[facetwp facet="reset"]' ); ?>
            </div>
            <div class="results">
                <?php echo do_shortcode( '[facetwp selections="true"]' ); ?>
            </div>
		</div>
		<div class="location-map-wrap basemt">
			<?php echo do_shortcode( '[facetwp facet="location_map"]' ); ?>
		</div>
	</section>

	<?php

}

// Lower Half or so of the location page
function ns_locations_lower_archive() {
	 $args = array(
		 'posts_per_page' => -1,
		 'post_type'   => 'location',
		 'post_status' => 'publish',
		 'order'       => 'ASC',
		 'orderby'     => 'title',
		 'facetwp'     => true,
	 );

	 $locations_query = new WP_Query( $args );
		?>

		<section class="location-arch-lower facetwp-template">
			<?php if ( $locations_query->have_posts() ) : ?>
				<div class="locations-list one2grid">
					<?php while ( $locations_query->have_posts() ) : ?>
						<?php $locations_query->the_post(); ?>
						<?php ns_locations_archive_location( get_the_ID() ); ?>
					<?php endwhile; ?>
				</div>
			<?php else : ?>
				Sorry, no matching locations.
			<?php endif; ?>
		</section>

	<?php

	wp_reset_postdata();

}

// Remove the loop and replace it with our own.
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'ns_locations_archive_content' );
function ns_locations_archive_content() {
	ns_locations_upper_archive();
	ns_locations_lower_archive();
}

genesis();
