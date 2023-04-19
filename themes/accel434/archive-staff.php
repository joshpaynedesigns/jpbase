<?php

add_filter ( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );
remove_action( 'genesis_loop', 'genesis_do_loop' );

// add_action( 'genesis_loop', 'ns_success_tax_filter' );
function ns_success_tax_filter() {
	ns_tax_filter( 'staff', 'staff-cat', true );
}

add_action( 'genesis_loop', 'ns_intro_text' );
function ns_intro_text() {
	$arch_cont = ns_get_field( 'archive_intro_text_staff', 'option' );
	?>
	<?php if ( ! empty( $arch_cont ) ) : ?>
		<section class="archIntroText lastMNone">
			<?php echo $arch_cont ?>
		</section>
	<?php endif; ?>
	<?php
}

add_action( 'genesis_loop', 'ns_staff_archive' );
function ns_staff_archive() {

	$terms = get_terms( array(
		'taxonomy' => 'staff-cat',
		'hide_empty' => true,
	) );

	if ( ! empty( $terms ) )  {
		echo "<div class='archive-grid'>";
		foreach ( $terms as $t ) {
			$id = $t->term_id;
			$name = $t->name;

			$args = array(
				'numberposts' => -1,
				'post_type' => 'staff',
				'post_status' => 'publish',
				'orderby' => 'menu_order',
				'order'   => 'ASC',
				'tax_query' => array(
					array(
						'taxonomy' => 'staff-cat',
						'field' => 'term_id',
						'terms' => $id,
					),
				)
			);

			$staffs = get_posts( $args );

			?>
			<?php if ( ! empty( $staffs ) ) : ?>
				<div class="staffTermSection">
					<?php if ( ! empty( $name ) ) : ?>
						<header class="staffTermSectionHeader">
							<h2 class="staffTermTitle"><?php echo $name ?></h2>
						</header>
					<?php endif; ?>
					<div class="staffTermStaffGrid one24grid">
						<?php foreach ( $staffs as $s ) : ?>
							<?php ns_staff_block( $s ) ?>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>
			<?php
		}
		echo "</div>";
	}

	// get all terms with posts
	// go through terms and display those people in a blockx

}

genesis();
