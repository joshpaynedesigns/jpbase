---
title: Genesis Onboarding File Examples
menuTitle: Full Examples
layout: layouts/base.njk
permalink: theme-setup/onboarding-examples/index.html
minVersion: Genesis 2.9.0+ and WordPress 5.0.0+
tags: docs
---

## One-click theme setup

- <a href="{{ '/theme-setup/' | url }}">Theme setup introduction</a>.
- **See full onboarding examples**.
- <a href="{{ '/theme-setup/install-dependencies/' | url }}">Install plugins</a>.
- <a href="{{ '/theme-setup/import-content/' | url }}">Import content</a>.
- <a href="{{ '/theme-setup/import-menus/' | url }}">Set up navigation</a>.
- <a href="{{ '/theme-setup/import-widgets/' | url }}">Import widgets</a>.
- <a href="{{ '/theme-setup/run-code/' | url }}">Run code</a>.
- <a href="{{ '/theme-setup/starter-packs/' | url }}">Create Starter Packs</a>.
- <a href="{{ '/theme-setup/theme-settings/' | url }}">Update theme settings</a>.

## Example onboarding.php files

### Theme setup with a single choice of content

```php
<?php
/**
 * Genesis Sample - config/onboarding.php.
 *
 * Onboarding config shared between Starter Packs.
 *
 * Genesis Starter Packs give you a choice of content variation when activating
 * the theme. The content below is common to all packs for this theme.
 *
 * @package Genesis Sample
 * @author  StudioPress
 * @license GPL-2.0-or-later
 * @link    https://www.studiopress.com/
 */

return [
	'plugins'          => [
		[
			'name'       => __( 'Atomic Blocks', 'genesis-sample' ),
			'slug'       => 'atomic-blocks/atomicblocks.php',
			'public_url' => 'https://atomicblocks.com/',
		],
		[
			'name'       => __( 'Simple Social Icons', 'genesis-sample' ),
			'slug'       => 'simple-social-icons/simple-social-icons.php',
			'public_url' => 'https://wordpress.org/plugins/simple-social-icons/',
		],
		[
			'name'       => __( 'Genesis eNews Extended (Third Party)', 'genesis-sample' ),
			'slug'       => 'genesis-enews-extended/plugin.php',
			'public_url' => 'https://wordpress.org/plugins/genesis-enews-extended/',
		],
		[
			'name'       => __( 'WPForms Lite (Third Party)', 'genesis-sample' ),
			'slug'       => 'wpforms-lite/wpforms.php',
			'public_url' => 'https://wordpress.org/plugins/wpforms-lite/',
		],
	],
	'content'          => [
		'blocks'  => [
			'post_title'     => 'Block Content Examples',
			'post_content'   => require dirname( __FILE__ ) . '/import/content/block-examples.php',
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'meta_input'     => [ '_genesis_layout' => 'full-width-content' ],
			],
		'about'   => [
			'post_title'     => 'About Us',
			'post_content'   => require dirname( __FILE__ ) . '/import/content/about.php',
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'featured_image' => CHILD_URL . '/config/import/images/about.jpg',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'meta_input'     => [ '_genesis_layout' => 'full-width-content' ],
		],
		'contact' => [
			'post_title'     => 'Contact Us',
			'post_content'   => require dirname( __FILE__ ) . '/import/content/contact.php',
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		],
		'landing' => [
			'post_title'     => 'Landing Page',
			'post_content'   => require dirname( __FILE__ ) . '/import/content/landing-page.php',
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'page_template'  => 'page-templates/landing.php',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		],
	],
	'navigation_menus' => [
		'primary' => [
			'homepage' => [
				'title' => 'Home',
			],
			'about'    => [
				'title' => 'About Us',
			],
			'contact'  => [
				'title' => 'Contact Us',
			],
			'blocks'   => [
				'title' => 'Block Examples',
			],
			'landing'  => [
				'title' => 'Landing Page',
			],
		],
	],
	'widgets'          => [
		'footer-1' => [
			[
				'type' => 'text',
				'args' => [
					'title'  => 'Design',
					'text'   => '<p>With an emphasis on typography, white space, and mobile-optimized design, your website will look absolutely breathtaking.</p><p><a href="#">Learn more about design</a>.</p>',
					'filter' => 1,
					'visual' => 1,
				],
			],
		],
		'footer-2' => [
			[
				'type' => 'text',
				'args' => [
					'title'  => 'Content',
					'text'   => '<p>Our team will teach you the art of writing audience-focused content that will help you achieve the success you truly deserve.</p><p><a href="#">Learn more about content</a>.</p>',
					'filter' => 1,
					'visual' => 1,
				],
			],
		],
		'footer-3' => [
			[
				'type' => 'text',
				'args' => [
					'title'  => 'Strategy',
					'text'   => '<p>We help creative entrepreneurs build their digital business by focusing on three key elements of a successful online platform.</p><p><a href="#">Learn more about strategy</a>.</p>',
					'filter' => 1,
					'visual' => 1,
				],
			],
		],
	],
];
```

### Starter packs with shared content

This `onboarding.php` file pulls in shared content from another config file, then uses `array_merge()` to combine it with content that is unique to each pack. This prevents repetition of identical config for each pack:

```php
<?php
/**
 * Genesis Sample - config/onboarding.php.
 *
 * Onboarding config to load plugins and homepage content on theme activation.
 *
 * @package Genesis Sample
 * @author  StudioPress
 * @license GPL-2.0-or-later
 * @link    https://www.studiopress.com/
 */
$genesis_sample_shared_content = genesis_get_config( 'onboarding-shared' );

return [
	'starter_packs' => [
		'black-white' => [
			'title'       => __( 'Black & White', 'genesis-sample' ),
			'description' => __( 'A pack with a homepage designed with black and white images.', 'genesis-sample' ),
			'thumbnail'   => get_stylesheet_directory_uri() . '/config/import/images/thumbnails/home-black-white.jpg',
			'demo_url'    => 'https://demo.studiopress.com/genesis-sample/',
			'config'      => [
				'dependencies'     => [
					'plugins' => $genesis_sample_shared_content['plugins'],
				],
				'content'          => array_merge(
					[
						'homepage' => [
							'post_title'     => 'Homepage',
							'post_content'   => require dirname( __FILE__ ) . '/import/content/home-black-white.php',
							'post_type'      => 'page',
							'post_status'    => 'publish',
							'comment_status' => 'closed',
							'ping_status'    => 'closed',
							'meta_input'     => [
								'_genesis_layout'     => 'full-width-content',
								'_genesis_hide_title' => true,
								'_genesis_hide_breadcrumbs' => true,
								'_genesis_hide_singular_image' => true,
							],
						],
					],
					$genesis_sample_shared_content['content']
				),
				'navigation_menus' => $genesis_sample_shared_content['navigation_menus'],
				'widgets'          => $genesis_sample_shared_content['widgets'],
			],
		],
		'color'       => [
			'title'       => __( 'Color', 'genesis-sample' ),
			'description' => __( 'A pack with a homepage designed with color images.', 'genesis-sample' ),
			'thumbnail'   => get_stylesheet_directory_uri() . '/config/import/images/thumbnails/home-color.jpg',
			'demo_url'    => 'https://demo.studiopress.com/genesis-sample/home-color/',
			'config'      => [
				'dependencies'     => [
					'plugins' => $genesis_sample_shared_content['plugins'],
				],
				'content'          => array_merge(
					[
						'homepage' => [
							'post_title'     => 'Homepage',
							'post_content'   => require dirname( __FILE__ ) . '/import/content/home-color.php',
							'post_type'      => 'page',
							'post_status'    => 'publish',
							'comment_status' => 'closed',
							'ping_status'    => 'closed',
							'meta_input'     => [
								'_genesis_layout'     => 'full-width-content',
								'_genesis_hide_title' => true,
								'_genesis_hide_breadcrumbs' => true,
								'_genesis_hide_singular_image' => true,
							],
						],
					],
					$genesis_sample_shared_content['content']
				),
				'navigation_menus' => $genesis_sample_shared_content['navigation_menus'],
				'widgets'          => $genesis_sample_shared_content['widgets'],
			],
		],
	],
];
```

The `config/onboarding-shared.php` file looks like this:

```php
<?php
/**
 * Genesis Sample - config/onboarding-shared.php.
 *
 * Onboarding config shared between Starter Packs.
 *
 * Genesis Starter Packs give you a choice of content variation when activating
 * the theme. The content below is common to all packs for this theme.
 *
 * @package Genesis Sample
 * @author  StudioPress
 * @license GPL-2.0-or-later
 * @link    https://www.studiopress.com/
 */

return [
	'plugins'          => [
		[
			'name'       => __( 'Atomic Blocks', 'genesis-sample' ),
			'slug'       => 'atomic-blocks/atomicblocks.php',
			'public_url' => 'https://atomicblocks.com/',
		],
		[
			'name'       => __( 'Simple Social Icons', 'genesis-sample' ),
			'slug'       => 'simple-social-icons/simple-social-icons.php',
			'public_url' => 'https://wordpress.org/plugins/simple-social-icons/',
		],
		[
			'name'       => __( 'Genesis eNews Extended (Third Party)', 'genesis-sample' ),
			'slug'       => 'genesis-enews-extended/plugin.php',
			'public_url' => 'https://wordpress.org/plugins/genesis-enews-extended/',
		],
		[
			'name'       => __( 'WPForms Lite (Third Party)', 'genesis-sample' ),
			'slug'       => 'wpforms-lite/wpforms.php',
			'public_url' => 'https://wordpress.org/plugins/wpforms-lite/',
		],
	],
	'content'          => [
		'blocks'  => [
			'post_title'     => 'Block Content Examples',
			'post_content'   => require dirname( __FILE__ ) . '/import/content/block-examples.php',
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'meta_input'     => [ '_genesis_layout' => 'full-width-content' ],
		],
		'about'   => [
			'post_title'     => 'About Us',
			'post_content'   => require dirname( __FILE__ ) . '/import/content/about.php',
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'featured_image' => CHILD_URL . '/config/import/images/about.jpg',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'meta_input'     => [
				'_genesis_layout'              => 'full-width-content',
				'_genesis_hide_singular_image' => true,
			],
		],
		'contact' => [
			'post_title'     => 'Contact Us',
			'post_content'   => require dirname( __FILE__ ) . '/import/content/contact.php',
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		],
		'landing' => [
			'post_title'     => 'Landing Page',
			'post_content'   => require dirname( __FILE__ ) . '/import/content/landing-page.php',
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'page_template'  => 'page-templates/landing.php',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'meta_input'     => [
				'_genesis_layout'              => 'full-width-content',
				'_genesis_hide_breadcrumbs'    => true,
				'_genesis_hide_singular_image' => true,
			],
		],
	],
	'navigation_menus' => [
		'primary' => [
			'homepage' => [
				'title' => 'Home',
			],
			'about'    => [
				'title' => 'About Us',
			],
			'contact'  => [
				'title' => 'Contact Us',
			],
			'blocks'   => [
				'title' => 'Block Examples',
			],
			'landing'  => [
				'title' => 'Landing Page',
			],
		],
	],
	'widgets'          => [
		'footer-1' => [
			[
				'type' => 'text',
				'args' => [
					'title'  => 'Design',
					'text'   => '<p>With an emphasis on typography, white space, and mobile-optimized design, your website will look absolutely breathtaking.</p><p><a href="#">Learn more about design</a>.</p>',
					'filter' => 1,
					'visual' => 1,
				],
			],
		],
		'footer-2' => [
			[
				'type' => 'text',
				'args' => [
					'title'  => 'Content',
					'text'   => '<p>Our team will teach you the art of writing audience-focused content that will help you achieve the success you truly deserve.</p><p><a href="#">Learn more about content</a>.</p>',
					'filter' => 1,
					'visual' => 1,
				],
			],
		],
		'footer-3' => [
			[
				'type' => 'text',
				'args' => [
					'title'  => 'Strategy',
					'text'   => '<p>We help creative entrepreneurs build their digital business by focusing on three key elements of a successful online platform.</p><p><a href="#">Learn more about strategy</a>.</p>',
					'filter' => 1,
					'visual' => 1,
				],
			],
		],
	],
];
```

You can change the name of the `config/onboarding-shared.php` file if you wish, as long as you update the `genesis_get_config( 'onboarding-shared' );` line in your `config/onboarding.php` file to match.

It is only the main `config/onboarding.php` file that can not be renamed.
