<?php
$section_title = get_sub_field('section_title');
$attachments = get_sub_field('attachments');
$attachments = ns_split_array_half($attachments);

$at_1 = false;
$at_2 = false;
if (! empty($attachments)) {
    $at_1 = $attachments[0];
    $at_2 = $attachments[1];
}

$section_classes = ns_decide_section_classes();
?>

<?php if ($attachments) : ?>
    <section class="attachments-section <?php echo $section_classes; ?>">
        <div class="wrap">
            <?php ns_section_header($section_title, 'basemb'); ?>

            <div class="attachments one2grid">
                <?php if ($at_1) : ?>
                    <div class="flex flex-col gap-4">
                        <?php foreach ($at_1 as $file) : ?>
                            <?php ns_attachment_block($file) ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <?php if ($at_2) : ?>
                    <div class="flex flex-col gap-4">
                        <div class="w-full h-1 bg-light-gray mobile-divider"></div>
                        <?php foreach ($at_2 as $file) : ?>
                            <?php ns_attachment_block($file) ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
