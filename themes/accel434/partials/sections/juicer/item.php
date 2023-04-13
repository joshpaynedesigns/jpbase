<?php if ( ! empty( $juicer_feed_id ) ) : ?>
	<section class="juicerFeedSection pfsection <?php echo $padding_classes; ?>">
		<div class="wrap">
			<?php if ( ! empty( $title_bar ) ) : ?>
				<header class="sectionHeader">
						<?php echo $title_bar; ?>
				</header>
			<?php endif; ?>
			<div class="juicerFeedWrap">
				<?php if ( ! empty( $juicer_per ) && ! empty( $juicer_feed_id ) ) : ?>
				<script src="https://assets.juicer.io/embed.js" type="text/javascript"></script>
					<link href="https://assets.juicer.io/embed.css" media="all" rel="stylesheet" type="text/css" />
				<ul class="juicer-feed" data-per="<?php echo $juicer_per; ?>" data-feed-id="<?php echo $juicer_feed_id; ?>" data-columns="4" data-style="slider"></ul>
				<?php endif; ?>
			</div>
		</div>
	</section>
<?php endif; ?>
