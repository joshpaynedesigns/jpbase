<section class="informational-cta-section page-flexible-section <?php echo $padding_classes; ?> fifty-fifty-section <?php echo $fifty_type ?>">

    <div class="image item <?php echo $content_pos; ?>">
        <?php
        $fifty_image = wp_get_attachment_image_src( $image, 'large' )[0];
        ?>
        <?php if ( ! empty( $fifty_image ) ) : ?>
            <div class="bg <?php echo $bg_class ?>" style="background: url('<?php echo $fifty_image; ?>') no-repeat 50% 50%; background-size: cover;"></div>
            <?php if ( $show_content_over_image ) : ?>
                <div class="image-blurb-wrap">
                    <div class="image-blurb-inner-wrap">
                        <div class="image-blurb-content">
                            <?php echo $over_image_content ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="wrap">
		<div class="content-section item <?php echo $content_pos; ?>">
            <div class="content-section__inner">
                <?php if ( ! empty( $content ) ) : ?>
                    <?php echo $content ?>
                <?php endif; ?>
            </div>
		</div>
    </div>

</section>