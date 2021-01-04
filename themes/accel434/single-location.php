<?php

//* Force content-sidebar layout
add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );

// Remove the entry header
remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 15 );
remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_open', 5 );
remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_close', 15 );

add_action( 'genesis_before_entry', 'location_single', 1 );
function location_single() {
    $ad1 = get_field('loc_address_line_1');
    $ad2 = get_field('loc_address_line_2');
    $phone = get_field('loc_phone_number');
    $fax = get_field('loc_fax_number');
    $med_fax = get_field('medical_records_fax');
    $hours = get_field('location_hours');
    $img = get_field('location_image');
    $notes = get_field('notes');
    $content = get_field('location_content');
    ?>

    <section class="location-single">
        <div class="single-location-slider">
            <div class="slides">
                <div class="slide" style="background-image: url(<?php echo $img['url'] ?>)"></div>
            </div>
        </div>

        <?php if(!empty($content)): ?>
        <div class="details-content fifty-fifty">
        <?php else: ?>
        <div class="details-content">
        <?php endif; ?>
            <div class="wrap">
                <h1 class="post-title"><?php the_title(); ?></h1>
                <div class="location-details half">
                    <div class="address-div">
                        <h5 class="address"><?php echo $ad1; ?>, <?php echo $ad2; ?></h5>
                        <a class="get-direction" href="https://www.google.com/maps/place/<?php echo $ad1; ?>, <?php echo $ad2; ?>" target=_blank" class="address">Get Directions</a>
                    </div>
                    <?php if ( ! empty( $phone ) ) : ?>
                        <div class="phone loc-info">
                            <b>Phone:</b> <?php echo $phone ?>
                        </div>
                    <?php endif; ?>
                    <?php if ( ! empty( $fax ) ) : ?>
                        <div class="fax loc-info">
                            <b>Fax:</b> <?php echo $fax ?>
                        </div>
                    <?php endif; ?>
                    <?php if ( ! empty( $hours ) ) : ?>
                        <div class="hours loc-info">
                            <b>Hours:</b> <?php echo $hours ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if(!empty($content)): ?>
                    <div class="location-content half">
                            <?php echo $content ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php }

genesis();