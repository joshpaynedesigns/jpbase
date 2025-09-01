<?php
/**
 * View: Calendar Embed
 *
 * @since   7.2.0
 *
 * @version 7.2.0
 *
 * var array<string,mixed> $attributes array<string|mixed> An array of the block attributes.
 */

use Tribe\Shortcode\Manager;
use Tribe\Events\Pro\Views\V2\Shortcodes\REST\V1\Calender_Embed;

$calendar_embed = tribe( Calender_Embed::class );
$manager        = tribe( Manager::class );

// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
// phpcs:disable StellarWP.XSS.EscapeOutput.OutputNotEscaped
echo $manager->render_shortcode( $calendar_embed->process_attributes( $attributes ), '', $calendar_embed->get_shortcode_slug() );
// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
// phpcs:enable StellarWP.XSS.EscapeOutput.OutputNotEscaped
