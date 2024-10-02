<?php
/**
 * Default Organizer settings tab.
 *
 * @since 7.0.1
 */

$organizers        = Tribe__Events__Main::instance()->get_organizer_info() ?? [];
$organizer_options = [];

if ( is_array( $organizers ) && ! empty( $organizers ) ) {
	$organizer_options[0] = __( 'No Default', 'tribe-events-calendar-pro' );
	foreach ( $organizers as $organizer ) {
		$organizer_options[ $organizer->ID ] = $organizer->post_title;
	}
}


$default_organizer_fields = [
	'tec-events-pro-defaults-organizer-title' => [
		'type' => 'html',
		'html' => '<div class="tec-settings-form__header-block">'
			. '<h3 id="tec-events-pro-defaults-organizer-title" class="tec-settings-form__section-header">'
			. _x( 'Organizer', 'Default organizer section header', 'tribe-events-calendar-pro' )
			. '</h3>'
			. '<p class="tec-settings-form__section-description">'
			. esc_html__( 'You can override these settings as you enter a new event.', 'tribe-events-calendar-pro' )
			. '</p>'
			. '</div>',
	],
	'eventsDefaultOrganizerID'                => [
		'type'            => 'dropdown',
		'label'           => __( 'Default organizer', 'tribe-events-calendar-pro' ),
		'default'         => false,
		'validation_type' => 'options',
		'options'         => $organizer_options,
		'if_empty'        => __( 'No saved organizers yet.', 'tribe-events-calendar-pro' ),
		'can_be_empty'    => true,
	],
];

$defaults_organizer = new Tribe__Settings_Tab(
	'defaults-organizer-tab',
	esc_html__( 'Default Organizer', 'the-events-calendar' ),
	[
		'priority' => 30.10,
		'fields'   => apply_filters(
			'tec_events_settings_defaults_organizer_section',
			$default_organizer_fields
		),
		'parent'   => 'defaults',
	]
);

do_action( 'tec_events_settings_tab_defaults_organizer', $defaults_organizer );

return $defaults_organizer;
