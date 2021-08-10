---
title: Run Code During Genesis Theme Setup
menuTitle: Run Code
layout: layouts/base.njk
permalink: theme-setup/run-code/index.html
minVersion: Genesis 2.10.0+ and WordPress 5.0.0+
tags: docs
---

## One-click theme setup

- <a href="{{ '/theme-setup/' | url }}">Theme setup introduction</a>.
- <a href="{{ '/theme-setup/onboarding-examples/' | url }}">See full onboarding examples</a>.
- <a href="{{ '/theme-setup/install-dependencies/' | url }}">Install plugins</a>.
- <a href="{{ '/theme-setup/import-content/' | url }}">Import content</a>.
- <a href="{{ '/theme-setup/import-menus/' | url }}">Set up navigation</a>.
- <a href="{{ '/theme-setup/import-widgets/' | url }}">Import widgets</a>.
- **Run code**.
- <a href="{{ '/theme-setup/starter-packs/' | url }}">Create Starter Packs</a>.
- <a href="{{ '/theme-setup/theme-settings/' | url }}">Update theme settings</a>.

## Run code before and after importing content

To run code before content is imported during theme setup, use the `genesis_onboarding_before_import_content` action:

```php
add_action( 'genesis_onboarding_before_import_content', 'theme_prefix_onboarding_before_import_content' );
/**
 * Runs code before content is imported during theme setup.
 *
 * @since 1.0.0
 *
 * @param array $content The content data from the `onboarding.php` file.
 */
function theme_prefix_onboarding_before_import_content( $content ) {

	// Code you would like to run before content is imported.

}
```

To run code after content is imported during theme setup, use the `genesis_onboarding_after_import_content` action:

```php
add_action( 'genesis_onboarding_after_import_content', 'theme_prefix_onboarding_after_import_content', 10, 2 );
/**
 * Runs code after content is imported during theme setup.
 *
 * @since 1.0.0
 *
 * @param array $content The content data from the `onboarding.php` file.
 * @param array $imported_post_ids Content keys and created post IDs. Example: `[ "homepage" => 123 ]`.
 */
function theme_prefix_onboarding_after_import_content( $content, $imported_post_ids ) {

	// Code you would like to run after content is imported.

}
```

For an example of how Genesis uses this hook, see the `genesis_onboarding_import_widgets()` function in `wp-content/themes/genesis/lib/functions/onboarding.php`.
