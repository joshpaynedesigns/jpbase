<?php

// Funtion to check if we are on a tribe events page
function is_event_calendar_page() {

    if ( class_exists( 'Tribe__Events__Main' ) ) {

        $is_event_calendar = false; // Default is false so if everything falls through it returns that it is not an EC page

        if ( is_post_type_archive( 'tribe_events' ) ) { // Default Page

            $is_event_calendar = true;

        // } elseif( tribe_is_month() && !is_tax() ) { // Month View Page

        //     $is_event_calendar = true;

        } elseif( tribe_is_month() && is_tax() ) { // Month View Category Page

            $is_event_calendar = true;

        } elseif( tribe_is_past() || tribe_is_upcoming() && !is_tax() ) { // List View Page

            $is_event_calendar = true;

        } elseif( tribe_is_past() || tribe_is_upcoming() && is_tax() ) { // List View Category Page

            $is_event_calendar = true;

        } elseif( tribe_is_week() && !is_tax() ) { // Week View Page

            $is_event_calendar = true;

        } elseif( tribe_is_week() && is_tax() ) { // Week View Category Page

            $is_event_calendar = true;

        } elseif( tribe_is_day() && !is_tax() ) { // Day View Page

            $is_event_calendar = true;

        } elseif( tribe_is_day() && is_tax() ) { // Day View Category Page

            $is_event_calendar = true;

        } elseif( tribe_is_map() && !is_tax() ) { // Map View Page

            $is_event_calendar = true;

        } elseif( tribe_is_map() && is_tax() ) { // Map View Category Page

            $is_event_calendar = true;

        } elseif( tribe_is_photo() && !is_tax() ) { // Photo View Page

            $is_event_calendar = true;

        } elseif( tribe_is_photo() && is_tax() ) { // Photo View Category Page

            $is_event_calendar = true;

        } elseif( tribe_is_event() && is_single() ) { // Single Events

            $is_event_calendar = true;

        } elseif( tribe_is_venue() ) { // Single Venues

            $is_event_calendar = true;

        } elseif( get_post_type() == 'tribe_organizer' && is_single() ) { // Single Organizers

            $is_event_calendar = true;

        } else {

        }

        return $is_event_calendar;
    }
}