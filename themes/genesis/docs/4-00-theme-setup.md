---
title: Genesis One-Click Theme Setup
menuTitle: Theme Setup
layout: layouts/base.njk
permalink: theme-setup/index.html
minVersion: Genesis 2.9.0+ and WordPress 5.0.0+
tags: docs
---

## One-click theme setup helps users to recreate theme demos

Genesis one-click theme setup reduces frustrating manual theme setup by offering to automate parts of the theme setup process when a Genesis child theme is activated.

When activating a Genesis child theme with one-click theme setup support, users are redirected to a setup page:

<img src="{{ '/img/onboarding.png' | url }}" alt="Theme setup screen showing the Create Your New Homepage prompt.">

Themes that support <a href="{{ '/theme-setup/starter-packs/' | url }}">Genesis Starter Packs</a> present users with a choice of starting content. They can choose the pack that best meets their needs:

<img src="{{ '/img/onboarding-starter-packs.jpg' | url }}" alt="Theme setup screen showing a selection of starter packs.">

## Actions theme developers can take during theme setup

Your child theme's `onboarding.php` config file determines what happens during the Theme Setup process. You can:

- <a href="{{ '/theme-setup/install-dependencies/' | url }}">Set plugins to install and activate</a>.
- <a href="{{ '/theme-setup/import-content/' | url }}">Import content</a> and set a static homepage. 
- <a href="{{ '/theme-setup/import-widgets/' | url }}">Import widgets</a>.
- <a href="{{ '/theme-setup/import-menus/' | url }}">Import menu items</a>.
- <a href="{{ '/theme-setup/run-code/' | url }}">Run code</a>.
- <a href="{{ '/theme-setup/theme-settings/' | url }}">Update theme settings</a>.

## Add one-click theme setup to your Genesis child theme

Add an `onboarding.php` file to your Genesis child theme's `config` folder. The file must be named `onboarding.php` or Genesis will not present the theme setup screen when the theme is activated.

<p class="notice-small">
Create the <code>config</code> folder in the root of your child theme (at the same level as <code>style.css</code>) if it does not exist already.
</p>

The onboarding file must return an array with keys for each onboarding task.

<a class="button" href="{{ '/theme-setup/onboarding-examples/' | url }}">See full onboarding examples</a>

## Basic onboarding.php structure

For regular theme setup with only one choice of content, the `onboarding.php` file takes this structure:

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
		'plugins' => [],
	],
	'content'          => [],
	'navigation_menus' => [],
	'widgets'          => [],
];
```

<a href="{{ '/theme-setup/onboarding-examples/' | url }}">See a complete example here</a>.

## Starter packs onboarding.php structure

Themes that support Starter Packs to enable a choice of content use an `onboarding.php` file with this structure:

```php
<?php
/**
 * Genesis Sample.
 *
 * Onboarding config to present a choice of Starter Packs.
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

<a href="{{ '/theme-setup/onboarding-examples/#starter-packs-with-shared-content' | url }}">See a complete Starter Packs example here</a>.

## To test the theme setup process

1. Activate another theme from the Appearance → Themes page.
2. Activate your theme. You should be redirected to the theme setup page.
3. Click “Set up your homepage” or choose a starter pack and wait for the setup steps to complete.
4. Click “View your homepage” or “Edit your homepage” to see the imported homepage content.

You can repeat the theme setup process by leaving your theme active and visiting Genesis → Child Theme Setup or `/wp-admin/admin.php?page=genesis-getting-started` instead of deactivating and reactivating your theme. Note that new pages and menus will be created each time. Pages and menus are not deleted or overwritten during theme setup.

## Theme setup order

The order of operation for the theme setup process is:

1. Dependencies (plugins) are installed.
2. Actions hooked to `genesis_onboarding_before_import_content` run.
3. Content is imported.
4. Actions hooked to `genesis_onboarding_after_import_content` run. This includes widget import. 
5. Menu items are set and assigned to menu areas.

## Points to note

1. **One-click theme setup requires WordPress 5.0.0+ and Genesis 2.9.0+**. The onboarding config file has no effect if both of these requirements are not met. Widget import requires Genesis 3.1.0+.
2. **The redirect to the theme setup page occurs when themes are activated via the Appearance → Themes screen only**. A redirect will not occur when activating themes via the Customizer or WP-CLI. You are welcome to direct people to the *Genesis → Child Theme Setup* menu item or `/wp-admin/admin.php?page=genesis-getting-started` URL to complete the theme onboarding process in your support documentation or elsewhere. Running the setup process multiple times will create additional pages, but is otherwise not destructive.
3. **The text used on the theme setup admin screen is not currently filterable,** including the “Create your new homepage” title.
4. **Only plugins from the [WordPress.org plugins repository](https://wordpress.org/plugins/) are currently supported** as dependencies.
5. **Blocks included in imported content that display posts can not be guaranteed to appear in the same way as your demo content**. It's not safe to assume that all of a site's posts have a featured image set, for example, or that a site contains any posts.

## Learn more about one-click theme setup

- <a href="{{ '/theme-setup/onboarding-examples/' | url }}">See full onboarding examples</a>.
- <a href="{{ '/theme-setup/install-dependencies/' | url }}">Install plugins</a>.
- <a href="{{ '/theme-setup/import-content/' | url }}">Import content</a>.
- <a href="{{ '/theme-setup/import-menus/' | url }}">Set up navigation</a>.
- <a href="{{ '/theme-setup/import-widgets/' | url }}">Import widgets</a>.
- <a href="{{ '/theme-setup/run-code/' | url }}">Run code</a>.
- <a href="{{ 'theme-setup/starter-packs/' | url }}">Create Starter Packs</a>.
- <a href="{{ '/theme-setup/theme-settings/' | url }}">Update theme settings</a>.
