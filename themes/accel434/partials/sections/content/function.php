<?php

return (object) array(
	'acf_name'  => 'content_section',
	'options'   => (object) array(
	'func'      => function ($padding_classes = '') {
            $p_loc = FlexibleContentSectionUtility::getSectionsDirectory();
            $fcta_loc = "$p_loc/content";
            $item = "$fcta_loc/item.php";

            $bg_color = get_sub_field('background_color');
            $title = get_sub_field('section_title');
            $type = get_sub_field('content_type');
            $content = get_sub_field('content');
            $content_2 = get_sub_field('second_content_block');
            $content_3 = get_sub_field('third_content_block');

            $content_blocks = 0;

            if( $type === 'full-width' ) {
                $content_blocks = 1;
            } elseif( $type === 'fifty-fifty' || $type === 'thirty-seventy' || $type === 'seventy-thirty' ) {
                $content_blocks = 2;
            } elseif( $type === 'thirty-three' ) {
                $content_blocks = 3;
            }

            require($item);
        },
        'padding_filter' => function($has_padding, $section, $field) {
	        if ($section === 'content_section') {
                $bg_color = $field['background_color'];

                return $bg_color != 'white' ? false : $has_padding;
            }

	        return $has_padding;
        },
        'has_padding'   => true,
	)
);
