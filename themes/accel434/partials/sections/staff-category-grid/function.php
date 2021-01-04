<?php

return (object) array(
	'acf_name'  => 'staff_category_grid',
	'options'   => (object) array(
	'func'      => function ($padding_classes = '') {
		$p_loc = FlexibleContentSectionUtility::getSectionsDirectory();
		$fcta_loc = "$p_loc/staff-category-grid";
		$item = "$fcta_loc/item.php";

		$section_title = get_sub_field('section_title');
		$staff_category = get_sub_field('staff_category');

		$args = array(
			'post_type'  => 'staff',
			'numberposts' => -1,
			'offset' => 0,
			'post_status' => 'publish',
			'suppress_filters' => true,
			'orderby'=> 'menu_order',
			'order' => 'ASC',
			'tax_query' => array(
				array(
					'taxonomy' => 'staff-cat',
					'field'    => 'term_id',
					'terms'    => $staff_category,
				),
			),
		);
		$staff_members = get_posts( $args );

		require($item);
	},
	'has_padding'   => true
	)
);
