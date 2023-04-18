<?php

$bg_color = get_sub_field('background_color');
$title = get_sub_field('section_title');
$type = get_sub_field('content_type');
$content = get_sub_field('content');
$content_2 = get_sub_field('second_content_block');
$content_3 = get_sub_field('third_content_block');
$content_4 = get_sub_field('fourth_content_block');
$vac = get_sub_field('vertical_align_center');

$va_class = "";
if ($vac) {
    $va_class = "va-center";
}

$content_blocks = 0;

if ($type === 'full-width') {
    $content_blocks = 1;
} elseif ($type === 'fifty-fifty' || $type === 'thirty-seventy' || $type === 'seventy-thirty') {
    $content_blocks = 2;
} elseif ($type === 'thirty-three') {
    $content_blocks = 3;
} elseif ($type === 'twenty-five') {
    $content_blocks = 4;
}

// Todo - handle padding and bg color
?>

<section class="pfsection content-section">
    <div class="wrap">
        <?php ns_section_header($title);  ?>

        <div class="content-section-wrap <?php echo $type ?> <?php echo $va_class ?>">
            <?php if ($content_blocks >= 1) : ?>
                <div class="fcmt0 lcmb0">
                    <?php echo $content ?>
                </div>
            <?php endif; ?>
            <?php if ($content_blocks >= 2) : ?>
                <div class="fcmt0 lcmb0">
                    <?php echo $content_2 ?>
                </div>
            <?php endif; ?>
            <?php if ($content_blocks >= 3) : ?>
                <div class="fcmt0 lcmb0">
                    <?php echo $content_3 ?>
                </div>
            <?php endif; ?>
            <?php if ($content_blocks >= 4) : ?>
                <div class="fcmt0 lcmb0">
                    <?php echo $content_4 ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
