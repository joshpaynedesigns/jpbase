---
title: Genesis REST API
menuTitle: REST API
layout: layouts/base.njk
permalink: developer-features/rest-api/index.html
minVersion: Genesis 3.1.0+
tags: docs
---

Genesis exposes meta data and settings to the [WordPress REST API](https://developer.wordpress.org/rest-api/).

## Genesis post meta
For post types with `custom-fields` support, Genesis exposes the title toggle, breadcrumbs toggle, featured image display status, layout, and body and post custom classes under the `meta` key in the posts endpoint:

```
> curl https://example.com/wp-json/wp/v2/posts/[id]
```

```json
…
"meta": {
    "_genesis_hide_title": false,
    "_genesis_hide_breadcrumbs": false,
    "_genesis_hide_singular_image": false,
    "_genesis_hide_footer_widgets": false,
    "_genesis_custom_body_class": "",
    "_genesis_custom_post_class": "",
    "_genesis_layout": "full-width-content"
},
…
```

## Genesis layouts
Access the supported layouts for the currently active theme via the Genesis `layouts` endpoint:

```
> curl https://example.com/wp-json/genesis/v1/layouts/site
```

```json
{
  "content-sidebar": {
    "label": "Content, Primary Sidebar",
    "img": "https://example.com/wp-content/themes/genesis/lib/admin/images/layouts/cs.gif",
    "type": ["site"],
    "default": true
  },
  "sidebar-content": {
    "label": "Primary Sidebar, Content",
    "img": "https://example.com/wp-content/themes/genesis/lib/admin/images/layouts/sc.gif",
    "type": ["site"],
    "default": false
  },
  "full-width-content": {
    "label": "Full Width Content",
    "img": "https://example.com/wp-content/themes/genesis/lib/admin/images/layouts/c.gif",
    "type": ["site"]
  }
}
```

Some Genesis sites may register layouts for specific page types or IDs:

```php
add_action( 'after_setup_theme', 'custom_page_layouts' );
/**
 * Register custom Genesis layouts.
 */
function custom_page_layouts() {
    // A layout that will be selectable on 'singular' types only.
	genesis_register_layout(
		'test-singular',
		[
			'label' => __( 'Singular', 'custom' ),
			'img' => get_stylesheet_directory_uri() . '/images/singular-layout.png',
			'type' => [ 'singular' ],
		]
	);

    // A layout that will be selectable on page 99 only.
	genesis_register_layout(
		'test-page-99',
		[
			'label' => __( 'Page 99', 'custom' ),
			'img' => get_stylesheet_directory_uri() . '/images/page-99-layout.png',
			'type' => [ 'page-99' ],
		]
	);
}
```
 
Request layouts for the [singular](https://developer.wordpress.org/reference/functions/is_singular/) type:

```
> curl https://example.com/wp-json/wp/v2/posts/singular 
```

```json
{
  "test-singular-layout": {
    "label": "Singular Layout",
    "img": "https://example.com/wp-content/themes/genesis-sample/images/singular-layout.png",
    "type": [
      "singular"
    ]
  }
}
``` 

Or layouts registered for a specific page ID:

```
> curl https://example.com/wp-json/wp/v2/posts/page-99 
```

```json
{
  "test-page-99": {
    "label": "Test Layout",
    "img": "https://example.com/wp-content/themes/genesis-sample/images/page-id-99-layout.png",
    "type": [
      "page-99"
    ]
  }
}
```

Requests for page type or ID will fall back to 'site' if no matching ID or type specific layouts have been registered.

You can make a single request for multiple layout types. This request asks for a 'page-99' layout, falling back to 'page' if no 'page-99' layout exists, falling back to 'site' if no 'page' layout exists:

```
> curl https://example.com/wp-json/genesis/v1/layouts/site,page,99 
``` 

## Genesis singular images
Get post types with <a href="{{ '/developer-features/featured-images/' | url }}">genesis-singular-images support</a> enabled and active:

```
> curl https://example.com/wp-json/genesis/v1/singular-images
```

```json
[
    "post",
    "page"
]
```

The absence of a post type denotes that `genesis-singular-images` support is not active for that type.

## Genesis breadcrumbs
Get breadcrumbs options that are enabled:

```
> curl https://example.com/wp-json/genesis/v1/breadcrumbs
```

```json
[
    "breadcrumb_home",
    "breadcrumb_front_page",
    "breadcrumb_posts_page",
    "breadcrumb_single",
    "breadcrumb_page",
    "breadcrumb_archive",
    "breadcrumb_404",
    "breadcrumb_attachment"
]
```

The values match the key for each breadcrumb toggle setting displayed in the Customizer.

The absence of a key denotes that breadcrumbs are not currently enabled for that area.
