<?php

add_filter ( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );
remove_action( 'genesis_loop', 'genesis_do_loop' );

// add_action( 'genesis_loop', 'objectiv_success_tax_filter' );
function objectiv_success_tax_filter() {
	objectiv_tax_filter( 'staff', 'staff-cat', true );
}

add_action( 'genesis_loop', 'objectiv_intro_text' );
function objectiv_intro_text() {
	$arch_cont = ns_get_field( 'archive_intro_text_staff', 'option' );
	?>
	<?php if ( ! empty( $arch_cont ) ) : ?>
		<section class="archIntroText lastMNone">
			<?php echo $arch_cont ?>
		</section>
	<?php endif; ?>
	<?php
}

add_action( 'genesis_loop', 'objectiv_staff_archive' );
function objectiv_staff_archive() {

	$terms = get_terms( array(
		'taxonomy' => 'staff-cat',
		'hide_empty' => true,
	) );

	if ( ! empty( $terms ) )  {
		echo "<div class='archive-grid'>";
		foreach ( $terms as $t ) {
			$id = $t->term_id;
			$name = $t->name;
			$color = ns_get_field( 'category_color', $t );
			$term_link = get_term_link( $id );

			$args = array(
				'numberposts' => -1,
				'offset' => 0,
				'post_type' => 'staff',
				'post_status' => 'publish',
				'suppress_filters' => true,
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

			$staffs = wp_get_recent_posts( $args );

			?>
			<?php if ( ! empty( $staffs ) ) : ?>
				<div class="staffTermSection">
					<?php if ( ! empty( $name ) ) : ?>
						<header class="staffTermSectionHeader">
							<h2 class="staffTermTitle"><?php echo $name ?></h2>
						</header>
					<?php endif; ?>
					<div class="staffTermStaffGrid">
						<?php foreach ( $staffs as $s ) : ?>
							<?php
							$s_id = $s['ID'];
							$thumb = get_the_post_thumbnail(
								$s_id,
								'medium',
								['class' => 'staffArchImg' ]
							);
							$name = $s['post_title'];
							$position = ns_get_field( 'position_title', $s_id );
							$has_content = $s['post_content'];
							$s_link = get_the_permalink( $s_id );
							?>
							<div class="staffArchBlock">
								<?php if ( !empty($has_content) ) : ?>
									<a href="<?php echo $s_link ?>"><?php echo $thumb; ?></a>
								<?php else : ?>
									<?php echo $thumb; ?>
								<?php endif; ?>
								<?php if ( ! empty( $has_content ) ) : ?>
									<h4 class="staffArchBlockName"><a href="<?php echo $s_link ?>"><?php echo $name ?></h4></a>
								<?php else: ?>
									<h4 class="staffArchBlockName"><?php echo $name ?></h4>
								<?php endif; ?>
								<?php if ( ! empty( $position ) ) : ?>
									<p class="staffArchBlockPosition"><?php echo $position ?></p>
								<?php endif; ?>
							</div>
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
