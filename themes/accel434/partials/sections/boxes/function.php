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
	'padding_filter' => function($has_padding, $section, $field) {
            if ($section === 'boxes_section') {
                $bg_color = $field['background_color'];

                return $bg_color != 'white' ? false : $has_padding;
            }

            return $has_padding;
        },
        'has_padding'   => true,
	)
);