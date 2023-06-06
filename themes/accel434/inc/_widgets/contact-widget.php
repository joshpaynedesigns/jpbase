<?php

class ns_Contact_Widget extends WP_Widget
{
    /**
     * Sets up the widgets name etc
     */
    public function __construct()
    {
        $widget_ops = array(
            'class_name'  => 'ns_contact widget',
            'description' => 'Displays contact information.',
        );
        parent::__construct('ns_footer_contact', 'Contact Info', $widget_ops);
    }

    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget($args, $instance)
    {
        // Get the id
        $widget_id = $args['widget_id'];
        // Get the Fields
        $title             = ns_get_field('title', 'widget_' . $widget_id);
        $address_line_1    = ns_get_field('address_line_1', 'widget_' . $widget_id);
        $address_line_2    = ns_get_field('address_line_2', 'widget_' . $widget_id);
        $address_line_3    = ns_get_field('address_line_3', 'widget_' . $widget_id);
        $email_address     = ns_get_field('email_address', 'widget_' . $widget_id);
        $phone             = ns_get_field('phone', 'widget_' . $widget_id);
        $phone_numbers     = preg_replace('/[^0-9]/', '', $phone);
        $fax               = ns_get_field('fax', 'widget_' . $widget_id);
        $fax_numbers       = preg_replace('/[^0-9]/', '', $fax);
        $hide_social_icons = ns_get_field('hide_social_icons', 'widget_' . $widget_id);
        $blurb             = ns_get_field('blurb', 'widget_' . $widget_id);
        $button            = ns_get_field('button', 'widget_' . $widget_id);
        $or_link            = ns_get_field('or_link', 'widget_' . $widget_id);

        echo $args['before_widget'];
        ?>

        <?php if (! empty($title)) : ?>
            <h4 class="widget-title widgettitle"><?php echo $title; ?></h4>
        <?php endif; ?>

        <?php if ($blurb) : ?>
            <div class="blurb"><?php echo $blurb; ?></div>
        <?php endif; ?>

        <?php if (! empty($address_line_1) || ! empty($address_line_2) || ! empty($address_line_3)) : ?>
            <div class="address">
                <p>
                    <?php if (! empty($address_line_1)) : ?>
                        <?php echo $address_line_1; ?>
                        <br/>
                    <?php endif; ?>
                    <?php if (! empty($address_line_2)) : ?>
                        <?php echo $address_line_2; ?>
                        <br/>
                    <?php endif; ?>
                    <?php if (! empty($address_line_3)) : ?>
                        <?php echo $address_line_3; ?>
                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>

        <div class="email-phone">
            <?php if (! empty($email_address)) : ?>
                <div class="email">
                    <p>
                        Email: <?php echo ns_hide_email($email_address); ?>
                    </p>
                </div>
            <?php endif; ?>

            <?php if (! empty($phone) || ! empty($fax)) : ?>
                <div class="phone-fax">
                    <p>
                        <?php if (! empty($phone)) : ?>
                            Call: <a href="tel:<?php echo $phone_numbers; ?>"><?php echo $phone; ?></a>
                        <?php endif; ?>
                        <?php if (! empty($fax)) : ?>
                            <br/>
                            Fax: <a href="fax:<?php echo $fax_numbers; ?>"><?php echo $fax; ?></a>
                        <?php endif; ?>
                        <?php if (! empty($or_link)) : ?>
                            <br/>
                            <span class="f14">
                                Or <?php echo ns_link_link($or_link) ?>
                            </span>
                        <?php endif; ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($button) : ?>
            <div class="smallmt">
                <?php echo ns_link_button($button, 'light-blue-button small-button') ?>
            </div>
        <?php endif; ?>

        <?php if (! $hide_social_icons) : ?>
            <?php get_template_part('partials/social', 'links'); ?>
        <?php endif; ?>

        <?php
        echo $args['after_widget'];
    }

        /**
         * Outputs the options form on admin
         *
         * @param array $instance The widget options
         */
    public function form($instance)
    {
        ?>
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
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        return $instance;
    }
}
