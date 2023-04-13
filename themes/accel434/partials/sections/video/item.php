<?php if (! empty($video_url)) : ?>
    <section class="pfsection video-section <?php echo $padding_classes; ?>">
        <div class="wrap">
            <div class="video-section-content-wrap video-<?php echo $video_side ?>">
                <div class="content-side">
                    <?php if (! empty($section_title)) : ?>
                        <h2 class="section-title white-accent"><?php echo $section_title; ?></h2>
                    <?php endif; ?>
                    <?php if (! empty($section_blurb)) : ?>
                        <div class="section-blurb f18 fcmt0 lcmb0"><?php echo $section_blurb; ?></div>
                    <?php endif; ?>
                </div>
                <div class="video-side">
                    <div class="offset-border"></div>
                    <?php if (! empty($video_thumbnail)) : ?>
                        <div class="video-thumbnail" style="background-image:url('<?php echo $video_thumbnail['url'] ?>')">
                        </div>
                    <?php endif; ?>
                    <div class="video-bg-overlay"></div>
                    <span class="play-button"></span>
                    <?php echo do_shortcode('[arve url="' . $video_url . '" mode="lightbox" loop="no" muted="no" /]'); ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
