<section class="page-flexible-section ribbon-cta-section <?php echo $padding_classes; ?> color <?php echo $bar_color ?>">
	<div class="wrap">
		<div class="ribbon-content">
			<?php if ( ! empty( $first_text || $second_text ) ) : ?>
				<div class="ribbon-text">
					<?php if ( ! empty( $first_text ) ) : ?>
						<h4 class="top-text"><?php echo $first_text ?></h4>
					<?php endif; ?>

					<?php if ( ! empty( $second_text ) ) : ?>
						<h6 class="bottom-text"><?php echo $second_text ?></h6>
					<?php endif; ?>
				</div>
				<span class="button-wrap">
			<?php else: ?>
				<span class="button-wrap centered">
			<?php endif; ?>
				<?php echo ns_link_button( $btn_details, 'white-button' ); ?>
			</span>
		</div>
	</div>
</section>
