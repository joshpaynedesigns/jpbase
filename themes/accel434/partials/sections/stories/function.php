<?php

return (object) array(
	'acf_name'  => 'stories_slider',
	'options'   => (object) array(
	'func'      => function ($padding_classes = '') {
		$p_loc = FlexibleContentSectionUtility::getSectionsDirectory();
		$fcta_loc = "$p_loc/stories";
		$item = "$fcta_loc/item.php";

		$section_title = get_sub_field('section_title');
		$stories = get_sub_field('stories');

		require($item);
	},
	'has_padding'   => true
	)
);
