<?php
add_action( 'genesis_after_header', 'objectiv_page_banner', 10 );

function objectiv_page_banner() {
	$hide_banner_options = ns_get_field('hide_banner_options');

	if ( is_front_page() ) {
		echo get_template_part( 'partials/banner/home', 'banner' );
	} elseif ( class_exists( 'WooCommerce' ) ) {
		if ( !mktg434_is_woo()) {
			echo get_template_part( 'partials/banner/hero', 'banner' );
		}
	} elseif ($hide_banner_options != 'hide_banner' && !is_singular( 'location' )) {
	  echo get_template_part( 'partials/banner/hero', 'banner' );
	}
	do_action( 'mktg434_after_banner' );
}
