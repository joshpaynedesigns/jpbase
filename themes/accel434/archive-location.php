<?php

add_filter('genesis_pre_get_option_site_layout', '__genesis_return_full_width_content');
remove_action('genesis_entry_footer', 'genesis_post_meta');
remove_action('genesis_entry_footer', 'genesis_post_meta');
remove_action('genesis_before_loop', 'genesis_do_date_archive_title');

add_action( 'genesis_loop', 'ns_intro_text' );
function ns_intro_text() {
    $arch_cont = ns_get_field( 'archive_intro_text_locations', 'option' );
    ?>
    <?php if ( ! empty( $arch_cont ) ) : ?>
        <section class="archIntroText lastMNone">
            <?php echo $arch_cont ?>
        </section>
    <?php endif; ?>
    <?php
}

// Upper Half or so of the location page
function ns_locations_upper_archive()
{
    ?>

	<section class="location-arch-upper">
        <?php echo do_shortcode('[ns_google_maps]'); ?>
	</section>

	<?php

}

// Lower Half or so of the location page
function ns_locations_lower_archive()
{
    $args = array(
	  'numberposts' => -1,
	  'post_type' => 'location',
	  'post_status' => 'publish',
      'order'=> 'ASC',
      'orderby' => 'title'
	);

    $locations = get_posts($args);
	?>

	<?php if ( ! empty( $locations ) ) : ?>
		<section class="location-arch-lower">
			<header class="locationArchHeader">
			</header>
			<div class="locationArchGrid">
				<?php foreach ( $locations as $l ) : ?>
					<?php
						$id = $l->ID;
						$title = $l->post_title;
						$link = get_the_permalink( $id );
                        $phone = ns_get_field( 'loc_phone_number', $id );
                        $fax = ns_get_field( 'loc_fax_number', $id );
                        $image = ns_get_field( 'location_image', $id );
                        $ad1 = ns_get_field('loc_address_line_1', $id);
                        $ad2 = ns_get_field('loc_address_line_2', $id);
					?>
                    <div class="locationBlock">
                        <a href="<?php echo $link ?>" class="locationImage" style="background-image: url('<?php echo $image['url']; ?>')"></a>
                        <h3 class="locationBlockTitle"><a href="<?php echo $link ?>"><?php echo $title ?></a></h3>
                        <div class="locationBlockAddress">
                            <?php echo $ad1; ?>, <?php echo $ad2; ?>
                        </div>
                        <?php if ( ! empty( $phone ) ) : ?>
                            <div class="locationBlockDetail">
                                <b>Phone:</b> <?php echo $phone ?>
                            </div>
                        <?php endif; ?>
                        <?php if ( ! empty( $fax ) ) : ?>
                            <div class="locationBlockDetail">
                                <b>Fax:</b> <?php echo $fax ?>
                            </div>
                        <?php endif; ?>
                        <a class="locationBlockLink" href="<?php echo $link ?>">View Location</a>
                    </div>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php

}

// Remove the loop and replace it with our own.
remove_action('genesis_loop', 'genesis_do_loop');
add_action('genesis_loop', 'cgd_locations_archive_custom_loop');
function cgd_locations_archive_custom_loop()
{
    ns_locations_upper_archive();
    ns_locations_lower_archive();
}

genesis();
