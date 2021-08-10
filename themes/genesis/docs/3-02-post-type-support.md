---
title: Genesis Post Type Support
menuTitle: Post Type Support
layout: layouts/base.njk
permalink: developer-features/post-type-support/index.html
tags: docs
---

Genesis offers certain features via [post type support](https://developer.wordpress.org/reference/functions/add_post_type_support/):

| Support | Types with support by default | Description | Also requires |
|-|-|-|-|
| genesis-seo | post, page | Add a Genesis SEO panel to control SEO options. | - |
| genesis-scripts | post, page | Add a Genesis Scripts field for per-page scripts. | - |
| genesis-layouts | post, page | Add Genesis layout options to your post type. | custom-fields |
| genesis-breadcrumbs-toggle | post, page | Display a “hide breadcrumbs” checkbox control in the Genesis editor sidebar. | custom-fields |
| genesis-footer-widgets-toggle | post, page | Display a “hide footer widgets” checkbox control in the Genesis editor sidebar. | custom-fields |
| genesis-title-toggle | page | Display a “hide title” checkbox control in the Genesis editor sidebar. | custom-fields |
| genesis-singular-images | none | Add <a href="{{ '/developer-features/featured-images/' | url }}">options to show featured images</a>. | custom-fields |

## Remove Genesis post type support

To opt-out of Genesis post type support on the default post and page types, remove support in your child theme's `functions.php` with [`remove_post_type_support()`](https://developer.wordpress.org/reference/functions/remove_post_type_support/):

```php
// Remove the ability to hide the title on pages.
remove_post_type_support( 'page', 'genesis-title-toggle' );
```

## Add post type support to default post types

Extend Genesis post features to existing types using [`add_post_type_support()`](https://developer.wordpress.org/reference/functions/add_post_type_support/):

```php
// Add the “hide title” checkbox to posts as well as pages.
add_post_type_support( 'post', 'genesis-title-toggle' );
```

## Add post type support to custom post types
<p class="notice-small">
<strong>You must also add <code>custom-fields</code> post type support to use features designed for the Genesis block editor sidebar, as noted in the support table above.</strong>
</p>


Add Genesis post type supports to custom post types like this:

```php
add_post_type_support( 
	'your-type',
	[
		'custom-fields',
		'genesis-seo',
		'genesis-scripts',
		'genesis-layouts',
		'genesis-breadcrumbs-toggle',
		'genesis-footer-widgets-toggle',
		'genesis-title-toggle',
		'genesis-singular-images'
	]
);
```




