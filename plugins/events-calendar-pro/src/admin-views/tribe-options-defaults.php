<?php

$defaults_tab = new Tribe__Settings_Tab(
	'defaults',
	esc_html__( 'Default Content', 'tribe-events-calendar-pro' ),
	[
		'priority' => 30,
		'fields'   => [], // Parent tabs don't have content of their own!
	]
);

$venue_tab = require_once __DIR__ . '/settings/tabs/defaults/default-venue.php';
$defaults_tab->add_child( $venue_tab );

$organizer_tab = require_once __DIR__ . '/settings/tabs/defaults/default-organizer.php';
$defaults_tab->add_child( $organizer_tab );

do_action( 'tec_events_settings_tab_defaults', $defaults_tab );
