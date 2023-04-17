<section class="pfsection ribbon-cta-section">
    <div class="wrap">
        <div class="ribbon-content color <?php echo $bar_color ?>">
            <?php if (! empty($first_text || $second_text)) : ?>
                <div class="ribbon-text">
                    <?php if (! empty($first_text)) : ?>
                        <h3 class="top-text"><?php echo $first_text ?></h3>
                    <?php endif; ?>

                    <?php if (! empty($second_text)) : ?>
                        <div class="bottom-text"><?php echo $second_text ?></div>
                    <?php endif; ?>
                </div>
                <span class="button-wrap">
            <?php else : ?>
                <span class="button-wrap centered">
            <?php endif; ?>
                <?php echo ns_link_button($btn_details, 'white-button'); ?>
            </span>
        </div>
    </div>
</section>
