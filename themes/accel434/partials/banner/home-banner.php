<?php

$custom_height = ns_get_field( 'banner_custom_height' );
$banner_height = ns_get_field( 'banner_height' );
$banner_panels = ns_get_field( 'banner_panels' );

echo "<section class='home-hero-outer'>";
ns_do_home_banner( $custom_height, $banner_height, $banner_panels );
echo '</section>';
