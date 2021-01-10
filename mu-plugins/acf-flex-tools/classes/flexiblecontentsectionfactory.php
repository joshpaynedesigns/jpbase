<?php

/**
 * Class used to create a FlexibleContentSection object
 */
class FlexibleContentSectionFactory {

	/**
	 * Creates and returns a FlexibleContentSection object ready to be ran
	 *
	 * @param string $acf_name
	 *
	 * @return FlexibleContentSection
	 */
	public static function create($acf_name) {
		$sections = FlexibleContentSectionUtility::getSections();

		foreach($sections as $sec_acf_name => $options) {
		    $padding_filter = null;

		    if (property_exists($options, 'padding_filter')) {
		        $padding_filter = $options->padding_filter;
            }

			new FlexibleContentSectionItem($sec_acf_name,
				new FlexibleContentSectionItemOptions($options->func, $options->has_padding, $padding_filter));
		}

		return new FlexibleContentSection($acf_name);
	}
}
