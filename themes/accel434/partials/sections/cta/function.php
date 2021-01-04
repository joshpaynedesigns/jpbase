<?php

return (object) array(
	'acf_name'  => 'cta_section',
	'options'   => (object) array(
	'func'      => function ($padding_classes = '') {
		$p_loc = FlexibleContentSectionUtility::getSectionsDirectory();
		$fcta_loc = "$p_loc/cta";
		$item = "$fcta_loc/item.php";

		$bg_img = get_sub_field('background_image')['url'];
		$disable_overlay = get_sub_field('image_overlay');
		$content = get_sub_field('content');
		$bg_type = get_sub_field('background_type');

		if ( $bg_type == "color" ) {
			$bg_img = null;
			$bg_color = "primary-color";
			$disable_overlay = true;
		}

		require($item);
	},
	'has_padding'   => false
	)
);
