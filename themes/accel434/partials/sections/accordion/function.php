<?php

return (object) array(
	'acf_name'  => 'accordion_section',
	'options'   => (object) array(
	'func'      => function ($padding_classes = '') {
		$p_loc = FlexibleContentSectionUtility::getSectionsDirectory();
		$sb_loc = "$p_loc/accordion";
		$item = "$sb_loc/item.php";

		$title = get_sub_field('section_title');
		$intro_text = get_sub_field('intro_text');
		$accordions = get_sub_field('accordion_repeater');
		$two_columns = get_sub_field('two_columns');

		$midpoint = count($accordions) / 2;

		require($item);
	},
	'has_padding'   => true
	)
);
