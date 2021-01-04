<?php

class objectiv_Contact_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array(
			'class_name' => 'objectiv_contact widget',
			'description' => 'Displays contact information.',
		);
		parent::__construct( 'objectiv_footer_contact', 'Contact Info', $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		// Get the id
        $widget_id = $args['widget_id'];
        // Get the Fields
		$title = get_field('title', 'widget_' . $widget_id);
		$logo = get_field('logo', 'widget_' . $widget_id)['sizes']['large'];
		$logo_link = get_field('logo_link', 'widget_' . $widget_id);
		$address_line_1 = get_field('address_line_1', 'widget_' . $widget_id);
		$address_line_2 = get_field('address_line_2', 'widget_' . $widget_id);
		$address_line_3 = get_field('address_line_3', 'widget_' . $widget_id);
		$show_get_directions_link = get_field('show_get_directions_link', 'widget_' . $widget_id);
		$email_address = get_field('email_address', 'widget_' . $widget_id);
		$phone = get_field('phone', 'widget_' . $widget_id);
		$phone_numbers = preg_replace( '/[^0-9]/', '', $phone );
        $fax = get_field('fax', 'widget_' . $widget_id);
		$fax_numbers = preg_replace( '/[^0-9]/', '', $fax );
		$hide_social_icons = get_field('hide_social_icons', 'widget_' . $widget_id);

        echo $args['before_widget'];
        ?>

		<?php if ( ! empty( $title ) ) : ?>
			<h4 class="widget-title widgettitle"><?php echo $title ?></h4>
		<?php endif; ?>

		<?php if ( ! empty( $logo ) ) : ?>
			<div class="footer-logo">
				<?php if ( ! empty( $logo_link ) ) : ?>
					<a href="<?php echo $logo_link['url'] ?>">
						<img src="<?php echo $logo ?>" alt="" />
					</a>
				<?php else: ?>
					<img src="<?php echo $logo ?>" alt="" />
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $address_line_1 ) || ! empty( $address_line_2 ) || ! empty( $address_line_3 ) ): ?>
			<div class="address">
				<p>
					<?php if ( ! empty( $address_line_1 ) ) : ?>
						<?php echo $address_line_1 ?>
						<br/>
					<?php endif; ?>
					<?php if ( ! empty( $address_line_2 ) ) : ?>
						<?php echo $address_line_2 ?>
						<br/>
					<?php endif; ?>
					<?php if ( ! empty( $address_line_3 ) ) : ?>
						<?php echo $address_line_3 ?>
					<?php endif; ?>
				</p>
				<?php if ($show_get_directions_link && !empty($address_line_1)): ?>
					<p class="get-directions"><a href="https://www.google.com/maps/place/<?php echo $address_line_1 ?>, <?php echo $address_line_2 ?>" target="_blank">Get Directions</a></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<div class="email-phone">
			<?php if ( ! empty( $email_address ) ) : ?>
				<div class="email">
					<p>
						Email: <?php echo objectiv_hide_email($email_address) ?>
					</p>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $phone ) || ! empty( $fax ) ) : ?>
				<div class="phone-fax">
					<p>
						<?php if ( ! empty( $phone ) ) : ?>
							Phone: <a href="tel:<?php echo $phone_numbers ?>"><?php echo $phone ?></a>
						<?php endif; ?>
						<br/>
						<?php if ( ! empty( $fax ) ) : ?>
							Fax: <a href="fax:<?php echo $fax_numbers ?>"><?php echo $fax ?></a>
						<?php endif; ?>
					</p>
				</div>
			<?php endif; ?>
		</div>

		<?php if(empty($hide_social_icons)) { get_template_part( 'partials/social', 'links' ); } ?>

        <?php
		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) { ?>
		<h2>Contact Widget</h2>
        <p>Displays the contact info set below as well as the social media icons for accounts set in the <a href="/wp-admin/admin.php?page=theme-general-settings">theme settings</a>.</p>
		<br/>
	<?php
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		return $instance;
	}
}