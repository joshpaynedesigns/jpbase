<section class="events-feed page-flexible-section <?php echo $padding_classes; ?>" >
	<?php if ( ! empty( $events ) ) : ?>
		<div class="events">
			<div class="wrap">
				<div class="events-title-wrap">
			        <?php if ( ! empty( $events_title ) ) : ?>
			            <h2 class="events-title"><?php echo $events_title ?></h2>
			        <?php endif; ?>
			        <?php if ( ! empty( $e_arch_link ) ) : ?>
			        	<span class="medium-gray-button small-button">
				            <a href="<?php echo $e_arch_link ?>" class="events-all-link">View All Events</a>
				        </span>
			        <?php endif; ?>
			    </div>
				<div class="events-list">
			        <?php foreach ( $events as $e ) :
			            $event_id = $e->ID;
			            $title = $e->post_title;
			            $date = tribe_get_start_date( $event_id, true, 'F d, Y' );
			            $start_mon = tribe_get_start_date( $event_id, true, 'M' );
			            $start_day = tribe_get_start_date( $event_id, true, 'd');
			            $start_time = tribe_get_start_date( $event_id, true, 'h:i');
			            $end_time = tribe_get_end_date( $event_id, true, 'h:i');
			            $permalink = get_the_permalink( $event_id );
			            ?>
			            <div class="event-item-wrap <?php /* if( Tribe__Events__Featured_Events::is_featured( $e ) ){ echo 'featured-event'; } */ ?>">
			                <a href="<?php echo $permalink ?>" class="event-item">
			                    <div class="event-date">
			                        <span class="event-date-up">
			                            <?php echo $start_mon ?>
			                        </span>
			                        <span class="event-date-down">
			                            <?php echo $start_day ?>
			                        </span>
			                    </div>
			                    <div class="event-details-wrap">
				                    <div class="event-details">
				                        <span class="event-title"><?php echo $title ?></span>
				                        <span class="event-link uppercase">View Event</span>
				                    </div>
			                    </div>
			                </a>
			            </div>
			        <?php endforeach; ?>
			    </div>
			</div>
		</div>
	<?php endif; ?>
</section>
