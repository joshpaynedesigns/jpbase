---
title: Import Content During Genesis Theme Setup
menuTitle: Import Content
layout: layouts/base.njk
permalink: theme-setup/import-content/index.html
minVersion: Genesis 2.9.0+ and WordPress 5.0.0+
tags: docs
---

## One-click theme setup

- <a href="{{ '/theme-setup/' | url }}">Theme setup introduction</a>.
- <a href="{{ '/theme-setup/onboarding-examples/' | url }}">See full onboarding examples</a>.
- <a href="{{ '/theme-setup/install-dependencies/' | url }}">Install plugins</a>.
- **Import content**.
- <a href="{{ '/theme-setup/import-menus/' | url }}">Set up navigation</a>.
- <a href="{{ '/theme-setup/import-widgets/' | url }}">Import widgets</a>.
- <a href="{{ '/theme-setup/run-code/' | url }}">Run code</a>.
- <a href="{{ '/theme-setup/starter-packs/' | url }}">Create Starter Packs</a>.
- <a href="{{ '/theme-setup/theme-settings/' | url }}">Update theme settings</a>.

## Import content

Import content and set a page as a static homepage with a `config/onboarding.php` file like this:

```php
<?php
/**
 * Genesis Sample.
 *
 * Onboarding config to load content on theme activation.
 *
 * @package Genesis Sample
 * @author  StudioPress
 * @license GPL-2.0-or-later
 * @link    https://www.studiopress.com/
 */
return [
	'content'          => [
		'homepage' => [
			'post_title'     => 'Homepage',
			'post_content'   => require dirname( __FILE__ ) . '/import/content/homepage.php',
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'page_template'  => 'page-templates/blocks.php',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		],
		'blocks'   => [
			'post_title'     => 'Block Content Examples',
			'post_content'   => require dirname( __FILE__ ) . '/import/content/block-examples.php',
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'page_template'  => 'page-templates/blocks.php',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		],
		'about'    => [
			'post_title'     => 'About Us',
			'post_content'   => require dirname( __FILE__ ) . '/import/content/about.php',
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'page_template'  => 'page-templates/blocks.php',
			'featured_image' => get_stylesheet_directory_uri() . '/config/import/images/about.jpg',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		],
		'contact'  => [
			'post_title'     => 'Contact Us',
			'post_content'   => require dirname( __FILE__ ) . '/import/content/contact.php',
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'featured_image' => get_stylesheet_directory() . '/config/import/images/contact.jpg',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		],
		'landing'  => [
			'post_title'     => 'Landing Page',
			'post_content'   => require dirname( __FILE__ ) . '/import/content/landing-page.php',
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'page_template'  => 'page-templates/landing.php',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		],
	],
	// Other config removed for this example.
	'navigation_menus' => [],
	'dependencies'     => [],
	'widgets'          => [],
];
```

## About the content array

- **The `content` array contains one or more posts and pages**. You can use any unique value for the array keys. The special 'homepage' key tells Genesis that the imported page should be set as the site's static homepage.
- **The `page_template` key and value can be omitted** if you do not wish to use a page template for a given page.
- **The value of `post_content` should be a string containing the raw HTML of the page content you wish to import**, obtained from viewing the Text or Source of your page content in the WordPress editor. As this string is likely to be long, we recommend storing it in a separate file as [described below](#use-separate-files-for-your-post_content).
- **To set a manual excerpt**, add a `post_excerpt` key to any content item array.
- **To add post meta to posts and pages**, set a `meta_input key`. For example: `'meta_input' => [ '_genesis_layout' => 'sidebar-content' ),`


## Importing posts

If you choose to import posts as well as pages, these will appear in a site's RSS feed if imported with a `post_status` of `publish` by default. This can negatively affect RSS-to-email functionality a site owner may be using.

We recommend only importing posts if a site has the WordPress `fresh_site` option set to `true`:

```php
// Set your regular page content and other config here.
$example_onboarding_config = [
	'dependencies'     => [],
	'content'          => [],
	'navigation_menus' => [],
	'widgets'          => [],
];

// Append posts you want to import to the content array only if the site 
// is a clean WordPress installation.
if ( get_option( 'fresh_site' ) ) {
	$example_onboarding_config['content']['sample-post-1'] = [
		'post_title'     => 'First post',
		'post_content'   => require dirname( __FILE__ ) . '/import/content/post-example.php',
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'featured_image' => CHILD_URL . '/config/import/images/sample-post-1.jpg',
		'comment_status' => 'closed',
		'ping_status'    => 'closed',
	];
	$example_onboarding_config['content']['sample-post-2'] = [
		'post_title'     => 'Second post',
		'post_content'   => require dirname( __FILE__ ) . '/import/content/post-example.php',
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'featured_image' => CHILD_URL . '/config/import/images/sample-post-2.jpg',
		'comment_status' => 'closed',
		'ping_status'    => 'closed',
	];	
}

return $example_onboarding_config;
```

## Use separate files for your `post_content`

We recommend storing page or post content you wish to import in a separate file.

Instead of including a long string in your `onboarding.php` config, like this:

```php
'post_content' => '<!-- wp:paragraph --><p>This is a simple paragraph block, but your imported content can contain much more exciting content than this.</p><!-- /wp:paragraph -->';
```

You can require a file that returns the string instead, like this:

```php
'post_content' => require dirname( __FILE__ ) . '/homepage.php',
```

This `homepage.php` file can be named as you wish. It can live in your `config` directory, but you may wish to place it in a subdirectory such as `config/import/`:

```php
'post_content' => require dirname( __FILE__ ) . '/import/homepage.php',
```

The `homepage.php` file must return a string containing the content you wish to import:

```php
<?php
/**
 * Your Theme Name
 *
 * Homepage content optionally installed after theme activation.
 *
 * @package Theme Name
 * @author  Your Name
 * @license GPL-2.0-or-later
 * @link    https://example.com/
 */

$theme_name_homepage_content = <<<CONTENT
<!-- wp:paragraph -->
<p>This is a simple paragraph block, but your imported content can contain much more exciting content than this.</p>
<!-- /wp:paragraph -->
CONTENT;

return $theme_name_homepage_content;
```

Note that the string is returned on the final line. If you omit the return statement, no content will be imported during theme setup and your pages will be blank.

Your raw page content can be copied from the code editor. The code editor is accessible from the menu in the top-right of the block editor:

<img src="{{ '/img/code-editor.png' | url }}" alt="The menu from the block editor showing how to access the code editor mode.">

<p class="notice-small">
The opening <code>&lt;&lt;&lt;CONTENT</code> and closing <code>CONTENT;</code> string delimiters in the <code>homepage.php</code> code snippet are a PHP feature called the <a href="http://php.net/manual/en/language.types.string.php#language.types.string.syntax.heredoc">heredoc syntax</a>.
<br><br>The heredoc syntax is an alternative to wrapping multi-line strings with quote marks. It prevents you from having to escape single or double quotes within your string. PHP will also process internal variables such as <code>$my_var</code> or <code>{$my_array['key']}</code>.
<br><br>You can replace the <code>CONTENT</code> identifier with your own delimiter if you wish, as long as you use the same for the starting and ending one. The line with the ending <code>CONTENT;</code> delimiter must contain no other characters aside from the identifier and the semicolon, and no white space at the start of the line.
</p>

Each page or post you create can import the same sample content, or you can create a separate file with different content for each page, then update the `post_content` value for each imported page to point to that file.

## Importing featured images in local development environments

Featured images may fail to import in local development environments that use HTTPS.

This is usually due to a [known issue with PHP 7.1, curl, and self-signed certificates](https://github.com/laravel/valet/issues/460).

If you encounter this, you can try testing locally with HTTP or testing in an HTTPS environment with a certificate signed by a certificate authority. This issue should not affect sites in production environments.
