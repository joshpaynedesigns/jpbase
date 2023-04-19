<?php
$section_title = get_sub_field('section_title');
$section_button = get_sub_field('section_button');
$images_per_row = get_sub_field('images_per_row');
if ($images_per_row == 'four') {
    $images_per_block= '16';
} else {
    $images_per_block= '9';
}
$gallery = get_sub_field('gallery');

$section_classes = ns_decide_section_classes();
?>

<section class="gallery-section text-center <?php echo $section_classes; ?>">
    <div class="wrap">
        <?php ns_section_header($section_title, 'basemb text-center'); ?>
        <?php if (! empty($gallery)) : ?>
            <div class="single-gallery-grid <?php echo $images_per_row; ?>-images">
                <?php foreach (array_chunk($gallery, $images_per_block, true) as $array) : ?>
                    <div class="gallery-row">
                        <?php foreach ($array as $image) : ?>
                            <a class="gallery-img-wrap" href="<?php echo $image['sizes']['large']; ?>" rel="gallery">
                                <div class="gallery-img" style="background-image: url(<?php echo $image['sizes']['large'] ?>);"></div>
                                <?php if ($image['caption']) : ?>
                                    <span class="gallery-caption"><strong>PC:</strong> <?php echo $image['caption']; ?></span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if (! empty($section_button)) : ?>
            <span class="gallery-button blue-button">
                <a href="<?php echo $section_button['url'] ?>" target="<?php echo $section_button['target'] ?>"><?php echo $section_button['title'] ?></a>
            </span>
        <?php endif; ?>
    </div>
</section>
