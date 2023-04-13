<?php

/**
 * Add Mobile Menu Trigger
 *
 * @author Wesley Cole
 * @link http://objectiv.co/
 */
add_action( 'genesis_header', 'objectiv_mobile_trigger' );
function objectiv_mobile_trigger() {

	if ( has_nav_menu( 'mobile-menu' ) || has_nav_menu( 'primary' ) ) { ?>
		<div class="mobile-show mobile-menu-icon">
			<span></span>
			<span></span>
			<span></span>
			<span></span>
		</div>
	<?php
	}
}

/**
 * Add Mobile Menu
 *
 * @author Wesley Cole
 * @link http://objectiv.co/
 */
add_action( 'genesis_before_header', 'objectiv_mobile_menu' );
function objectiv_mobile_menu() {

	if ( has_nav_menu( 'mobile-menu' ) ) { ?>
		<div id="mobile-menu" class="mobile-menu">
			<?php wp_nav_menu( array( 'menu' => 'mobile-menu' ) ); ?>
		</div>
	<?php
	}
	elseif ( has_nav_menu( 'primary') ) { ?>
		<div id="mobile-menu" class="mobile-menu">
			<?php wp_nav_menu( array( 'theme_location' => 'primary' ) ); ?>
		</div>
	<?php
	}
}


// Add an "Author" meta tag with  the blog name as the value.
function objectiv_add_author_to_head() {
	?>
	<meta name="author" content="<?php bloginfo( 'name' ); ?>">
	<?php
}
add_action( 'wp_head', 'objectiv_add_author_to_head' );

// Wrapper to allow for safe "getting of file contents" replaces "file_get_contents"
function objective_url_get_contents( $url ) {
	$args = array(
		'sslverify' => false,
	);
	return wp_remote_retrieve_body( wp_remote_get( $url, $args ) );
}


// Add a search icon to the  header right
// add_action( 'genesis_header_right', 'objectiv_header_icons' );
function objectiv_header_icons() {
	$phone = ns_get_field('phone_number', 'option');
	$phone_numbers = preg_replace( '/[^0-9]/', '', $phone );
	?>
		<div class="top-icon-section">
			<div class="searchPhoneWrap">
				<?php if ( ! empty( $phone_numbers ) ) : ?>
					<a class="phoneDesk" title="<?php echo $phone ?>" href="#">
						<div class="phoneIcon">
							<?php echo get_svg_icon('phone', '', 22, 22); ?>
						</div>
					</a>
					<a class="phonePhone" title="<?php echo $phone ?>" href="tel:<?php echo $phone_numbers ?>">
						<div class="phoneIcon">
							<?php echo get_svg_icon('phone', '', 22, 22); ?>
						</div>
					</a>
				<?php endif; ?>
				<span class="top-icon-section__search searchIcon">
					<?php echo get_svg_icon( 'search', '', 22, 22 ); ?>
				</span>
			</div>
		</div>
	<?php
}

// Add a search box to the header
// add_action( 'genesis_header', 'objectiv_header_search_box' );
function objectiv_header_search_box() { ?>
	<div class="header-search-box">
		<div class="inner-wrap">
			<?php get_search_form(); ?>
			<div class="header-search-box__close-search">
				<svg class="icon-close" fill="#b2b2b2" height="48" viewBox="0 0 48 48" width="48" xmlns="http://www.w3.org/2000/svg"><path d="M38 12.83l-2.83-2.83-11.17 11.17-11.17-11.17-2.83 2.83 11.17 11.17-11.17 11.17 2.83 2.83 11.17-11.17 11.17 11.17 2.83-2.83-11.17-11.17z"/><path d="M0 0h48v48h-48z" fill="none"/></svg>
			</div>
		</div>
	</div>
	<?php
}

// Add Alert Bar to header
add_action( 'genesis_before_header', 'alert_bar' );
function alert_bar() {
	$show_alert_bar = ns_get_field('show_alert_bar', 'option');
	$alert_bar_color = ns_get_field('alert_bar_color', 'option');
	$alert_bar_text = ns_get_field('alert_bar_text', 'option');
	$alert_bar_text_color = ns_get_field('alert_bar_text_color', 'option');
	$alert_bar_button = ns_get_field('alert_bar_button', 'option');
	?>
	<?php if ( $show_alert_bar ): ?>
		<div class="alert-bar" style="background-color: <?php echo $alert_bar_color ?>;">
			<div class="wrap">
				<span class="alert-text" style="color: <?php echo $alert_bar_text_color ?>;"><?php echo $alert_bar_text ?></span>
				<?php if ( ! empty( $alert_bar_button ) ) : ?>
					<span class="white-button alert-button">
						<a href="<?php echo $alert_bar_button['url'] ?>" target="<?php echo $alert_bar_button['target'] ?>"><?php echo $alert_bar_button['title'] ?></a>
					</span>
				<?php endif; ?>
			</div>
		</div>
	<?php endif;
}
