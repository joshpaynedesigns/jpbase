<section class="icon-blurb-section page-flexible-section <?php echo $padding_classes; ?> <?php if ( ( $bg_color != 'white' ) ){ echo 'color ' .$bg_color; } ?>">
	<div class="wrap">
		<div class="icon-blurb-content">

			<div class="upper-content">
				<?php obj_section_header($section_title); ?>

				<?php if ( ! empty( $section_sub_title ) ) : ?>
					<h6 class="section-sub-title basemb"><?php echo $section_sub_title ?></h6>
				<?php endif; ?>
			</div>

			<div class="icon-blurb-grid one23grid">
				<?php foreach ($icon_blurbs as $ib ) :
					$blurb_link = $ib['icon_blurb_url'];
					$icon = $ib['icon']['url'];
					$blurb_title = $ib['blurb_title'];
					$blurb = $ib['blurb'];

					?>
					<div class="blurb tac">
						<?php if ( ! empty( $blurb_link ) ) : ?>
							<a href="<?php echo $blurb_link['url'] ?>" target="<?php echo $blurb_link['target'] ?>">
						<?php endif; ?>

							<div class="inner-blurb">
								<?php if ( ! empty( $icon ) ) : ?>
									<div class="blurb-image-wrap">
										<img class="blurb-image" src="<?php echo $icon ?>" alt="<?php echo $blurb_title ?>">
									</div>
								<?php endif; ?>

								<?php if ( ! empty( $blurb_title ) ) : ?>
									<h6 class="blurb-title"><?php echo $blurb_title ?></h6>
								<?php endif; ?>

								<?php if ( ! empty( $blurb ) ) : ?>
									<p class="blurb-text"><?php echo $blurb ?></p>
								<?php endif; ?>
							</div>

						<?php if ( ! empty( $blurb_link ) ) : ?>
							</a>
						<?php endif; ?>
					</div>

				<?php endforeach; ?>
			</div>

			<?php if ( ! empty( $button_details ) ) : ?>
				<div class="bottom-content">
					<span class="medium-gray-button small-button">
	                    <a href="<?php echo $button_details['url'] ?>" target="<?php echo $button_details['target'] ?>"><?php echo $button_details['title'] ?></a>
	                </span>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>