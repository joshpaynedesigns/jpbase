<?php
/**
 * View: Top Bar
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-pro/v2/map/top-bar.php
 *
 * See more documentation about our views templating system.
 *
 * @link https://evnt.is/1aiy
 *
 * @version 7.6.1
 *
 * @since 7.6.1 Added the Category Color Picker.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

?>
<div class="tribe-events-c-top-bar tribe-events-header__top-bar">

	<?php $this->template( 'map/top-bar/nav' ); ?>

	<?php $this->template( 'components/top-bar/today' ); ?>

	<?php $this->template( 'map/top-bar/datepicker' ); ?>

	<?php $this->template( 'components/top-bar/category-color-picker' ); ?>

	<?php $this->template( 'components/top-bar/actions' ); ?>

</div>
