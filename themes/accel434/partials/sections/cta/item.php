<section class="cta-section page-flexible-section <?php echo $padding_classes; ?> color <?php echo $bg_color; ?>" style="background-image: url(<?php echo $bg_img ?>);">
	<div class="wrap">
		<div class="cta-content">
			<?php if ( ! empty( $content ) ) : ?>
				<?php echo $content ?>
			<?php endif; ?>
		</div>

		<?php if ( ! $disable_overlay  ) : ?>
			<div class="cta-overlay">
			</div>
		<?php endif; ?>

	</div>
</section>
