<?php

return (object) array(
	'acf_name'  => 'tile_section',
	'options'   => (object) array(
		'func'  => function ($padding_classes = '') {
			$p_loc = FlexibleContentSectionUtility::getSectionsDirectory();
			$i_loc = "$p_loc/tiles";
			$item = "$i_loc/item.php";

			$section_title = get_sub_field('section_title');
			$page_tiles = get_sub_field('page_tiles');
			$tiles_count = count($page_tiles);

			require($item);
		},
		'has_padding'   => true
	)
);