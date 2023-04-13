<?php

add_filter ( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
remove_action( 'genesis_before_loop', 'genesis_do_date_archive_title' );

add_filter( 'genesis_post_info', 'sp_post_info_filter' );
function sp_post_info_filter($post_info) {
	$post_info = '[post_date]';
	return $post_info;
}

add_action( 'genesis_loop', 'ns_intro_text' );
function ns_intro_text() {
	$arch_cont = ns_get_field( 'archive_intro_text_testimonials', 'option' );
	?>
	<?php if ( ! empty( $arch_cont ) ) : ?>
		<section class="archIntroText lastMNone">
			<?php echo $arch_cont ?>
		</section>
	<?php endif; ?>
	<?php
}

// Remove the loop and replace it with our own.
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'ns_testimonial_archive_custom_loop' );
function ns_testimonial_archive_custom_loop( ) {

	// get the pest types
	$args = array(
	    'post_type'  => 'testimonial',
		'numberposts' => -1,
		'post_status' => 'publish',
		'orderby' => 'menu_order',
    	'order'   => 'ASC',
	);
	$testimonials = get_posts( $args );

	if ( ! empty( $testimonials ) ) {
		foreach ( $testimonials as $t ) {
			$test_company = ns_get_field( 'testimonial_company', $t->ID );
			?>
			<div class="testimonial-block">
				<div class="entry-content">
					<?php echo $t->post_content ?>
				</div>
				<h5 class="entry-title">
					- <?php echo $t->post_title ?>
					<?php if ( ! empty( $test_company ) ) : ?>
						/ <?php echo $test_company ?>
					<?php endif; ?>
				</h5>
			</div>
			<?php
		}
	}
}

//* Customize the post info function
add_filter( 'genesis_post_info', 'ns_post_info_filter' );
function ns_post_info_filter($post_info) {
	$post_info = '';
	return $post_info;
}

genesis();
