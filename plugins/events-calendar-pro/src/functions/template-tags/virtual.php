<?php
/**
 * Virtual Event Template Tags.
 *
 * @since 7.0.1 Migrated to Events Pro from Events Virtual.
 *
 */
if ( ! function_exists( 'tribe_events_virtual_show_fail_message' ) ) {
	/**
	 * Shows a message to indicate the plugin cannot be loaded due to missing requirements.
	 *
	 * @since 1.0.0
	 * @since 1.14.0 Include message as a param.
	 *
	 * @param ?string $message The message to show. Defaults to null.
	 */
	function tribe_events_virtual_show_fail_message( string $message = null ) {
		_deprecated_function( __FUNCTION__, '7.0.1', 'tribe_show_fail_message' );
	}
}

if ( ! function_exists( 'tribe_events_virtual_load_text_domain' ) ) {
	/**
	 * Loads the plugin localization files.
	 *
	 * If the text domain loading functions provided by `common` (from The Events Calendar or Event Tickets) are not
	 * available, then the function will use the `load_plugin_textdomain` function.
	 *
	 * @since      1.0.4
	 *
	 * @deprecated 1.14.0 Use `tribe_load_textdomain` instead.
	 */
	function tribe_events_virtual_load_text_domain() {
		_deprecated_function( __FUNCTION__, '7.0.1' );
	}
}

if ( ! function_exists( 'tribe_events_virtual_load' ) ) {
	/**
	 * Register and load the service provider for loading the plugin.
	 *
	 * @since 1.0.0
	 */
	function tribe_events_virtual_load() {
		// Load the plugin, autoloading happens here.
		tribe_register_provider( \Tribe\Events\Virtual\Plugin::class );
	}
}

if ( ! function_exists( 'tribe_events_virtual_uninstall' ) ) {
	/**
	 * Handles the removal of PUE-related options when the plugin is uninstalled.
	 *
	 * @since 1.0.0
	 */
	function tribe_events_virtual_uninstall() {
		_deprecated_function( __FUNCTION__, '7.0.1' );
	}
}

if ( ! function_exists( 'tribe_events_virtual_preload' ) ) {
	/**
	 * Get virtual label.
	 * Returns the capitalized version of the "Virtual" Term.
	 *
	 * Note: the output of this function is not escaped.
	 * You should escape it wherever you use it!
	 *
	 * @since 7.0.1 Migrated to Events Pro from Events Virtual.
	 *
	 * @return string
	 */
	function tribe_get_virtual_label() {
		$label = _x( 'Virtual', 'Capitalized version of the "virtual" term.', 'tribe-events-calendar-pro' );

		/**
		 * Allows customization of the capitalized version of the "virtual" term.
		 *
		 * Note: the output of this filter is not escaped!
		 *
		 * @param string $label The capitalized version of the "virtual" term, defaults to "Virtual".
		 *
		 * @see tribe_get_event_label_plural
		 */
		return apply_filters( 'tribe_virtual_label', $label );
	}
}

if ( ! function_exists( 'tribe_get_virtual_label_lower' ) ) {
	/**
	 * Get lowercase virtual label.
	 * Returns the lowercase version of the "Virtual" Term.
	 *
	 * Note: the output of this function is not escaped.
	 * You should escape it wherever you use it!
	 *
	 * @since 7.0.1 Migrated to Events Pro from Events Virtual.
	 *
	 * @return string The lowercase version of the "Virtual" Term.
	 */
	function tribe_get_virtual_label_lowercase() {
		$label = _x( 'virtual', 'Lowercase version of the "virtual" term.', 'tribe-events-calendar-pro' );

		/**
		 * Allows customization of the lowercase version of the "virtual" term.
		 *
		 * Note: the output of this filter is not escaped!
		 *
		 * @param string $label The lowercase version of the "virtual" term, defaults to "virtual".
		 *
		 * @see tribe_get_event_label_plural
		 */
		return apply_filters( 'tribe_virtual_label_lowercase', $label );
	}
}

if ( ! function_exists( 'tribe_get_virtual_event_label_plural' ) ) {
	/**
	 * Get virtual event label singular.
	 * Returns the singular version of the Event Label.
	 *
	 * Note: the output of this function is not escaped.
	 * You should escape it wherever you use it!
	 *
	 * @since 7.0.1 Migrated to Events Pro from Events Virtual.
	 *
	 * @return string The singular version of the Event Label.
	 */
	function tribe_get_virtual_event_label_singular() {
		$label = sprintf(
			_x(
				'%1$s %2$s', 'Capitalized "virtual" term, capitalized singular event term.', 'tribe-events-calendar-pro'
			), tribe_get_virtual_label(), tribe_get_event_label_singular()
		);

		/**
		 * Allows customization of the singular version of the Virtual Event Label.
		 *
		 * Note: the output of this filter is not escaped!
		 *
		 * @param string $label The singular version of the Virtual Event label,
		 *                      defaults to "Virtual Event"
		 *                      (or the filtered term for "Virtual" + the filtered term for "Event").
		 *
		 * @see tribe_get_event_label_plural
		 */
		return apply_filters( 'tribe_virtual_event_label_singular', $label );
	}
}

if ( ! function_exists( 'tribe_get_virtual_event_label_singular_lowercase' ) ) {
	/**
	 * Get virtual event label singular lowercase.
	 * Returns the lowercase singular version of the Event Label.
	 *
	 * Note: the output of this function is not escaped.
	 * You should escape it wherever you use it!
	 *
	 * @since 7.0.1 Migrated to Events Pro from Events Virtual.
	 *
	 * @return string
	 */
	function tribe_get_virtual_event_label_singular_lowercase() {
		$label = sprintf(
			_x(
				'%1$s %2$s', 'Lowercase "virtual" term, singular lowercase event term.', 'tribe-events-calendar-pro'
			), tribe_get_virtual_label_lowercase(), tribe_get_event_label_singular_lowercase()
		);

		/**
		 * Allows customization of the singular lowercase version of the Virtual Event Label.
		 *
		 * Note: the output of this filter is not escaped!
		 *
		 * @param string $label The singular lowercase version of the Virtual Event label,
		 *                      defaults to "virtual events"
		 *                      (or the filtered term for "virtual" + the filtered term for "event").
		 *
		 * @see tribe_get_event_label_singular_lowercase
		 */
		return apply_filters( 'tribe_virtual_event_label_singular_lowercase', $label );
	}
}

if ( ! function_exists( 'tribe_get_virtual_event_label_plural' ) ) {
	/**
	 * Get virtual event label plural.
	 * Returns the plural version of the Event Label.
	 *
	 * Note: the output of this function is not escaped.
	 * You should escape it wherever you use it!
	 *
	 * @since 7.0.1 Migrated to Events Pro from Events Virtual.
	 *
	 * @return string
	 */
	function tribe_get_virtual_event_label_plural() {
		$label = sprintf(
			_x(
				'%1$s %2$s', 'Capitalized "virtual" term, capitalized plural event term.', 'tribe-events-calendar-pro'
			), tribe_get_virtual_label(), tribe_get_event_label_plural()
		);

		/**
		 * Allows customization of the plural version of the Virtual Event Label.
		 *
		 * Note: the output of this filter is not escaped!
		 *
		 * @param string $label The plural version of the Virtual Event label,
		 *                      defaults to "Virtual Events"
		 *                      (or the filtered term for "Virtual" + the filtered term for "Events").
		 *
		 * @see tribe_get_event_label_plural
		 */
		return apply_filters( 'tribe_virtual_event_label_plural', $label );
	}
}

if ( ! function_exists( 'tribe_get_virtual_event_label_plural_lowercase' ) ) {
	/**
	 * Get virtual event label plural lowercase.
	 * Returns the lowercase plural version of the Event Label.
	 *
	 * Note: the output of this function is not escaped.
	 * You should escape it wherever you use it!
	 *
	 * @since 7.0.1 Migrated to Events Pro from Events Virtual.
	 *
	 * @return string
	 */
	function tribe_get_virtual_event_label_plural_lowercase() {
		$label = sprintf(
			_x(
				'%1$s %2$s', 'Lowercase "virtual" term, lowercase plural event term.', 'tribe-events-calendar-pro'
			), tribe_get_virtual_label_lowercase(), tribe_get_event_label_plural_lowercase()
		);

		/**
		 * Allows customization of the plural lowercase version of the Virtual Event Label.
		 *
		 * Note: the output of this filter is not escaped!
		 *
		 * @param string $label The plural lowercase version of the Virtual Event label,
		 *                      defaults to "virtual events" (lowercase)
		 *                      (or the filtered term for "virtual" + the filtered term for "events").
		 *
		 * @see tribe_get_event_label_plural_lowercase
		 */
		return apply_filters( 'tribe_virtual_event_label_plural_lowercase', $label );
	}
}

if ( ! function_exists( 'tribe_get_hybrid_label' ) ) {
	/**
	 * Get hybrid label.
	 * Returns the capitalized version of the "Hybrid" Term.
	 *
	 * Note: the output of this function is not escaped.
	 * You should escape it wherever you use it!
	 *
	 * @since 7.0.1 Migrated to Events Pro from Events Virtual.
	 *
	 * @return string
	 */
	function tribe_get_hybrid_label() {
		$label = _x( 'Hybrid', 'Capitalized version of the "hybrid" term.', 'tribe-events-calendar-pro' );

		/**
		 * Allows customization of the capitalized version of the "hybrid" term.
		 *
		 * Note: the output of this filter is not escaped!
		 *
		 * @param string $label The capitalized version of the "hybrid" term, defaults to "Hybrid".
		 *
		 * @see tribe_get_event_label_plural
		 */
		return apply_filters( 'tribe_hybrid_label', $label );
	}
}

if ( ! function_exists( 'tribe_get_hybrid_label_lowercase' ) ) {
	/**
	 * Get hybrid event label singular.
	 * Returns the singular version of the Event Label.
	 *
	 * Note: the output of this function is not escaped.
	 * You should escape it wherever you use it!
	 *
	 * @since 7.0.1 Migrated to Events Pro from Events Virtual.
	 *
	 * @return string The singular version of the Event Label.
	 */
	function tribe_get_hybrid_event_label_singular() {
		$label = sprintf(
			_x(
				'%1$s %2$s', 'Capitalized "hybrid" term, capitalized singular event term.', 'tribe-events-calendar-pro'
			), tribe_get_hybrid_label(), tribe_get_event_label_singular()
		);

		/**
		 * Allows customization of the singular version of the Hybrid Event Label.
		 *
		 * Note: the output of this filter is not escaped!
		 *
		 * @param string $label The singular version of the Hybrid Event label,
		 *                      defaults to "Hybrid Event"
		 *                      (or the filtered term for "Hybrid" + the filtered term for "Event").
		 *
		 * @see tribe_get_event_label_plural
		 */
		return apply_filters( 'tribe_hybrid_event_label_singular', $label );
	}
}
