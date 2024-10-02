<?php
/**
 * Default Venue settings tab.
 *
 * @since 7.0.1
 */

$venues          = Tribe__Events__Main::instance()->get_venue_info() ?? [];
$state_options   = [];
$country_options = [];
$venue_options   = [];

if ( is_array( $venues ) && ! empty( $venues ) ) {
	$venue_options[0] = __( 'No Default', 'tribe-events-calendar-pro' );
	foreach ( $venues as $venue ) {
		$venue_options[ $venue->ID ] = $venue->post_title;
	}
}

$state_options = array_merge(
	[ '' => __( 'Select a State', 'tribe-events-calendar-pro' ) ],
	Tribe__View_Helpers::loadStates()
);

$country_options = Tribe__View_Helpers::constructCountries();

// Generate the HTML for the title and description of the section.
ob_start();
?>
<div class="tec-settings-form__header-block">
	<h3 id="tec-events-pro-defaults-venue-title" class="tec-settings-form__section-header">
		<?php echo esc_html_x( 'Venue', 'Venue defaults section header', 'tribe-events-calendar-pro' ); ?>
	</h3>
	<p class="tec-settings-form__section-description">
		<?php esc_html_e( 'You can override these settings as you enter a new event.', 'tribe-events-calendar-pro' ); ?>
	</p>
</div>

<?php
$title_html = ob_get_clean();

// Generate the HTML for the Address section.
ob_start();
?>
<div class="tec-settings-form__header-block tec-settings-form__element--rowspan-2">
	<h3 id="tec-events-pro-defaults-venue-title" class="tec-settings-form__section-header tec-settings-form__section-header--sub">
		<?php echo esc_html_x( 'Address', 'Section header for Address defaults', 'tribe-events-calendar-pro' ); ?>
	</h3>
	<p class="tec-settings-form__section-description">
		<?php esc_html_e( 'You can use these settings to set specific, individual defaults for any new Venue you create (these will not be used for your default venue).', 'tribe-events-calendar-pro' ); ?>
	</p>
</div>
<?php
$address_html = ob_get_clean();

// Define the fields for the section.
$default_venue_fields = [
	'tec-events-pro-defaults-venue-title'             => [
		'type' => 'html',
		'html' => $title_html,
	],
	'eventsDefaultVenueID'                            => [
		'type'            => 'dropdown',
		'label'           => __( 'Default venue', 'tribe-events-calendar-pro' ),
		'default'         => false,
		'validation_type' => 'options',
		'options'         => $venue_options,
		'if_empty'        => __( 'No saved venues yet.', 'tribe-events-calendar-pro' ),
		'can_be_empty'    => true,
	],
	'tec-events-pro-defaults-venue-address-separator' => [
		'type' => 'html',
		'html' => '<hr class="tec-settings-form__separator--section">',
	],
	'tec-events-pro-defaults-venue-address-title'     => [
		'type' => 'html',
		'html' => $address_html,
	],
	'eventsDefaultAddress'                            => [
		'type'            => 'text',
		'label'           => __( 'Default address', 'tribe-events-calendar-pro' ),
		'default'         => false,
		'class'           => 'venue-default-info',
		'validation_type' => 'address',
		'can_be_empty'    => true,
	],
	'eventsDefaultCity'                               => [
		'type'            => 'text',
		'label'           => __( 'Default city', 'tribe-events-calendar-pro' ),
		'default'         => false,
		'class'           => 'venue-default-info',
		'validation_type' => 'city_or_province',
		'can_be_empty'    => true,
	],
	'defaultCountry'                                  => [
		'type'            => 'dropdown',
		'label'           => __( 'Default country', 'tribe-events-calendar-pro' ),
		'default'         => false,
		'class'           => 'venue-default-info',
		'validation_type' => 'options_with_label',
		'options'         => $country_options,
		'can_be_empty'    => true,
	],
	'eventsDefaultState'                              => [
		'type'            => 'dropdown',
		'label'           => __( 'Default state/province', 'tribe-events-calendar-pro' ),
		'default'         => false,
		'class'           => 'venue-default-info',
		'validation_type' => 'options',
		'options'         => $state_options,
		'can_be_empty'    => true,
	],
	'eventsDefaultProvince'                           => [
		'type'            => 'text',
		'label'           => __( 'Default state/province', 'tribe-events-calendar-pro' ),
		'default'         => false,
		'class'           => 'venue-default-info',
		'validation_type' => 'city_or_province',
		'can_be_empty'    => true,
	],
	'eventsDefaultZip'                                => [
		'type'            => 'text',
		'label'           => __( 'Default postal code/zip code', 'tribe-events-calendar-pro' ),
		'default'         => false,
		'class'           => 'venue-default-info',
		'validation_type' => 'address', // allows for letters, numbers, dashses and spaces only.
		'can_be_empty'    => true,
	],
	'eventsDefaultPhone'                              => [
		'type'            => 'text',
		'label'           => __( 'Default phone', 'tribe-events-calendar-pro' ),
		'default'         => false,
		'class'           => 'venue-default-info',
		'validation_type' => 'phone',
		'can_be_empty'    => true,
	],
	'tribeEventsCountries'                            => [
		'type'            => 'textarea',
		'label'           => __( 'Use a custom list of countries', 'tribe-events-calendar-pro' ),
		'default'         => false,
		'validation_type' => 'country_list',
		'tooltip'         => __( 'Replaces the default list.', 'tribe-events-calendar-pro' ),
		'tooltip_first'   => true,
		'append'          => '<p class="tribe-field-description description">' . __( 'One country per line in the following format: <br>US, United States <br> UK, United Kingdom.', 'tribe-events-calendar-pro' ) . '</p>',
		'can_be_empty'    => true,
	],
];

$defaults_venue = new Tribe__Settings_Tab(
	'defaults-venue-tab',
	esc_html__( 'Default Venue', 'the-events-calendar' ),
	[
		'priority' => 30.05,
		'parent'   => 'defaults',
		'fields'   => apply_filters(
			'tec_events_settings_defaults_venue_section',
			$default_venue_fields
		),
	]
);

do_action( 'tec_events_settings_tab_defaults_venue', $defaults_venue );

return $defaults_venue;
