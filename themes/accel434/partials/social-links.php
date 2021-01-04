<?php if ( get_field( 'social_selection', 'option' ) ): ?>
	<?php $selection = get_field( 'social_selection', 'option' ); ?>
	<div class="social-links">
		<?php foreach( $selection as $social_platform ): ?>
            <?php $link_field = $social_platform . '_link'; ?>
            <?php $selection = get_field( $link_field, 'option' ); ?>
			<a class="social-link <?php echo $social_platform ?>" href="<?php echo $selection ?>" target="_blank">
				<?php echo get_svg_icon( $social_platform, '', 25, 25 ); ?>
			</a>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
