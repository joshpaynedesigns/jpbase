<?php

return (object) array(
	'acf_name'  => 'cta_section',
	'options'   => (object) array(
	'func'      => function ($padding_classes = '') {
		$p_loc = FlexibleContentSectionUtility::getSectionsDirectory();
		$fcta_loc = "$p_loc/cta";
		$item = "$fcta_loc/item.php";

		$cta_slider = get_sub_field('cta_slider');

		require($item);
	},
	'has_padding'   => false
	)
);
