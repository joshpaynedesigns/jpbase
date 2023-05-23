<?php
$events_title = get_sub_field('events_title');
$event_category = get_sub_field('event_category');
$posts_per_feed = get_sub_field('posts_per_feed');

if (!empty($event_category)) {
    $events = tribe_get_events(array(
        'posts_per_page' => $posts_per_feed,
        'start_date' => date('Y-m-d H:i:s'),
            'tax_query'=> array(
                array(
                    'taxonomy' => 'tribe_events_cat',
                    'field' => 'term_id',
                    'terms' => $event_category
                )
            ),
        ));
    $e_arch_link = get_term_link($event_category);
} else {
    $events = tribe_get_events(array(
        'posts_per_page' => $posts_per_feed,
        'start_date' => date('Y-m-d H:i:s'),
    ));
    $e_arch_link = '/events/';
}

$section_classes = ns_decide_section_classes('white');

?>

<?php if (! empty($events)) : ?>
    <section class="events-feed-section <?php echo $section_classes; ?>" >
        <div class="wrap">
            <div class="events-title-wrap">
                <?php if (! empty($events_title)) : ?>
                    <h2 class="mb0"><?php echo $events_title; ?></h2>
                <?php endif; ?>
                <?php if (! empty($e_arch_link)) : ?>
                    <span class="blue-button small-button smallmt">
                        <a href="<?php echo $e_arch_link; ?>" class="">View All Events</a>
                    </span>
                <?php endif; ?>
            </div>
            <div class="events-slider-outer">
                <div class="events-list-slider ns-slider-arrows-wrap">
                    <div class="events-list-slides">
                        <?php foreach ($events as $e) : ?>
                            <?php
                                $event_id   = $e->ID;
                                $title      = $e->post_title;
                                $date       = tribe_get_start_date($event_id, true, 'F d, Y');
                                $start_mon  = tribe_get_start_date($event_id, true, 'M');
                                $start_day  = tribe_get_start_date($event_id, true, 'd');
                                $start_time = tribe_get_start_date($event_id, true, 'h:i');
                                $end_time   = tribe_get_end_date($event_id, true, 'h:i');
                                $permalink  = get_the_permalink($event_id);
                            ?>
                            <a href="<?php echo $permalink; ?>">
                                <div class="event-item">
                                    <div class="event-date">
                                        <span class="event-date-up">
                                            <?php echo $start_mon; ?>
                                        </span>
                                        <span class="event-date-down">
                                            <?php echo $start_day; ?>
                                        </span>
                                    </div>
                                    <div class="event-details">
                                        <span class="event-title"><?php echo $title; ?></span>
                                        <span class="event-link uppercase">View Event</span>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <?php ns_slider_arrows(32, 32) ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
