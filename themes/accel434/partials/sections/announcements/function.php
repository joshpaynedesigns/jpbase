<?php

return (object) array(
	'acf_name' => 'announcements_section',
	'options'  => (object) array(
		'func'        => function ( $padding_classes = '' ) {
			$p_loc = FlexibleContentSectionUtility::getSectionsDirectory();
			$fcta_loc = "$p_loc/announcements";
			$item = "$fcta_loc/item.php";

			$section_blurb = get_sub_field( 'section_blurb' );
			$announcements_box_title = get_sub_field( 'announcements_box_title' );
			$announcements = get_sub_field( 'announcements' );

            if ( ! empty( $announcements ) && is_array( $announcements ) ) {
                $new_announcements = array();

                $current_count = 1;
                foreach ($announcements as $key => $value) {
                    $even = $key % 2 === 0;

                    $new_announcements[$current_count][] = $value;

                    if ( ! $even ) {
                        $current_count++;
                    }
                }

                $announcements = $new_announcements;
            }

			require $item;
		},
		'has_padding' => true,
	),
);
