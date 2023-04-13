<?php

return (object) array(
	'acf_name' => 'attachments_section',
	'options'  => (object) array(
		'func'        => function ( $padding_classes = '' ) {
			$p_loc = FlexibleContentSectionUtility::getSectionsDirectory();
			$fcta_loc = "$p_loc/attachments";
			$item = "$fcta_loc/item.php";

			$section_title = get_sub_field( 'section_title' );
			$attachments = get_sub_field( 'attachments' );
			$attachments = ns_split_array_half( $attachments );

            $at_1 = false;
            $at_2 = false;
            if ( ! empty( $attachments ) ) {
                $at_1 = $attachments[0];
                $at_2 = $attachments[1];
            }

			require $item;
		},
		'has_padding' => true,
	),
);
