<?php

return (object) array(
	'acf_name'  => 'testimonials_section',
	'options'   => (object) array(
	'func'      => function ($padding_classes = '') {
		$p_loc = FlexibleContentSectionUtility::getSectionsDirectory();
		$fcta_loc = "$p_loc/testimonials";
		$item = "$fcta_loc/item.php";

		$section_title = get_sub_field('section_title');
		$show_view_all_button = get_sub_field('show_view_all_button');
		$arch_link = get_post_type_archive_link( 'testimonial' );

		require($item);
	},
	'has_padding'   => false
	)
);