<?php
/**
 * Provides methods for Widgets that behave include taxonomy filters.
 *
 * @since   5.2.0
 *
 * @package Tribe\Events\Pro\Views\V2\Widgets
 */

namespace Tribe\Events\Pro\Views\V2\Widgets;

use Tribe__Context as Context;

/**
 * Trait Taxonomy_Filter
 *
 * @since   5.2.0
 *
 * @package Tribe\Events\Pro\Views\V2\Widgets
 *
 * @property Context $context The current View context.
 */
trait Taxonomy_Filter {

	/**
	 * Get the admin structure for a widget taxonomy filter.
	 *
	 * @since 5.2.0
	 *
	 * @return array<string,mixed> The additional structure
	 */
	public static function get_taxonomy_admin_section() {
		return [
			'taxonomy_section' => [
				'type'     => 'section',
				'classes'  => [ 'calendar-widget-filters-container' ],
				'label'    => _x( 'Filters:', 'The title for the selected taxonomy section of the List Widget.', 'tribe-events-calendar-pro' ),
				'children' => [
					'filters' => [
						'type' => 'taxonomy-filters',
						'name' => 'filters',
					],
					'operand' => [
						'type'     => 'fieldset',
						'classes'  => 'tribe-common-form-control-checkbox-radio-group',
						'label'    => _x( 'Operand:', 'The label for the taxonomy and/or option in the List Widget.', 'tribe-events-calendar-pro' ),
						'children' => [
							[
								'type'         => 'radio',
								'label'        => 'Match all',
								'button_value' => 'AND',
							],
							[
								'type'         => 'radio',
								'label'        => 'Match any',
								'button_value' => 'OR',
							],
						],
					],
				],
			],
			'taxonomy'         => [
				'type'        => 'taxonomy',
				'classes'     => 'calendar-widget-add-filter',
				'label'       => _x( 'Add a filter:', 'The label for the option to filter the List Widget events via a taxonomy.', 'tribe-events-calendar-pro' ),
				'placeholder' => _x( 'Select a Taxonomy Term', 'Placeholder label for taxonomy filter dropdown.', 'tribe-events-calendar-pro' ),
			],
		];
	}

	/**
	 * Decodes and sets the taxonomy args in a format that WP can use.
	 *
	 * @since 5.2.0
	 *
	 * @param string|array<string,mixed> $filters The current 'filter' arguments.
	 *
	 * @return array<string,mixed> $filters The clean and ready filters argument.
	 */
	public function set_taxonomy_args( $filters ) {
		$filters = maybe_unserialize( $filters );

		if ( is_string( $filters ) ) {
			$filters = json_decode( $filters, true );
		}

		// Remove empty elements from each sub-array, then from the top-level one.
		$filters = array_filter( array_map( 'array_filter', (array) $filters ) );

		return $filters;
	}

	/**
	 * Removes all filters that contains empty strings as before was creating data structures such as:
	 * {"tribe_events_cat":[]}, instead of just empty string. Return the properly formatted taxonomy
	 * filters.
	 *
	 * @since 4.4.21
	 * @since 5.2.0 carried to new widget.
	 *
	 * @param mixed $filters The filter taxonomies to be analyzed.
	 *
	 * @return string A JSON string representation of the clean and properly formatted filters.
	 */
	public function format_taxonomy_filters( $filters ) {
		$filters = maybe_unserialize( $filters );

		if ( is_string( $filters ) ) {
			$filters = json_decode( $filters, true );
		}

		// Remove empty elements from each sub-array, then from the top-level one.
		$filters = array_filter( array_map( 'array_filter', (array) $filters ) );

		return empty( $filters ) ? '' : (string) wp_json_encode( $filters );
	}

	/**
	 * Parse and format the data for select2 fields.
	 *
	 * @since 5.2.0
	 *
	 * @param array<string,mixed> $field      The data for the field we're rendering.
	 * @param \WP_Widget          $widget_obj The widget object.
	 *
	 * @return array<string,mixed> The modified field data.
	 */
	public function format_tax_data( $field, $widget_obj ) {
		$field['widget_obj'] = $widget_obj;
		$field['disabled']   = null;
		$disabled            = [];

		if ( ! ( isset( $widget_obj->number, $widget_obj->option_name ) && is_numeric( $widget_obj->number ) ) ) {
			// Trust no one.
			return $field;
		}

		// Hunt down the widget options.
		$widgets_options = get_option( $widget_obj->option_name );

		if ( ! isset( $widgets_options[ $widget_obj->number ]['filters'] ) ) {
			return $field;
		}

		$tax_filters     = json_decode( $widgets_options[ $widget_obj->number ]['filters'], true );

		if ( empty( $tax_filters ) ) {
			return $field;
		}

		// Populate the disables terms IDs.
		$disabled = array_filter( array_merge( $disabled, ...array_values( $tax_filters ) ) );

		// Convert to string for select2.
		$disabled = '[' . implode( ',', $disabled ) . ']';

		$field['disabled'] = $disabled;

		return $field;
	}

	/**
	 * Adds the taxonomy multiselect
	 *
	 * @since 5.2.0
	 *
	 * @param array<string,mixed> $field      The data for the field we're rendering.
	 * @param \WP_Widget           $widget_obj The widget object.
	 * @param \Tribe__Container    $container  The DI container.
	 */
	public function add_taxonomy_input( $field, $widget_obj, $container ) {
		$data = $this->format_tax_data( $field, $widget_obj );
		$container->make( Admin_Template::class )->template( 'widget/components/taxonomy', $data );
	}

	/**
	 * Modify the data for the taxonomy filter.
	 *
	 * @since 5.2.0
	 *
	 * @param array<string,mixed> $data The data for the field we're rendering.
	 * @param string              $field_name The slug for the field.
	 * @param obj                 $widget_obj The widget object.
	 *
	 * @return array<string,mixed> The modified field data.
	 */
	public function add_taxonomy_filters_field_data( $data, $field_name, $widget_obj ) {
		if ( 'filters' !== $field_name ) {
			return $data;
		}

		$data['id']         = $widget_obj->get_field_id( 'filters' );
		$data['name']       = $widget_obj->get_field_name( 'filters' );
		$data['list_items'] = $this->format_tax_value_for_list( $data['value'] );

		return $data;
	}

	/**
	 * Generates a formatted array of taxonomy items for the template.
	 *
	 * @since 5.2.0
	 *
	 * @param array<string,string> $value The input values to iterate through and display.
	 *
	 * @return array<string,string> $list_items The array of taxonomy items.
	 */
	public function format_tax_value_for_list( $value ) {
		if ( empty( $value ) ) {
			return [];
		}

		$value = json_decode( $value, true );

		$list_items = [];
		foreach ( $value as $tax_name => $terms ) {
			if ( empty( $terms ) ) {
				continue;
			}

			$tax_obj = get_taxonomy( $tax_name );

			$list_items[ $tax_name ] = [
				'name'  => $tax_obj->labels->name,
				'terms' => [],
			];

			foreach ( $terms as $term_name ) {
				if ( empty( $term_name ) ) {
					continue;
				}

				$term_obj = get_term( $term_name, $tax_name );

				if ( empty( $term_obj ) || is_wp_error( $term_obj ) ) {
					continue;
				}

				$list_items[ $tax_name ]['terms'][] = [
					'name' => $term_obj->name,
					'id'   => $term_obj->term_id,
				];
			}
		}

		return $list_items;
	}
}
