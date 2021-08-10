---
title: Set Theme Settings During Activation
menuTitle: Theme Settings
layout: layouts/base.njk
permalink: theme-setup/theme-settings/index.html
minVersion: Genesis 2.9.0+
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
- <a href="{{ '/theme-setup/starter-packs/' | url }}">Create Starter Packs</a>.
- **Update theme settings**.

## Set theme settings during activation

Create a `config/child-theme-settings.php` file to set theme settings during activation:

```php
<?php
/**
 * Genesis Sample theme settings.
 *
 * Genesis 2.9+ updates these settings when themes are activated.
 *
 * @package Genesis Sample
 * @author  StudioPress
 * @license GPL-2.0-or-later
 * @link    https://www.studiopress.com/
 */

return [
	GENESIS_SETTINGS_FIELD => [
		'blog_cat_num'              => 6,
		'breadcrumb_home'           => 0,
		'breadcrumb_front_page'     => 0,
		'breadcrumb_posts_page'     => 0,
		'breadcrumb_single'         => 0,
		'breadcrumb_page'           => 0,
		'breadcrumb_archive'        => 0,
		'breadcrumb_404'            => 0,
		'breadcrumb_attachment'     => 0,
		'content_archive'           => 'full',
		'content_archive_limit'     => 0,
		'content_archive_thumbnail' => 0,
		'image_size'                => 'genesis-singular-images',
		'image_alignment'           => 'aligncenter',
		'posts_nav'                 => 'numeric',
		'site_layout'               => 'content-sidebar',
		'footer_text'               => 'Default theme footer text.'
	],
	'posts_per_page'       => 6,
];
```

<p class="notice">Theme settings import is attached to the <code>after_switch_theme</code> hook. It runs on theme activation independently from the theme setup or starter packs selection process.</p>