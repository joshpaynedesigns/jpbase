<?php

return (object) array(
	'acf_name'  => 'blog_section',
	'options'   => (object) array(
		'func'  => function ($padding_classes = '') {
			$p_loc = FlexibleContentSectionUtility::getSectionsDirectory();
			$sb_loc = "$p_loc/blog";
			$item = "$sb_loc/item.php";

			$section_title = get_sub_field( 'title' );
			$posts_per_feed = get_sub_field('posts_per_feed');
			$blog_category = get_sub_field( 'blog_category' );

			if ( !empty($blog_category)) {
				$args = array(
					'post_type' => 'post',
					'posts_per_page' => $posts_per_feed,
					'tax_query' => array(
						array(
							'taxonomy' => 'category',
							'field' => 'term_id',
							'terms' => $blog_category,
						),
					)
				);
				$arch_link = get_term_link( $blog_category );
			} else {
				$args = array(
					'post_type' => 'post',
					'posts_per_page' => $posts_per_feed
				);
				$arch_link = get_post_type_archive_link( 'post' );
			}

			$loop = new WP_Query($args);
			
			require($item);

		},
		'has_padding'   => true
	)
);