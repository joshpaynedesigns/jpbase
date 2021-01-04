<?php

return (object) array(
	'acf_name'  => 'boxes_section',
	'options'   => (object) array(
	'func'      => function ($padding_classes = '') {
		$p_loc = FlexibleContentSectionUtility::getSectionsDirectory();
		$fcta_loc = "$p_loc/boxes";
		$item = "$fcta_loc/item.php";

		$bg_color = get_sub_field('background_color');
		$hide_section_info = get_sub_field('hide_section_info');
		$section_title = get_sub_field('section_title');
		$button_details = get_sub_field('section_button');
		$boxes = get_sub_field('boxes');

		require($item);
	},
	'has_padding'   => true
	)
);
