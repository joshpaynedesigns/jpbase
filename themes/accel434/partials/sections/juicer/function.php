<?php
return (object) array(
	'acf_name'  => 'juicer_section',
	'options'   => (object) array(
	'func'      => function ($padding_classes = '') {
		$p_loc = FlexibleContentSectionUtility::getSectionsDirectory();
		$fcta_loc = "$p_loc/juicer";
		$item = "$fcta_loc/item.php";

		$title_bar = get_sub_field('juicer_title_bar');
		$juicer_feed_id = get_sub_field('juicer_feed_id');
		$juicer_per = get_sub_field('juicer_per');

		require($item);
	},
	'has_padding'   => true
	)
);