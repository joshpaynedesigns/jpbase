---
title: Create Genesis Starter Packs
menuTitle: Starter Packs
layout: layouts/base.njk
permalink: theme-setup/starter-packs/index.html
minVersion: Genesis 3.1.0+ and WordPress 5.0.0+
tags: docs
---

## One-click theme setup

- <a href="{{ '/theme-setup/' | url }}">Theme setup introduction</a>.
- <a href="{{ '/theme-setup/onboarding-examples/' | url }}">See full onboarding examples</a>.
- <a href="{{ '/theme-setup/install-dependencies/' | url }}">Install plugins</a>.
- <a href="{{ '/theme-setup/import-content/' | url }}">Import content</a>.
- <a href="{{ '/theme-setup/import-menus/' | url }}">Set up navigation</a>.
- <a href="{{ '/theme-setup/import-widgets/' | url }}">Import widgets</a>.
- <a href="{{ '/theme-setup/run-code/' | url }}">Run code</a>.
- **Create Starter Packs**.
- <a href="{{ '/theme-setup/theme-settings/' | url }}">Update theme settings</a>.

## What is a starter pack?

A starter pack is a bundle of content, plugins, menus, and widgets that will be automatically imported and set up when someone clicks the “Install Pack” button.

<img src="{{ '/img/onboarding-starter-packs.jpg' | url }}" alt="Theme setup screen showing a selection of starter packs.">

Starter packs give users a choice over the initial theme content and features that best suit their needs, without forcing them to choose between long lists of options and make many decisions during the setup process. Starter packs also allow child theme developers to market one theme to multiple audiences.

Starter packs for a single theme might differ greatly from each other, such as a “Shop”, “Blogger”, “Photographer”, and “Podcaster” pack that each offer different plugins and content for those users. Or packs can contain small differences only, such as a homepage variation.

Theme developers can choose to give users a single choice of content, or support starter packs and present multiple curated options.

## Create starter packs

A `config/onboarding.php` file for a theme that supports starter packs consists of a `starter_packs` key containing an array of packs:

```php
<?php
/**
 * Genesis Sample.
 *
 * Onboarding config to present a choice of starter packs.
 *
 * @package Genesis Sample
 * @author  StudioPress
 * @license GPL-2.0-or-later
 * @link    https://www.studiopress.com/
 */
return [
	'starter_packs' => [
		'pack-1' => [
			'title'       => __( 'Pack One', 'genesis-sample' ),
			'description' => __( 'A short description about this pack.', 'genesis-sample' ),
			'thumbnail'   => get_stylesheet_directory_uri() . '/config/import/images/thumbnails/pack-1.jpg',
			'demo_url'    => 'https://example.com/path-to-pack-one-demo/',
			'config'      => [
				'dependencies'     => [],
				'content'          => [],
				'navigation_menus' => [],
				'widgets'          => [],
			],
		],
		'pack-2' => [
			'title'       => __( 'Pack Two', 'genesis-sample' ),
			'description' => __( 'A short description about this pack.', 'genesis-sample' ),
			'thumbnail'   => get_stylesheet_directory_uri() . '/config/import/images/thumbnails/pack-2.jpg',
			'demo_url'    => 'https://example.com/path-to-pack-two-demo/',
			'config'      => [
				'dependencies'     => [],
				'content'          => [],					
				'navigation_menus' => [],
				'widgets'          => [],
			],
		],
	],
];
```

Each pack is an array containing:

- `title`: The title of the pack that you want to present to the user.
- `description`: A short description of what differentiates the pack from other packs. Users will see this if they click the pack image.
- `thumbnail`: A URL to a thumbnail showing the pack's main features. This can be any size. StudioPress themes use 1200 × 900 images.
- `demo_url`: An optional link to an offsite URL where users can see a preview of the pack.
- `config`: An array containing the <a href="{{ '/theme-setup/import-content/' | url }}">content</a>, <a href="{{ '/theme-setup/install-dependencies/' | url }}">dependencies</a>, <a href="{{ '/theme-setup/import-menus/' | url }}">menus</a>, and <a href="{{ '/theme-setup/import-widgets/' | url }}">widgets</a> specific to that pack.

## Sharing content between packs

Packs may share some content or plugins and have only small differences, such as a homepage variation.

You can repeat the config array for each pack and alter the different sections, or you can extract shared config into a separate file and use `array_merge()` to combine shared content with content that differs for each pack:

<a class="button" href="{{ '/theme-setup/onboarding-examples/' | url }}">See full examples</a>
