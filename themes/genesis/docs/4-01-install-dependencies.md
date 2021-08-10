---
title: Install Dependencies During Genesis Theme Setup
menuTitle: Install Plugins
layout: layouts/base.njk
permalink: theme-setup/install-dependencies/index.html
minVersion: Genesis 2.9.0+ and WordPress 5.0.0+
tags: docs
---

## One-click theme setup

- <a href="{{ '/theme-setup/' | url }}">Theme setup introduction</a>.
- <a href="{{ '/theme-setup/onboarding-examples/' | url }}">See full onboarding examples</a>.
- **Install plugins**.
- <a href="{{ '/theme-setup/import-content/' | url }}">Import content</a>.
- <a href="{{ '/theme-setup/import-menus/' | url }}">Set up navigation</a>.
- <a href="{{ '/theme-setup/import-widgets/' | url }}">Import widgets</a>.
- <a href="{{ '/theme-setup/run-code/' | url }}">Run code</a>.
- <a href="{{ '/theme-setup/starter-packs/' | url }}">Create Starter Packs</a>.
- <a href="{{ '/theme-setup/theme-settings/' | url }}">Update theme settings</a>.

## Install plugins

Plugins you add as dependencies are installed and activated before content is imported.

Only plugins from the [WordPress.org repository](https://wordpress.org/plugins/) are currently supported.

Add plugins as dependencies to your onboarding config like this:

```php
<?php
/**
 * Genesis Sample.
 *
 * Onboarding config to load plugins and homepage content on theme activation.
 *
 * @package Genesis Sample
 * @author  StudioPress
 * @license GPL-2.0-or-later
 * @link    https://www.studiopress.com/
 */
return [
	'dependencies'     => [
		'plugins' => [
			[
				'name'       => __( 'Atomic Blocks', 'genesis-sample' ),
				'slug'       => 'atomic-blocks/atomicblocks.php',
				'public_url' => 'https://atomicblocks.com/',
			],
			[
				'name'       => __( 'WPForms Lite', 'genesis-sample' ),
				'slug'       => 'wpforms-lite/wpforms.php',
				'public_url' => 'https://wordpress.org/plugins/wpforms-lite/',
			],
			[
				'name'       => __( 'Genesis eNews Extended', 'genesis-sample' ),
				'slug'       => 'genesis-enews-extended/plugin.php',
				'public_url' => 'https://wordpress.org/plugins/genesis-enews-extended/',
			],
			[
				'name'       => __( 'Simple Social Icons', 'genesis-sample' ),
				'slug'       => 'simple-social-icons/simple-social-icons.php',
				'public_url' => 'https://wordpress.org/plugins/simple-social-icons/',
			],
		],
	],
	// Other config removed for this example.
	'content'          => [],
	'navigation_menus' => [],
	'widgets'          => [],
];
```

- **The plugin `slug` is the basename for the plugin's main PHP file.** This is the plugin folder name (if the plugin has a folder), a forward slash, and then the name of the PHP file containing the plugin header. It's the value returned by adding `var_dump( plugin_basename( __FILE__ ) );` to the PHP file that contains the plugin header.
- **Users can skip the onboarding process, so you should still check for plugin dependencies.** If your theme uses plugin-specific code, check for plugin classes and functions before using them with [`class_exists()`](http://php.net/manual/en/function.class-exists.php) or [`function_exists()`](http://php.net/manual/en/function.function-exists.php).
- **If a site already has one of your dependencies installed, it will be activated but not upgraded if a new plugin version is available.** Updating plugins is left to the user to prevent possible conflicts with their existing content. If your theme depends on functionality from a specific plugin version, check for that functionality using `function_exists()` or `class_exists()`, or check the plugin version in your code.