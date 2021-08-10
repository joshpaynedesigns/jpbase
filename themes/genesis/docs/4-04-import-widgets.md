---
title: Import Widgets During Genesis Theme Setup
menuTitle: Import Widgets
layout: layouts/base.njk
permalink: theme-setup/import-widgets/index.html
minVersion: Genesis 3.1.0+ and WordPress 5.0.0+
tags: docs
---

## One-click theme setup

- <a href="{{ '/theme-setup/' | url }}">Theme setup introduction</a>.
- <a href="{{ '/theme-setup/onboarding-examples/' | url }}">See full onboarding examples</a>.
- <a href="{{ '/theme-setup/install-dependencies/' | url }}">Install plugins</a>.
- <a href="{{ '/theme-setup/import-content/' | url }}">Import content</a>.
- <a href="{{ '/theme-setup/import-menus/' | url }}">Set up navigation</a>.
- **Import widgets**.
- <a href="{{ '/theme-setup/run-code/' | url }}">Run code</a>.
- <a href="{{ '/theme-setup/starter-packs/' | url }}">Create Starter Packs</a>.
- <a href="{{ '/theme-setup/theme-settings/' | url }}">Update theme settings</a>.

## Import widgets

Import any widget using a `config/onboarding.php` file like this:

```php
<?php
/**
 * Genesis Sample.
 *
 * Onboarding config to load content and navigation on theme activation.
 *
 * @package Genesis Sample
 * @author  StudioPress
 * @license GPL-2.0-or-later
 * @link    https://www.studiopress.com/
 */
return [
	// Full config omitted for this example.
	'dependencies'     => [],
	'content'          => [],
	'navigation_menus' => [],
	'widgets'          => [
		'footer-1' => [
			[
				'type' => 'featured-post',
				'args' => [
					'title'                   => 'Featured Posts Example',
					'posts_cat'               => '0',
					'posts_num'               => 3,
					'posts_offset'            => '0',
					'orderby'                 => 'date',
					'order'                   => 'DESC',
					'gravatar_size'           => '45',
					'gravatar_alignment'      => 'alignnone',
					'image_size'              => 'thumbnail',
					'image_alignment'         => 'alignnone',
					'show_title'              => '1',
					'show_byline'             => '1',
					'post_info'               => '[post_date] By [post_author_posts_link] [post_comments]',
					'show_content'            => 'excerpt',
					'content_limit'           => '0',
					'more_text'               => '[Read More...]',
					'extra_title'             => '',
					'extra_num'               => '',
					'more_from_category_text' => 'More Posts from this Category',
				],
			],
		],
		'footer-2' => [
			[
				'type' => 'featured-listings',
				'args' => [
					'title'          => 'Featured Listings Example',
					'posts_per_page' => '5',
				],
			],
		],
		'footer-3' => [
			[
				'type' => 'enews-ext',
				'args' => [
					'title'       => 'eNews Extended Example',
					'action'      => '#',
					'email-field' => '#',
					'input_text'  => 'Enter Email Address ...',
					'button_text' => 'Subscribe',
				],
			],
		],
	],
];
```

The `footer-n` keys denote the widget area to import widgets into, as defined by Genesis or your theme.

You can import multiple widgets into a single widget area:

```php
'footer-1' => [
	// The first text widget.
	[
		'type' => 'text',
		'args' => [
			'title'  => 'Text widget 1',
			'text'   => '<p>With an emphasis on typography, white space, and mobile-optimized design, your website will look absolutely breathtaking.</p><p><a href="#">Learn more about design</a>.</p>',
			'filter' => 1,
			'visual' => 1,
		],
	],
	// The second text widget.
	[
		'type' => 'text',
		'args' => [
			'title'  => 'Text widget 2',
			'text'   => '<p>Our team will teach you the art of writing audience-focused content that will help you achieve the success you truly deserve.</p><p><a href="#">Learn more about content</a>.</p>',
			'filter' => 1,
			'visual' => 1,
		],
	],
],

```


WordPress stores widget config as serialized arrays in the `wp_options` table.

You can retrieve fields you need to use for each widget type via [WP-CLI](https://wp-cli.org/):

```
> wp option get widget_text
> wp option get widget_enews-extended
> wp option get widget_featured-post
```

These commands reveal widget properties you can import:

```
> wp option get widget_featured-post
…
array (
    'title' => 'Fashion',
    'posts_num' => '2',
    'posts_offset' => '1',
    'orderby' => 'ID',
    'show_image' => 1,
    'image_size' => 'sidebar-thumbnail',
    'image_alignment' => 'alignleft',
    'show_title' => 1,
    'show_byline' => 1,
    'post_info' => '[post_date] By [post_author_posts_link]',
    'show_content' => '',
  ),
…
```

You can alternatively inspect widget data using an SQL client by browsing the `wp_options` table and viewing the `widget_` rows. Or browse the widget source code and find the default properties in the class constructor:

```php
// wp-content/themes/genesis/lib/widgets/featured-post-widget.php.
$this->defaults = [
	'title'                   => '',
	'posts_cat'               => '',
	'posts_num'               => '',
	'posts_offset'            => 0,
	'orderby'                 => '',
	'order'                   => '',
	'exclude_displayed'       => 0,
	'exclude_sticky'          => 0,
	'show_image'              => 0,
	'image_alignment'         => '',
	'image_size'              => '',
	'show_gravatar'           => 0,
	'gravatar_alignment'      => '',
	'gravatar_size'           => '',
	'show_title'              => 0,
	'show_byline'             => 0,
	'post_info'               => '[post_date] ' . __( 'By', 'genesis' ) . ' [post_author_posts_link] [post_comments]',
	'show_content'            => 'excerpt',
	'content_limit'           => '',
	'more_text'               => __( '[Read More...]', 'genesis' ),
	'extra_num'               => '',
	'extra_title'             => '',
	'more_from_category'      => '',
	'more_from_category_text' => __( 'More Posts from this Category', 'genesis' ),
];
```

You only need to specify widget properties if they differ from defaults for that widget.

## Use imported page IDs as widget properties

Genesis imports content from your `onboarding.php` file before importing widgets. Content you import during theme setup is assigned a page ID by WordPress. 

You can access imported content IDs in widget properties with the `$imported_posts_[page_key]` placeholder. For example, if you import an about page in your `onboarding.php` content array like this:

```php
// Excerpt from child-theme/config/onboarding.php.
'content' => [
	'about'    => [
		'post_title'     => 'About Us',
		'post_content'   => require dirname( __FILE__ ) . '/import/content/about.php',
		'post_type'      => 'page',
		'post_status'    => 'publish',		
		'featured_image' => get_stylesheet_directory_uri() . '/config/import/images/about.jpg',
		'comment_status' => 'closed',
		'ping_status'    => 'closed',
	],
],
```

You can use this about page in a Featured Page widget like this:

```php
// Excerpt from child-theme/config/onboarding.php.
'widgets' => [
	'footer-1' => [
		[
			'type' => 'featured-page',
			'args' => [
				'title'           => '',
				'page_id'         => '$imported_posts_about', // <-- Use the ID of the imported about page.
				'show_image'      => 1,
				'image_size'      => 'featured-image',
				'image_alignment' => 'aligncenter',
				'show_title'      => 1,
				'content_limit'   => '',
				'more_text'       => '',
			],
		],
	],
],
```

Genesis will replace `$imported_posts_about` with the page ID of the imported `about` page when creating this `featured-page` widget. Replace `about` with other content keys to use the ID of different imported pages, such as `$imported_posts_contact`.
