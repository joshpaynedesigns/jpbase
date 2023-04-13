<?php

// * Force content-sidebar layout
add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );

add_action( 'ns_page_content', 'service_single' );
function service_single() {
	?>
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
