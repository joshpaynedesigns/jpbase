<?php

return (object) array(
	'acf_name'  => 'logos_section',
	'options'   => (object) array(
		'func'      => function ($padding_classes = '') {
			$p_loc = FlexibleContentSectionUtility::getSectionsDirectory();
			$pf_loc = "$p_loc/logos";
			$item = "$pf_loc/item.php";

			$section_title = get_sub_field('section_title');
			$logos = get_sub_field( 'logos' );

			require($item);
		},
		'has_padding' => true
	)
);