<?php if ( ! empty( $staff_members ) ) : ?>
	<section class="staff-cat-grid page-flexible-section <?php echo $padding_classes; ?>">
		<div class="wrap">
			<?php obj_section_header($section_title); ?>
			<div class="staffTermStaffGrid">
				<?php foreach ( $staff_members as $s ) : ?>
					<?php
					$s_id = $s->ID;
					$thumb = get_the_post_thumbnail(
						$s_id,
						'medium',
						['class' => 'staffArchImg' ]
					);
					$name = $s->post_title;
					$has_content = $s->post_content;
					$position = ns_get_field( 'position_title', $s_id );
					$s_link = get_the_permalink( $s_id );
					?>
					<div class="staffArchBlock">
						<?php if ( !empty($has_content) ) : ?>
							<a href="<?php echo $s_link ?>" class="linked"><?php echo $thumb; ?></a>
						<?php else : ?>
							<a ><?php echo $thumb; ?></a>
						<?php endif; ?>
						<?php if ( ! empty( $has_content ) ) : ?>
							<h4 class="staffArchBlockName"><a href="<?php echo $s_link ?>"><?php echo $name ?></h4></a>
						<?php else: ?>
							<h4 class="staffArchBlockName"><?php echo $name ?></h4>
						<?php endif; ?>
						<?php if ( ! empty( $position ) ) : ?>
							<p class="staffArchBlockPosition"><?php echo $position ?></p>
						<?php endif; ?>
						<?php /* if ( ! empty( $has_content ) ) : ?>
							<a href="<?php echo $s_link ?>" class="postLink">Read Full Bio</a>
						<?php endif; */ ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif; ?>
