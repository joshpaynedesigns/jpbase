<?php if ($attachments) : ?>
    <section class="pfsection attachments-section <?php echo $padding_classes; ?>">
        <div class="wrap">
            <?php ns_section_header($section_title,'basemb','green-accent'); ?>

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
                        <?php foreach ($at_2 as $file) : ?>
                            <?php ns_attachment_block($file) ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
