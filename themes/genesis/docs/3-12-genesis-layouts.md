---
title: Genesis Layouts
menuTitle: Layouts
layout: layouts/base.njk
permalink: developer-features/genesis-layouts/index.html
tags: docs
---

Genesis presents users with a choice of page layout variations in the admin area:

## Layout Settings

### Site default

Available at <em>Customize → Theme Settings → Site Layout</em>:

<img src="{{ '/img/settings-site-default-layout.png' | url }}" alt="The Genesis site default layout setting in the Customizer.">

### Archive layouts

Displayed in archive settings for post types with `genesis-layouts` post type support:

<img src="{{ '/img/settings-archive-layout.png' | url }}" alt="The Genesis archive layout options in the WordPress admin area.">

### Author layouts

Available in author profile settings:

<img src="{{ '/img/settings-author-layout.png' | url }}" alt="The Genesis author layout options in the WordPress admin area.">

### Page layouts

The Block Editor shows a layout selector in the Genesis sidebar for post types with both `genesis-layouts` and `custom-fields` support:

<img src="{{ '/img/settings-block-editor-layout.jpg' | url }}" alt="The Genesis page layout options in the block editor within the Genesis sidebar.">

The Classic Editor shows a “Layout Settings” meta box below post content:

<img src="{{ '/img/settings-classic-editor-layout.jpg' | url }}" alt="The Genesis page layout options in the classic editor within the Layout Settings meta box.">

## Default Genesis layouts

Genesis registers common layouts for all Genesis child themes in `genesis_create_initial_layouts()`:

- content-sidebar
- sidebar-content
- content-sidebar-sidebar
- sidebar-content-sidebar
- full-width-content

Default layouts come from the config file at `genesis/config/layouts.php`:

```php
$url = GENESIS_ADMIN_IMAGES_URL . '/layouts/'; // Resolves to genesis/lib/admin/images/layouts/.

return [
	'content-sidebar'         => [
		'label'   => __( 'Content, Primary Sidebar', 'genesis' ),
		'img'     => $url . 'cs.gif',
		'default' => is_rtl() ? false : true,
		'type'    => [ 'site' ],
	],
	'sidebar-content'         => [
		'label'   => __( 'Primary Sidebar, Content', 'genesis' ),
		'img'     => $url . 'sc.gif',
		'default' => is_rtl() ? true : false,
		'type'    => [ 'site' ],
	],
	'content-sidebar-sidebar' => [
		'label' => __( 'Content, Primary Sidebar, Secondary Sidebar', 'genesis' ),
		'img'   => $url . 'css.gif',
		'type'  => [ 'site' ],
	],
	'sidebar-sidebar-content' => [
		'label' => __( 'Secondary Sidebar, Primary Sidebar, Content', 'genesis' ),
		'img'   => $url . 'ssc.gif',
		'type'  => [ 'site' ],
	],
	'sidebar-content-sidebar' => [
		'label' => __( 'Secondary Sidebar, Content, Primary Sidebar', 'genesis' ),
		'img'   => $url . 'scs.gif',
		'type'  => [ 'site' ],
	],
	'full-width-content'      => [
		'label' => __( 'Full Width Content', 'genesis' ),
		'img'   => $url . 'c.gif',
		'type'  => [ 'site' ],
	],
];
```

## Unregister existing layouts

Some developers unregister default Genesis layouts, such as the double sidebar layouts:

```php
genesis_unregister_layout( 'content-sidebar-sidebar' );
genesis_unregister_layout( 'sidebar-content-sidebar' );
genesis_unregister_layout( 'sidebar-sidebar-content' );
```

<p class="notice-small">
If you remove all layouts that use the Genesis alternative sidebar, you should unregister that sidebar with <code>unregister_sidebar( 'sidebar-alt' );</code>.
</p>

## Register additional layouts
Register Genesis layouts with `genesis_register_layout()`.

### Example from Authority Pro

The [Authority Pro theme](https://my.studiopress.com/themes/authority/) adds a new `authority-grid` custom layout to the `category` and `post_tag` types:

```php
add_action( 'after_setup_theme', 'authority_register_grid_layout' );
/**
 * Registers custom grid layout.
 */
function authority_register_grid_layout() {
	genesis_register_layout(
		'authority-grid', // A layout slug of your choice. Used in body classes. 
		[
			'label' => __( 'Three-column Grid', 'authority-pro' ),
			'img'   => get_stylesheet_directory_uri() . '/images/grid.gif',
			'type'  => [ 'category', 'post_tag' ],
		]
	);
}
```

<p class="notice-small">
The <strong>img</strong> value is the URL of an image you have created for the custom layout, stored in your theme or plugin folder. <br><br>Genesis default layout images are 136px ⨉ 122px with a background color of <code>#7e8181</code>. The images use pure white 2px wide borders and dividing lines.
</p>

## ⚠ New layouts replace default site layouts for that type

By adding the `authority-grid` layout to the `category` and `post_tag` types above, the category and post tag archives now display the `authority-grid` layout only, in place of the Genesis default site layouts:

<img src="{{ '/img/settings-grid-layout-only.jpg' | url }}" alt="The category layout options showing the grid layout only."> 

To let users select the site default layouts <em>in addition to</em> your custom layout, add the default layouts for the relevant types:

```php
if ( function_exists( 'genesis_add_type_to_layout' ) ) {
	genesis_add_type_to_layout( 'content-sidebar', [ 'category', 'post_tag' ] );
	genesis_add_type_to_layout( 'sidebar-content', [ 'category', 'post_tag' ] );
	genesis_add_type_to_layout( 'full-width-content', [ 'category', 'post_tag' ] );
}
```

The custom layout now appears in addition to the default layouts in category and post tag settings:

<img src="{{ '/img/authority-grid-layout.png' | url }}" alt="Category layouts showing the new Authority Grid layout.">

This gives you the flexibility to either replace layouts for a type with your own, or extend Genesis site defaults with custom layouts for certain types.

<p class="notice-small">If you register a custom layout for the 'site' type, this extends the existing site types instead of replacing them. You do not need to call <code>genesis_add_type_to_layout( 'content-sidebar', [ 'site' ] );</code>.</p>

### About the 'type' parameter

Change the `type` parameter in your `genesis_register_layout()` call to choose the page types your layout is available on:

```php
'type' => [ 'site' ], // All types that do not have custom layouts set.
'type' => [ 'page' ], // Just pages.
'type' => [ 'post' ], // Just posts.
'type' => [ 'singular' ], // All singular types (includes pages and posts).
'type' => [ 'category', 'post_tag' ], // Categories and post tags.
'type' => [ 'category', 'singular' ], // Categories or singular types.
'type' => [ 'category-2', ], // Just the category with an ID of 2.
'type' => [ 'post-123' ], // A custom layout for the post with ID 123.
'type' => [ 'page-123' ], // A custom layout for the page with ID 123.
'type' => [ 'author' ], // Author archives.
'type' => [ 'author-123' ], // Only the author with an ID of 123.
'type' => [ 'author-123', 'author-456' ], // Only authors with IDs of 123 or 456.
'type' => [ 'post', 'author-123', 'category-2' ], // Mixed types.
``` 

## Make changes based on the layout

Use `genesis_site_layout()` to get the layout for the current page. The function returns the default site layout if no custom layout is set.  

Genesis uses this function to automatically add body classes for the current layout:

```php
add_filter( 'body_class', 'genesis_layout_body_classes' );
/**
 * Add site layout classes to the body classes.
 *
 * We can use pseudo-variables in our CSS file, which helps us achieve multiple site layouts with minimal code.
 *
 * @since 1.0.0
 *
 * @param array $classes Existing body classes.
 * @return array Amended body classes.
 */
function genesis_layout_body_classes( array $classes ) {

	$site_layout = genesis_site_layout();

	if ( $site_layout ) {
		$classes[] = $site_layout;
	}

	return $classes;

}
```

And to conditionally suppress sidebars if the layout is full-width:

```php
add_action( 'genesis_after_content', 'genesis_get_sidebar' );
/**
 * Output the sidebar.php file if layout allows for it.
 *
 * @since 1.0.0
 */
function genesis_get_sidebar() {

	$site_layout = genesis_site_layout();

	// Don't load sidebar on pages that don't need it.
	if ( 'full-width-content' === $site_layout ) {
		return;
	}

	get_sidebar();

}
```

Authority Pro makes adjustments to page output if its custom `authority-grid` layout is used:

```php
add_action( 'genesis_meta', 'authority_grid_layout' );
/**
 * Sets up the grid layout.
 *
 * @since 1.0.0
 */
function authority_grid_layout() {

	$site_layout = genesis_site_layout();

	if ( 'authority-grid' === $site_layout ) {
		remove_action( 'genesis_after_content', 'genesis_get_sidebar' );
		remove_action( 'genesis_after_content_sidebar_wrap', 'genesis_get_sidebar_alt' );
		add_filter( 'genesis_skip_links_output', 'authority_grid_skip_links_output' );
		add_filter( 'genesis_pre_get_option_content_archive_limit', 'authority_grid_archive_limit' );
		add_filter( 'genesis_pre_get_option_content_archive_thumbnail', 'authority_grid_archive_thumbnail' );
		add_filter( 'genesis_pre_get_option_content_archive', 'authority_grid_content_archive' );
		add_filter( 'genesis_pre_get_option_image_size', 'authority_grid_image_size' );
		add_filter( 'genesis_pre_get_option_image_alignment', 'authority_grid_image_alignment' );
	}

}
```

## Enable Genesis layouts on custom post types

Add post type support for `genesis-layouts` and `custom-fields` to your custom post type slug:

```php
add_post_type_support( 'your_custom_post_type', [ 'genesis-layouts', 'custom-fields' ] );
```

Learn more about <a href="{{ '/developer-features/post-type-support/' | url }}">Genesis post type support</a>.

## Force a page layout

Use the `genesis_site_layout` filter to override user and site layouts:

```php
add_filter( 'genesis_site_layout', '__genesis_return_full_width_content' );
``` 

This can be helpful on custom page templates, but should be used sparingly so that users can select the layout wherever possible. 

Genesis provides helper functions to return the layout slug for its default layouts:

- `__genesis_return_content_sidebar`
- `__genesis_return_sidebar_content`
- `__genesis_return_content_sidebar_sidebar`
- `__genesis_return_sidebar_sidebar_content`
- `__genesis_return_sidebar_content_sidebar`
- `__genesis_return_full_width_content`

To force a custom layout, return the layout slug from a callback function:

```php
add_filter( 'genesis_site_layout', 'custom_return_magic_grid' );
/**
 * Force a custom layout that was registered as 'magic-grid'.
 * 
 * @return string The grid layout.
 */
function custom_return_magic_grid() {
	return 'magic-grid';
}
```

## Set available layouts with a theme config file

Theme developers can alternatively <a href="{{ '/developer-features/configuration/#override-genesis-features' | url }}">set supported layouts using a layouts.php config</a>.

Plugin developers should use `genesis_register_layout` and `genesis_unregister_layout` as directed above. The `config/layouts.php` file in themes is not filterable by plugins.

## Set the default site layout during theme activation

Theme developers can set the `site_layout` key in their `config/child-theme-settings.php` to update the default site layout when their theme is activated.

Learn more about <a href="{{ '/theme-setup/theme-settings' | url }}">setting theme settings during activation</a>.

## Where Genesis stores layout data

Genesis stores the selected layout in site, archive, author, or page meta:

- Site default: `site_layout` key of `genesis-settings` in the `wp_options` table.
- Archive layout: `layout` [term meta](https://developer.wordpress.org/reference/functions/get_term_meta/).
- Author layout: `layout` [author meta](https://developer.wordpress.org/reference/functions/get_the_author_meta/).
- Page layout: `_genesis_layout` [post meta](https://developer.wordpress.org/reference/functions/get_post_meta/).

## Accessing layouts via the REST API

Genesis exposes a <a href="{{ '/developer-features/rest-api/#genesis-layouts' | url }}">layouts endpoint</a> to reveal all layouts that different types support. This is used for the block editor layout selector but may also be helpful for developers extending Genesis.
