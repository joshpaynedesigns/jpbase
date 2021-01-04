<?php

$custom_height = get_field( 'banner_custom_height' );
$banner_height = get_field( 'banner_height' );
$banner_panels = get_field( 'banner_panels' );
$default_banner_height = get_field( 'default_banner_height', 'option' );

// Set up the banner height class
$height_class = '';
if ( !empty( $banner_height ) && $custom_height ) {
	$height_class = $banner_height . '-height-banner';
} elseif ( !empty( $default_banner_height ) && !$custom_height ) {
	$height_class = $default_banner_height . '-height-banner';
} else {
	$height_class = 'medium-height-banner';
}

// If we have banner panels set lets go ahead and display them
if ( ! empty( $banner_panels ) ) { ?>

	<section class="page-banner-slider actual-slider <?php echo $height_class ?>">
		<?php
		$slide_num = count( $banner_panels );
		$display_arrows = false;

		if ( $slide_num > 1 ) {
			$display_arrows = true;
		}

		foreach ( $banner_panels as $bp ) {
			$type = $bp['banner_slide_type'];
			$txt_color = $bp['text_color'];
			$overlay = $bp['overlay'];
			$title = $bp['banner_title'];
			$subtitle = $bp['banner_sub_title'];
			$button = $bp['banner_button'];
			$use_default_bg_image = $bp['use_default_bg_image'];
			$bg_image = $bp['banner_background_image'];
			$webm_vid = $bp['webm_video_file'];
			$mp4_vid = $bp['mp4_video_file'];
			$og_vid = $bp['ogg_video_file'];

			objectiv_banner_display_slide( $type, $txt_color, $overlay, $title, $subtitle, $button, $use_default_bg_image, $bg_image, $webm_vid, $mp4_vid, $og_vid, $display_arrows );
		} ?>
	</section>
	<div class="skipContentAnchor" id="afterBanner"></div>
	<?php
} else { ?>
	<section class="page-banner-slider actual-slider <?php echo $height_class ?>">
		<?php
		$type = 'simple';
		$txt_color = 'light-text';
		$overlay = 'dark-overlay';
		$title = get_the_title();
		$use_default_bg_image = true;
		$display_arrows = false;

		objectiv_banner_display_slide( $type, $txt_color, $overlay, $title, $subtitle, $button, $use_default_bg_image, $bg_image, $webm_vid, $mp4_vid, $og_vid, $display_arrows );
		?>
	</section>
	<div class="skipContentAnchor" id="afterBanner"></div>
	<?php
}

// Function to output a single slide
function objectiv_banner_display_slide( $type, $txt_color, $overlay, $title, $subtitle, $button, $use_default_bg_image, $bg_image, $webm_vid, $mp4_vid, $og_vid, $display_arrows ) {

	$default_bg_image = get_field( 'default_banner_image', 'options' );


	// Decide which bg image to use and set it up
	if ( $use_default_bg_image || empty( $bg_image ) ) {
		$bg_image_url = $default_bg_image['url'];
	} elseif ( ! $use_default_bg_image && ! empty( $bg_image ) ) {
		$bg_image_url = $bg_image['url'];
	}

	if ( empty( $bg_image_url ) ) {
		$bg_image_url = $bg_image['url'];
	}

	?>

	<?php if ( $type === 'simple' ) : ?>

		<div class="page-banner__slide <?php echo $txt_color; ?>" style="background-image: url(<?php echo $bg_image_url ?>)">
			<?php display_slide_content( $title, $subtitle, $button, $overlay, $display_arrows ); ?>
		</div>

	<?php elseif ( $type === 'video' ) : ?>

		<div class="page-banner__slide <?php echo $txt_color; ?> video-slide" style="background-image: url(<?php echo $bg_image_url ?>)">

			<?php if ( ! empty( $webm_vid ) || ! empty( $mp4_vid || ! empty( $ogg_video_file)) ) : ?>
				<video muted loop autoplay id="banner-slide-video" poster="<?php echo $bg_image_url ?>">
					<?php if ( ! empty( $webm_vid ) ) : ?>
						<source src="<?php echo $webm_vid ?>" type="video/webm">
					<?php endif; ?>
					<?php if ( ! empty( $og_vid ) ) : ?>
						<source src="<?php echo $og_vid ?>" type="video/ogg">
					<?php endif; ?>
					<?php if ( ! empty( $mp4_vid ) ) : ?>
						<source src="<?php echo $mp4_vid ?>" type="video/mp4">
					<?php endif; ?>
				</video>
			<?php endif; ?>

			<?php display_slide_content( $title, $subtitle, $button, $overlay, $display_arrows ); ?>

		</div>
	<?php endif; ?>
	<?php
}

function display_slide_content( $title = null, $subtitle = null, $button = null, $overlay = null, $display_arrows = null ) {
	?>
	<div class="wrap">
		<div class="page-banner__content">
			<h1 class="page-banner__title"><?php echo $title; ?></h1>
			<?php if ( ! empty( $subtitle ) ): ?>
				<h4 class="page-banner__subtitle"><?php echo $subtitle; ?></h4>
			<?php endif; ?>
			<?php if ( ! empty( $button['url'] ) ): ?>
				<span class="page-banner__button white-button">
					<a href="<?php echo $button['url'] ?>"><?php echo $button['title'] ?></a>
				</span>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( $overlay != 'none' ): ?>
		<div class="overlay <?php echo $overlay; ?>"></div>
	<?php endif; ?>

	<?php if ( $display_arrows ) : ?>
		<?php objectiv_display_slider_arrows() ?>
	<?php endif; ?>

	<?php
}

// Displays the slider arrows
function objectiv_display_slider_arrows() { ?>
    <div class="left-arrow">
        <?php echo get_svg_icon( 'arrow-white', '', 22, 22 ); ?>
    </div>
    <div class="right-arrow">
        <?php echo get_svg_icon( 'arrow-white', '', 22, 22 ); ?>
    </div>
    <?php
}
