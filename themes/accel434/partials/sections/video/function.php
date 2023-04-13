<?php

return (object) array(
    'acf_name'  => 'video_section',
    'options'   => (object) array(
        'func'      => function ($padding_classes = '') {
            $p_loc = FlexibleContentSectionUtility::getSectionsDirectory();
            $fcta_loc = "$p_loc/video";
            $item = "$fcta_loc/item.php";

            $video_side = get_sub_field('video_side');
            $section_title = get_sub_field('section_title');
            $section_blurb = get_sub_field('section_blurb');
            $button = get_sub_field('button');
            $video_thumbnail = get_sub_field('video_thumbnail');
            $video_url = get_sub_field('video_url');

            require($item);
        },
        'has_padding'   => false,
    )
);
