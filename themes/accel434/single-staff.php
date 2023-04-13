<?php

//* Force content-sidebar layout
add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );

// Remove the entry header
remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 15 );
remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );

add_action( 'genesis_before_entry', 'ns_staff_header_content', 1 );
function ns_staff_header_content() {
    $s_id = get_the_ID();
    $thumb = get_the_post_thumbnail(
        $s_id,
        'medium',
        ['class' => 'singleStaffImage' ]
    );
    $position = ns_get_field( 'position_title', $s_id );
    $staff_email = ns_get_field( 'email_address', $s_id );
    $staff_office = ns_get_field( 'office_phone', $s_id );
    $staff_cell = ns_get_field( 'cell_phone', $s_id );
    $office_phone_numbers = preg_replace( '/[^0-9]/', '', $staff_office );
    $cell_phone_numbers = preg_replace( '/[^0-9]/', '', $staff_cell );


    if ( ! empty( $s_id ) ) { ?>
        <section class="singleStaffDetails">
            <div class="singleStaffDetailsInner">
                <?php if ( ! empty( $thumb ) ) : ?>
                    <div class="singleStaffThumb">
                        <?php echo $thumb ?>
                    </div>
                <?php endif; ?>
                <div class="singleStaffName">
                    <h1 class="post-title"><?php echo get_the_title(); ?></h1>
                </div>
                <?php if ( ! empty( $position ) ) : ?>
                    <div class="singleStaffPosition">
                        <h6><?php echo $position ?></h6>
                    </div>
                <?php endif; ?>
                <div class="singleStaffContact">
                    <?php if ( ! empty( $staff_email ) ) : ?>
                        <div class="staffEmail"><?php echo ns_hide_email( $staff_email ); ?></div>
                    <?php endif; ?>
                    <?php if ( ! empty( $staff_office ) ) : ?>
                        <div class="staffPhone"><span class="uppercase">Office:</span> <a href="tel:<?php echo $office_phone_numbers ?>"><?php echo $staff_office ?></a></div>
                    <?php endif; ?>
                    <?php if ( ! empty( $staff_cell ) ) : ?>
                        <div class="staffPhone"><span class="uppercase">Cell:</span> <a href="tel:<?php echo $cell_phone_numbers ?>"><?php echo $staff_cell ?></a></div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <?php
    }
}

genesis();
