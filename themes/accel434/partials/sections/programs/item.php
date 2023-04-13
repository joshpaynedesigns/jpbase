<?php if ( ! empty( $the_programs ) ) : ?>
	<section class="programs-section pfsection <?php echo $padding_classes; ?>">
		<div class="wrap">

			<?php if ( ! empty( $section_title ) ) : ?>
				<div class="section-header">
					<h2 class=""><?php echo $section_title; ?></h2>
					<?php if ( ! empty( $section_button ) ) : ?>
						<?php echo ns_link_button( $section_button, 'medium-gray-button small-button' ); ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="programs-wrap <?php echo $slider_class; ?>">
				<?php if ( $slider ) : ?>
					<div class="slides-wrap">
				<?php endif; ?>
				<?php foreach ( $the_programs as $program ) : ?>
					<?php ns_program_block( $program ); ?>
				<?php endforeach; ?>
				<?php if ( $slider ) : ?>
					</div>
					<?php ns_slider_arrows( 32, 32 ); ?>
				<?php endif; ?>
			</div>
		</div>
	</section>
<?php endif; ?>
