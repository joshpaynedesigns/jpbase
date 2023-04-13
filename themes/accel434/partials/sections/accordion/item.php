<section class="pfsection accordion-section <?php echo $padding_classes; ?>">
	<div class="wrap">

		<?php ns_section_header( $title, 'basemb text-center' ); ?>

		<?php if ( ! empty( $intro_text ) ) : ?>
			<div class="accordion-intro text-center basemb2">
				<?php echo $intro_text; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $accordions ) ) : ?>
			<?php if ( $two_columns == 1 ) : ?>
			<div class="accordions-columns-wrap one2gridlarge">
				<div class="accordions-wrap-left">
					<?php foreach ( $accordions as $key => $ar ) : ?>
						<?php if ( $key < $midpoint ) : ?>
							<?php obj_accordion_row( $ar ); ?>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
				<div class="accordions-wrap-right">
					<?php foreach ( $accordions as $key => $ar ) : ?>
						<?php if ( $key >= $midpoint ) : ?>
							<?php obj_accordion_row( $ar ); ?>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			</div>
			<?php else : ?>
				<div class="accordions-wrap">
					<?php foreach ( $accordions as $ar ) : ?>
						<?php obj_accordion_row( $ar ); ?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</section>
