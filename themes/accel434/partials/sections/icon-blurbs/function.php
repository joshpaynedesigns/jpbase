<?php

return (object) array(
	'acf_name'  => 'icon_blurb_section',
	'options'   => (object) array(
		'func'      => function ($padding_classes = '') {
			$p_loc = FlexibleContentSectionUtility::getSectionsDirectory();
			$fcta_loc = "$p_loc/icon-blurbs";
			$item = "$fcta_loc/item.php";

			$bg_color = get_sub_field('background_color');
			$section_title = get_sub_field('section_title');
			$section_sub_title = get_sub_field('sub_title');
			$icon_blurbs = get_sub_field('icon_blurbs');
			$open_in_new_tab = get_sub_field('link_behavior');
			$button_details = get_sub_field('section_button');

			require($item);
		},
        'has_padding'   => true,
	)
);
