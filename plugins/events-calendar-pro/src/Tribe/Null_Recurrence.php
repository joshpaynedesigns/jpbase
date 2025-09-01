<?php

/**
 * Class Tribe__Events__Pro__Null_Recurrence
 *
 * @since 7.4.5
 */
class Tribe__Events__Pro__Null_Recurrence extends Tribe__Events__Pro__Recurrence {
	public function __construct() {
	}

	public function getDates( $rule_count = null ) {
		return array();
	}
}
