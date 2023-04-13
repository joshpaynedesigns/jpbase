<?php

// * Force content-sidebar layout
add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );

add_action( 'ns_page_content', 'location_single' );
function location_single() {
	$ad1      = ns_get_field( 'loc_address_line_1' );
	$ad2      = ns_get_field( 'loc_address_line_2' );
	$address  = ns_get_location_full_address( get_the_ID() );
	$phone    = ns_get_field( 'loc_phone_number' );
	$fax      = ns_get_field( 'loc_fax_number' );
	$hours    = ns_get_field( 'location_hours' );
	$map_spot = ns_get_field( 'map_spot' );
	$services = ns_get_field( 'services' );
	?>

	<section class="location-single">
		<div class="wrap">
			<div class="sectionmt sectionmb">
				<h1 class="post-title"><?php the_title(); ?></h1>
				<div class="location-details basemt">
					<div class="details-upper">
						<?php if ( ! empty( $address ) ) : ?>
							<div class="address-div">
								<h5 class="">Address</h5>
								<div class="address">
                                    <?php if( ! empty( $ad1 ) ) : ?>
                                        <span class=""><?php echo $ad1 ?></span>
                                    <?php endif; ?>
                                    <?php if( ! empty( $ad2 ) ) : ?>
                                        <br>
                                        <span class=""><?php echo $ad2 ?></span>
                                    <?php endif; ?>
                                </div>
								<a class="get-direction" href="https://www.google.com/maps/place/<?php echo $ad1; ?>, <?php echo $ad2; ?>" target="_blank" class="address">Get Directions</a>
							</div>
						<?php endif; ?>
						<?php if ( $phone || $fax ) : ?>
							<div class="">
								<h5 class="">Contact</h5>
								<?php if ( ! empty( $phone ) ) : ?>
									<div class="phone loc-info">
										<b>Phone:</b> <?php echo $phone; ?>
									</div>
								<?php endif; ?>
								<?php if ( ! empty( $fax ) ) : ?>
									<div class="fax loc-info">
										<b>Fax:</b> <?php echo $fax; ?>
									</div>
								<?php endif; ?>
							</div>
						<?php endif; ?>
						<?php if ( ! empty( $hours ) ) : ?>
							<div class="hours loc-info">
								<h5>Hours</h5>
								<?php echo $hours; ?>
							</div>
						<?php endif; ?>
                        <?php if( ! empty( $services ) ) : ?>
                            <div class="">
                                <h5 class="">Services</h5>
                                <?php foreach ( $services as $service ) : ?>
                                    <a href="<?php echo get_the_permalink( $service->ID ) ?>" class=""><?php echo get_the_title( $service->ID ) ?></a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
					</div>
				</div>
				<?php if ( ! empty( $map_spot ) ) : ?>
					<div class="acf-map basemt" data-zoom="16">
						<div class="marker" data-lat="<?php echo esc_attr( $map_spot['lat'] ); ?>" data-lng="<?php echo esc_attr( $map_spot['lng'] ); ?>"></div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<section id="flexible-section-repeater">
		<?php
		$fcs = FlexibleContentSectionFactory::create( 'page_flexible_sections' );
		$fcs->run();
		?>
	</section>
	<?php
}

// Build the page
get_header();
do_action( 'ns_page_content' );
get_footer();
