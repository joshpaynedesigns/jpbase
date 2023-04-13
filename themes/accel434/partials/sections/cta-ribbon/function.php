<?php

return (object) array(
	'acf_name'  => 'ribbon_cta_section',
	'options'   => (object) array(
	'func'      => function ($padding_classes = '') {
		$p_loc = FlexibleContentSectionUtility::getSectionsDirectory();
		$sb_loc = "$p_loc/cta-ribbon";
		$item = "$sb_loc/item.php";

		$first_text = get_sub_field('line_one_text');
		$second_text = get_sub_field('line_two_text');
		$btn_details = get_sub_field('link_details');
		$bar_color = get_sub_field('bar_color');

		require($item);
	},
	'has_padding'   => false
	)
);
