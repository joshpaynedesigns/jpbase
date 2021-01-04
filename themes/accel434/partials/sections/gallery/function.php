<?php

return (object) array(
	'acf_name'  => 'gallery_section',
	'options'   => (object) array(
		'func'  => function ($padding_classes = '') {
			$p_loc = FlexibleContentSectionUtility::getSectionsDirectory();
			$sb_loc = "$p_loc/gallery";
			$item = "$sb_loc/item.php";

			$section_title = get_sub_field('section_title');
			$section_button = get_sub_field('section_button');
			$images_per_row = get_sub_field('images_per_row');
			if($images_per_row == 'four'){ 
                $images_per_block= '16'; 
            } else { 
                $images_per_block= '9'; 
            }
			$gallery = get_sub_field('gallery');
			
			require($item);

		},
		'has_padding'   => true
	)
);