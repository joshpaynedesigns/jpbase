<section class="content-section page-flexible-section <?php echo $padding_classes; ?> <?php echo $type ?> <?php if ( ( $bg_color != 'white' ) ){ echo 'color ' .$bg_color; } ?>">
	<div class="wrap">
		<?php obj_section_header( $title );  ?>
		
		<div class="content-section-blocks-wrap">
			<?php if ($content_blocks == 1 ) : ?>
			
				<div class="content-section-content">
					<?php echo $content; ?>
				</div>
			
				<?php elseif ($content_blocks == 2 ) : ?>
	
				<?php if ( ! empty( $content ) ) : ?>
					<div class="l-content">
						<?php echo $content ?>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $content_2 ) ) : ?>
					<div class="r-content">
						<?php echo $content_2 ?>
					</div>
				<?php endif; ?>
		
			<?php elseif ($content_blocks == 3) : ?>
		
				<?php if (!empty($content)) : ?>
					<div class="l-content">
						<?php echo $content ?>
					</div>
				<?php endif; ?>
				<?php if (!empty($content_2)) : ?>
					<div class="r-content">
						<?php echo $content_2 ?>
					</div>
				<?php endif; ?>
				<?php if (!empty($content_3)) : ?>
					<div class="third-content">
						<?php echo $content_3 ?>
					</div>
				<?php endif; ?>
	
			<?php endif; ?>
		</div>
	</div>
</section>
