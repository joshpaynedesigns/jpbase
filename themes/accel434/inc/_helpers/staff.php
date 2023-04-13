<?php

function ns_staff_block( $s ) {

	?>
	<?php
		$s_id        = $s->ID;
		$thumb       = get_the_post_thumbnail(
			$s_id,
			'medium',
			array( 'class' => 'staffArchImg' )
		);
		$name        = $s->post_title;
		$has_content = ! empty( $s->post_content );
		$position    = ns_get_field( 'position_title', $s_id );
		$s_link      = get_the_permalink( $s_id );

	?>
		<?php if ( $has_content ) : ?>
			<a class="staffArchBlockLink" href="<?php echo $s_link; ?>">
		<?php endif; ?>
		<div class="staffArchBlock text-center">
			<?php if ( ! empty( $thumb ) ) : ?>
				<div class="image-wrap">
					<?php echo $thumb; ?>
				</div>
			<?php endif; ?>
			<?php if ( ! empty( $name ) ) : ?>
				<h5 class="name mb0"><?php echo $name; ?></h5>
			<?php endif; ?>
			<?php if ( ! empty( $position ) ) : ?>
				<div class="position"><?php echo $position; ?></div>
			<?php endif; ?>
		</div>
		<?php if ( $has_content ) : ?>
			</a>
		<?php endif; ?>
	<?php
}
