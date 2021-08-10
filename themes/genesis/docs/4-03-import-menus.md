---
title: Import Menus During Genesis Theme Setup
menuTitle: Import Menus
layout: layouts/base.njk
permalink: theme-setup/import-menus/index.html
minVersion: Genesis 2.9.0+ and WordPress 5.0.0+
tags: docs
---

## One-click theme setup

- <a href="{{ '/theme-setup/' | url }}">Theme setup introduction</a>.
- <a href="{{ '/theme-setup/onboarding-examples/' | url }}">See full onboarding examples</a>.
- <a href="{{ '/theme-setup/install-dependencies/' | url }}">Install plugins</a>.
- <a href="{{ '/theme-setup/import-content/' | url }}">Import content</a>.
- **Set up navigation**.
- <a href="{{ '/theme-setup/import-widgets/' | url }}">Import widgets</a>.
- <a href="{{ '/theme-setup/run-code/' | url }}">Run code</a>.
- <a href="{{ '/theme-setup/starter-packs/' | url }}">Create Starter Packs</a>.
- <a href="{{ '/theme-setup/theme-settings/' | url }}">Update theme settings</a>.

## Import menu items and assign them to menus

Use this `config/onboarding.php` format to create and assign menu items:

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
	'dependencies'     => [],
	'widgets'          => [],
	// Full content omitted for this example leaving only keys.
	'content'          => [
		'homepage' => [],
		'about'    => [],
		'contact'  => [],
		'blocks'   => [],
		'landing'  => [],
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
];
```

The keys in the `navigation_menus` array should match those from imported content.

The `primary` key above denotes that menu items should be assigned to the menu area that was registered as `primary` via `register_nav_menus()`.

Importing links as bare URLs is not currently supported.
