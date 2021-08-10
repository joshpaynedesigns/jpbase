---
title: Genesis Schema Markup
menuTitle: Schema Markup
layout: layouts/base.njk
permalink: developer-features/schema-markup/index.html
tags: docs
---

Genesis emits [schema.org markup](https://schema.org/) by default using [HTML Microdata](https://html.spec.whatwg.org/multipage/microdata.html).

Microdata gives search engines access to structured data to help them understand the information on web pages.

## Remove Genesis microdata
<p class="notice">
Requires Genesis 3.1+.
</p>

Disable Genesis microdata with this filter:

```php
add_filter( 'genesis_disable_microdata', '__return_true' );
```

## Alter Genesis microdata
Genesis uses the `genesis_attr_{$context}` filter to add microdata to the HTML it outputs.

From Genesis 3.1+, see the full list of microdata filters together in one location by inspecting the file at `wp-content/themes/genesis/lib/functions/schema.php`.

Amend Genesis microdata for specific contexts by applying the same filter used in `schema.php` but with a later priority.

If you want to filter the 'sidebar-primary' markup, for example, find the existing filter:

```php
add_filter( 'genesis_attr_sidebar-primary', __NAMESPACE__ . '\\sidebar_primary' );
```

Where `sidebar_primary` is defined like this:

```php
function sidebar_primary( $attributes ) {
	$attributes['itemscope'] = true;
	$attributes['itemtype']  = 'https://schema.org/WPSideBar';

	return $attributes;
}

```

Then apply a filter with a later priority (the default priority is 10, so using a priority of 20 will run your custom filter after Genesis):

```php
add_filter( 'genesis_attr_sidebar-primary', 'custom_sidebar_primary_microdata', 20 );
/**
 * Adjust default schema markup for the primary sidebar.
 *
 * @param array $attributes Existing attributes for primary sidebar element.
 * @return array Amended attributes for primary sidebar element.
 */
function custom_sidebar_primary_microdata( $attributes) {
	$attributes['itemtype']  = 'https://schema.org/WPAdBlock';

	return $attributes;
}
```

Attributes you do not alter, such as `itemscope` in the above example, will use the default values from Genesis.

## Compatibility with Yoast SEO
[Yoast SEO](https://wordpress.org/plugins/wordpress-seo/) version 11.0+ emits schema.org markup using [JSON-LD](https://json-ld.org/).

Genesis 3.1+ automatically detects if Yoast SEO is outputting JSON-LD and disables Genesis microdata to prevent duplicate output and conflicts.

If you would prefer to use the main features of Yoast SEO with Genesis microdata instead of Yoast schema.org markup, disable Yoast SEO JSON-LD output with this filter:

```php
add_filter( 'wpseo_json_ld_output', '__return_false' );
```

You will not normally need to remove Yoast SEO JSON-LD markup. The filter can be useful if you are missing microdata that Genesis would offer, or if you have modified Genesis microdata and want to preserve those changes while using Yoast.
