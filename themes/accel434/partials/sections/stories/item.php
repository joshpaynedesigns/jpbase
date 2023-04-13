<?php if (! empty( $stories ) ) : ?>
    <section class="pfsection stories-section <?php echo $padding_classes; ?>">
        <div class="wrap">
            <div class="stories-section-inner">
                <?php ns_section_header($section_title, 'text-center basemb'); ?>
                <div class="stories-slider-wrap ns-slider-arrows-wrap">
                    <div class="stories-slider">
                        <?php foreach ( $stories as $story ) : ?>
                            <?php
                                $text = ns_key_value( $story, 'text' );
                                $attribution = ns_key_value( $story, 'attribution' );
                                $location = ns_key_value( $story, 'location' );
                                $image = ns_key_value( $story, 'image' );
                            ?>
                            <div class="stories-slide">
                                <div class="stories-slide-inner">
                                    <?php if( ! empty( $image ) ) : ?>
                                        <div class="image-wrap">
                                            <?php echo wp_get_attachment_image( $image['id'], 'med_landscape', false, array( 'class' => '' ) ); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="content-wrap">
                                        <div class="content-wrap-inner">
                                            <?php if( ! empty( $text ) ) : ?>
                                                <div class="story-text"><?php echo $text ?></div>
                                            <?php endif; ?>
                                            <?php if( ! empty( $attribution ) || ! empty( $location ) ) : ?>
                                                <div class="basemt">
                                                    <?php if( ! empty( $attribution ) ) : ?>
                                                        <div class="attribution font-bold text-green f18"><?php echo $attribution ?></div>
                                                    <?php endif; ?>
                                                    <?php if( ! empty( $location ) ) : ?>
                                                        <div class="location font-bold"><?php echo $location ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php ns_slider_arrows(32, 32); ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
