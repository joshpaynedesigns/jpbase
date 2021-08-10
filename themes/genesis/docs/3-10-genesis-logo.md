---
title: Genesis Logo Output
menuTitle: Logo Output
layout: layouts/base.njk
permalink: developer-features/logo-output/index.html
tags: docs
minVersion: Genesis 3.1.0+
---

## Add support for site logos with automatic logo output
The `genesis-custom-logo` theme support gives users the option to set a custom logo, outputs the custom logo, improves site heading accessibility and removes unneeded Customizer settings with minimal code.

This is the Genesis equivalent of the WordPress [custom-logo support](https://developer.wordpress.org/themes/functionality/custom-logo/#adding-custom-logo-support-to-your-theme), but it outputs the logo for you, helps you to avoid some common traps and reduces the amount of code you need for logo support in your themes.

Add support like this:

```php
add_theme_support(
    'genesis-custom-logo',
    [
        'height'      => 120,
        'width'       => 700,
        'flex-height' => true,
        'flex-width'  => true,
    ]
);
```

The parameters are the same as those for [WordPress custom-logo support](https://developer.wordpress.org/themes/functionality/custom-logo/#adding-custom-logo-support-to-your-theme).

## Benefits of `genesis-custom-logo`
Adding logo support via Genesis does four helpful things for your theme:

1. Users will be able to change their site’s logo in the Customizer in the Site Identity section.
2. Genesis will output the logo for you. You do not need to add `the_custom_logo()` function to your child theme to output the logo.
3. The Theme <em>Settings → Header</em> Customizer section will be removed to prevent confusion about where the site logo should be set. (This is the panel that contains the “Dynamic Title/Image Logo” select field.)
4. Genesis will remove the link around the site title (the site logo already has a link). This prevents the site title from capturing keyboard focus when it is hidden with CSS once the logo is in use, reducing confusion and improving accessibility.

## Prevent logo output
You can suppress output of the custom logo from a plugin or other custom code if needed:

```php
remove_action( 'after_setup_theme', 'genesis_output_custom_logo', 11 );
```

## Change the logo position
Genesis outputs the logo on the `genesis_site_title` hook with priority 0 by default. You will not normally need to change this, but you can adjust the position if desired:

```php
remove_action( 'after_setup_theme', 'genesis_output_custom_logo', 11 );
add_action( 'after_setup_theme', 'theme_prefix_output_custom_logo', 11 );
/**
 * Adds the WordPress custom logo inside a custom area.
 */
function theme_prefix_output_custom_logo() {

	if ( current_theme_supports( 'genesis-custom-logo' ) ) {
		add_action( 'genesis_[chosen_location]', 'the_custom_logo', 0 );
	}

}
```

## Output the logo a second time in an additional location
Display the logo somewhere else in addition to the header, such as in the footer:

```php
add_action( 'after_setup_theme', 'theme_prefix_output_custom_logo_footer', 11 );
/**
 * Adds the WordPress custom logo to the footer.
 */
function theme_prefix_output_custom_logo_footer() {
	if ( current_theme_supports( 'genesis-custom-logo' ) ) {
		add_action( 'genesis_footer', 'the_custom_logo', 11 );
	}
}
```

When moving the logo or adding a second logo, you may need to write CSS to adjust the styling of your logos. To get help with this, see the <a href="{{ '/contribute/community/' | url }}">community resources</a>.
