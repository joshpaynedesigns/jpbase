<?php

return (object) array(
	'acf_name'  => 'projects_feed_section',
	'options'   => (object) array(
		'func'      => function ($padding_classes = '') {
			$p_loc = FlexibleContentSectionUtility::getSectionsDirectory();
			$pf_loc = "$p_loc/projects-feed";
			$item = "$pf_loc/item.php";

			$section_title = get_sub_field('section_title');
			$projects_per_feed = get_sub_field('projects_per_feed');
			$category_filter = get_sub_field( 'category_filter' );

			if ( !empty($category_filter) ) {
				$args = array(
					'post_type'  => 'projects',
				    'numberposts' => $projects_per_feed,
				    'order' => 'ASC',
				    'orderby' => 'menu_order',
				    'tax_query' => array(
						array(
							'taxonomy' => 'projects-cat',
							'field' => 'term_id',
							'terms' => $category_filter,
						),
					)
				);
				$arch_link = get_term_link( $category_filter );

			} else {
				$args = array(
					'post_type'  => 'projects',
				    'numberposts' => $projects_per_feed,
				    'order' => 'ASC',
				    'orderby' => 'menu_order',
				);
				$arch_link = '/projects/';
			}

			// $args = array(
			//     'post_type'  => 'projects',
			//     'numberposts' => $projects_per_feed,
			//     'order' => 'ASC',
			//     'orderby' => 'menu_order',
			// );
			$projects = get_posts( $args );

			require($item);
		},
		'has_padding' => true
	)
);