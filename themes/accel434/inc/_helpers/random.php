<?php

function ns_section_header( $title = null, $classes = 'basemb', $title_classes = "" ) {

	if ( ! empty( $title ) ) : ?>
		<header class="section-header <?php echo $classes; ?> fcmt0 lcmb0">
			<h2 class="section-title <?php echo $title_classes ?>"><?php echo $title; ?></h2>
		</header>
		<?php
	endif;
}

function ns_split_array_half( $array = null ) {
	if ( empty( $array ) || ! is_array( $array ) ) {
		return false;
	}

	return array_chunk( $array, ceil( count( $array ) / 2 ) );
}

function ns_attachment_block( $file = null ) {

    if ( empty( $file ) ) {
        return;
    }

	$attachment_name = ns_key_value( $file, 'attachment_name' );
	$attachment_file = ns_key_value( $file, 'attachment_file' );
	$attachment_file = ns_key_value( $attachment_file, 'url' );

	?>
	 <?php if ( $attachment_file ) : ?>
		<a class="attachment flex items-center gap-4" href="<?php echo $attachment_file; ?>">
			<div class="attachment_icon text-white">
				<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
					<path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
				</svg>

			</div>
			<div class="attachment_info">
				<div class="attachment_title f18"><?php echo $attachment_name; ?></div>
			</div>
		</a>

        <div class="w-full h-1 bg-extra-light-gray lotdn"></div>
	<?php endif; ?>
	<?php
}
