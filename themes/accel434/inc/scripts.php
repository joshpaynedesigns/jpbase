<?php
/**
* Scripts / Styles
*
* Handles front end scripts and styles.
*
* @package     Objectiv-Genesis-Child
* @since       1.0
* @author      Wes Cole <wes@objectiv.co>
*/

function objectiv_enqueue_scripts() {

	// SVG Shim
	wp_register_script( 'svg4everybody', get_stylesheet_directory_uri() . '/assets/components/svg4everybody/dist/svg4everybody.min.js', array());
	wp_enqueue_script('svg4everybody');

	//  Google Fonts
	// wp_register_style( 'google-fonts', '//fonts.googleapis.com/css?family=Lato:100,200,300,400,700' );
	// wp_enqueue_style( 'google-fonts' );

	// Slick
	wp_register_script( 'slick', get_stylesheet_directory_uri() . '/assets/components/slick-carousel/slick/slick.min.js', '', true );
	wp_register_style( 'slick-css', get_stylesheet_directory_uri() . '/assets/components/slick-carousel/slick/slick.css' );

	// Modaal
	wp_register_script( 'modaal', get_stylesheet_directory_uri() . '/assets/components/modaal/dist/js/modaal.min.js', array('jquery'), false, true );
	wp_register_style( 'modaal-css', get_stylesheet_directory_uri() . '/assets/components/modaal/dist/css/modaal.min.css' );

	// Accessible Menu
	wp_register_script( 'gamajo-accessible-menu', get_stylesheet_directory_uri() . '/assets/components/accessible-menu/dist/jquery.accessible-menu.min.js', array('jquery'), '1.0.0', true );

	wp_register_script( 'sitewide', get_bloginfo('stylesheet_directory') . '/assets/js/build/site-wide.min.js', array('gamajo-accessible-menu', 'jquery'), '', true );

	wp_enqueue_style( 'slick-css' );
	wp_enqueue_script( 'modaal' );
	wp_enqueue_style( 'modaal-css' );
	wp_enqueue_script( 'sitewide' );
	wp_enqueue_script( 'slick' );

	$data_array = array(
		'stylesheetUrl' => get_stylesheet_directory_uri()
	);

	wp_localize_script( 'sitewide', 'data', $data_array );

}

add_action( 'wp_enqueue_scripts', 'objectiv_enqueue_scripts' );
