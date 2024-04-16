<?php
    $width = $args['width'] ?? 22;
?>

<?php if (ns_get_field('social_selection', 'option')) : ?>
    <?php $selection = ns_get_field('social_selection', 'option'); ?>
    <div class="social-links">
        <?php foreach ($selection as $social_platform) : ?>
            <?php $link_field = $social_platform . '_link'; ?>
            <?php $selection = ns_get_field($link_field, 'option'); ?>
            <a class="social-link <?php echo $social_platform ?>" href="<?php echo $selection ?>" target="_blank">
                <?php echo ns_get_svg_icon($social_platform, $width); ?>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
