<?php

return (object) array(
	'acf_name'  => 'events_feed_section',
	'options'   => (object) array(
		'func'      => function ($padding_classes = '') {
			$p_loc = FlexibleContentSectionUtility::getSectionsDirectory();
			$sb_loc = "$p_loc/events-feed";
			$item = "$sb_loc/item.php";

			$events_title = get_sub_field('events_title');
			$event_category = get_sub_field('event_category');

			if ( !empty($event_category)) {
				$events = tribe_get_events( array(
					'posts_per_page' => 3,
					'start_date' => date( 'Y-m-d H:i:s' ),
						'tax_query'=> array(
							array(
								'taxonomy' => 'tribe_events_cat',
								'field' => 'term_id',
								'terms' => $event_category
							)
						),
					)
				);
				$e_arch_link = get_term_link( $event_category );

			} else {
				$events = tribe_get_events( array(
					'posts_per_page' => 3,
					'start_date' => date( 'Y-m-d H:i:s' ),
				));
				$e_arch_link = '/events/';
			}

			require($item);
		},
		'has_padding'   => true
	)
);
