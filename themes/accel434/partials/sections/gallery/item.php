<section class="gallery-section page-flexible-section <?php echo $padding_classes; ?>">
	<div class="wrap">
        <?php obj_section_header($section_title); ?>
		<div class="gallery">
            <?php if ( ! empty( $gallery ) ): ?>
                <div class="single-gallery-grid <?php echo $images_per_row; ?>-images">
                    <?php foreach (array_chunk($gallery, $images_per_block, true) as $array): ?>
                        <div class="gallery-row">
                            <?php foreach($array as $image): ?>
                                <a class="gallery-img-wrap" href="<?php echo $image['sizes']['large']; ?>" rel="gallery">
                                    <div class="gallery-img" style="background-image: url(<?php echo $image['sizes']['large'] ?>);"></div>
                                    <?php if($image['caption']): ?>
                                        <span class="gallery-caption"><strong>PC:</strong> <?php echo $image['caption']; ?></span>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>    
            <?php endif; ?>
        </div>
        <?php if ( ! empty( $section_button ) ): ?>
            <span class="gallery-button medium-gray-button small-button">
                <a href="<?php echo $section_button['url'] ?>" target="<?php echo $section_button['target'] ?>"><?php echo $section_button['title'] ?></a>
            </span>
        <?php endif; ?>
    </div>
</section>
