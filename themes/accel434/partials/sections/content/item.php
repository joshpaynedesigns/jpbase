<section class="pfsection content-section <?php echo $padding_classes; ?> <?php if ( ( $bg_color != 'none' ) ){ echo 'color ' . $bg_color; } ?>">
	<div class="wrap">
		<?php ns_section_header( $title );  ?>

		<div class="content-section-wrap <?php echo $type ?> <?php echo $va_class ?>">
            <?php if( $content_blocks >= 1) : ?>
                <div class="fcmt0 lcmb0">
                    <?php echo $content ?>
                </div>
            <?php endif; ?>
            <?php if( $content_blocks >= 2) : ?>
                <div class="fcmt0 lcmb0">
                    <?php echo $content_2 ?>
                </div>
            <?php endif; ?>
            <?php if( $content_blocks >= 3) : ?>
                <div class="fcmt0 lcmb0">
                    <?php echo $content_3 ?>
                </div>
            <?php endif; ?>
            <?php if( $content_blocks >= 4) : ?>
                <div class="fcmt0 lcmb0">
                    <?php echo $content_4 ?>
                </div>
            <?php endif; ?>
		</div>
	</div>
</section>
