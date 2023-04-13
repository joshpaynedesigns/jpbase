<?php

return (object) array(
	'acf_name' => 'fifty_fifty_section',
	'options'  => (object) array(
		'func'        => function ( $padding_classes = '' ) {
			$p_loc = FlexibleContentSectionUtility::getSectionsDirectory();
			$i_loc = "$p_loc/fifty_fifty";

			$item = "$i_loc/item.php";

			$content = get_sub_field( 'fifty_content' );
			$content_pos = get_sub_field( 'fifty_position' );
			$image = get_sub_field( 'fifty_image' );
			$video_url = get_sub_field( 'video_url' );
			$show_content_over_image = get_sub_field( 'show_content_over_image' );
			$over_image_content = get_sub_field( 'over_image_content' );

			$bg_class = '';
			if ( $show_content_over_image && ! empty( $over_image_content ) ) {
				$bg_class = 'has-content-over';
			}

			require $item;

		},
		'has_padding' => false,
	),
);
