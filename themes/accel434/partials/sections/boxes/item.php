<section class="boxes-section page-flexible-section <?php echo $padding_classes; ?> <?php if ( ( $bg_color != 'white' ) ){ echo 'color ' .$bg_color; } ?>  <?php if($hide_section_info){ echo 'hide-section-info'; } ?>">
	<div class="wrap">
		<div class="boxes-content box-item">
			<?php obj_section_header($section_title); ?>
			<?php if ( ! empty( $button_details ) ) : ?>
				<span class="dark-gray-button">
					<a href="<?php echo $button_details['url'] ?>" target="<?php echo $button_details['target'] ?>"><?php echo $button_details['title'] ?></a>
				</span>
			<?php endif; ?>
		</div>

		<?php foreach ($boxes as $box ) :
			$box_icon = $box['icon']['url'];
			$box_title = $box['box_title'];
			$box_text = $box['box_text'];
			$show_button = $box['show_button'];
			$box_link = $box['box_url'];
			?>
				<?php if ( ! empty( $box_link && !$show_button ) ) : ?>
					<a class="box tac box-item linked" href="<?php echo $box_link['url'] ?>" target="<?php echo $box_link['target'] ?>">
				<?php else: ?>
					<div class="box tac box-item">
				<?php endif; ?>

					<?php if ( ! empty( $box_icon ) ) : ?>
						<img class="box-icon" src="<?php echo $box_icon ?>" alt="<?php echo $box_title ?>">
					<?php endif; ?>
					<h5 class="box-title"><?php echo $box_title ?></h5>
					<?php if ( ! empty( $box_text ) ) : ?>
						<p class="box-text"><?php echo $box_text ?></p>
					<?php endif; ?>
					<?php if ( ! empty( $box_link && $show_button ) ) : ?>
						<span class="white-button small-button">
							<a class="box-button" href="<?php echo $box_link['url'] ?>" target="<?php echo $box_link['target'] ?>"><?php echo $box_link['title'] ?></a>
						</span>
					<?php endif; ?>

				<?php if ( ! empty( $box_link && !$show_button ) ) : ?>
					</a>
				<?php else: ?>
					</div>
				<?php endif; ?>
		<?php endforeach; ?>
	</div>
</section>
