<?php if (! empty($staff_members)) : ?>
    <section class="pfsection staff-cat-grid <?php echo $padding_classes; ?>">
        <div class="wrap">
            <?php ns_section_header($section_title, 'basemb2 text-center'); ?>
            <div class="staffTermStaffGrid one24grid">
                <?php foreach ($staff_members as $s) : ?>
                    <?php ns_staff_block($s) ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
