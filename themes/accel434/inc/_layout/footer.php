<?php

/**
 * Footer
 *
 */
function objectiv_footer() { ?>
	<div class="footer-creds">
		<div class="footer-left">
			<div>Copyright &copy; <?php echo date('Y') ?> <?php bloginfo('name'); ?>, All rights reserved.</div>
		</div>
		<div class="footer-right">
			<?php if ( is_front_page() ) : ?>
				<div>Site by <a target="_blank" href="http://www.434marketing.com/">434 Marketing</a></div>
			<?php else: ?>
				<div>Site by <a target="_blank" rel="nofollow" href="http://www.434marketing.com/">434 Marketing</a></div>
			<?php endif; ?>
		</div>
	</div>
	<?php
}