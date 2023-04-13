<?php

return (object) array(
	'acf_name' => 'programs_section',
	'options'  => (object) array(
		'func'           => function ( $padding_classes = '' ) {
			$p_loc = FlexibleContentSectionUtility::getSectionsDirectory();
			$fcta_loc = "$p_loc/programs";
			$item = "$fcta_loc/item.php";

			$section_title = get_sub_field( 'section_title' );
			$section_button = get_sub_field( 'section_button' );
			$slider = get_sub_field( 'slider' );
			$source = get_sub_field( 'source' );
			$age_group = get_sub_field( 'age_group' );
			$category = get_sub_field( 'category' );
			$programs_to_display = get_sub_field( 'programs_to_display' );

			$the_programs = array();

			$slider_class = 'one24grid';
			if ( $slider ) {
				$slider_class = 'is-slider ns-slider-arrows-wrap';
			}

			if ( 'specific' === $source ) {
				$the_programs = $programs_to_display;
			}

			if ( 'age' === $source && ! empty( $age_group ) ) {
				$args = array(
					'numberposts' => -1,
					'orderby'     => 'title',
					'post_type'   => 'program',
					'post_status' => 'publish',
					'fields'      => 'ids',
					'tax_query'   => array(
						array(
							'taxonomy' => 'program-age-group',
							'field'    => 'term_id',
							'terms'    => $age_group,
						),
					),
				);

				$the_programs = get_posts( $args );
			}

			if ( 'cat' === $source && ! empty( $category ) ) {
				$args = array(
					'numberposts' => -1,
					'orderby'     => 'title',
					'post_type'   => 'program',
					'post_status' => 'publish',
					'fields'      => 'ids',
					'tax_query'   => array(
						array(
							'taxonomy' => 'program-cat',
							'field'    => 'term_id',
							'terms'    => $category,
						),
					),
				);

				$the_programs = get_posts( $args );
			}

			if ( 'all' === $source ) {
				$args = array(
					'numberposts' => -1,
					'orderby'     => 'title',
					'post_type'   => 'program',
					'post_status' => 'publish',
					'fields'      => 'ids',
				);

				$the_programs = get_posts( $args );
			}

			require $item;
		},
		'padding_filter' => function( $has_padding, $section, $field ) {
			if ( $section === 'boxes_section' ) {
				$bg_color = $field['background_color'];

				return $bg_color != 'white' ? false : $has_padding;
			}

			return $has_padding;
		},
		'has_padding'    => true,
	),
);
