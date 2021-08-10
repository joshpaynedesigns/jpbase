---
title: Genesis Body Classes
menuTitle: Body Classes
layout: layouts/base.njk
permalink: developer-features/body-classes/index.html
tags: docs
---

Genesis adds classes to the `body` element to help you adjust page styling.

## Content width classes
The `genesis_site_layout()` slug for the current page is added to the body class. This is usually one of:

- full-width-content
- content-sidebar
- sidebar-content
- sidebar-content-sidebar
- sidebar-sidebar-content
- content-sidebar-sidebar

 The layouts the active theme supports determine which classes can appear.

Remove the layout body class if needed:

```php
remove_filter( 'body_class', 'genesis_layout_body_classes' );
```

## Feature classes
<p class="notice">
Requires Genesis 3.1.1+.
</p>

These classes show if the title, breadcrumbs and featured images are hidden or visible for the current page:

- genesis-title-hidden
- genesis-title-visible
- genesis-breadcrumbs-hidden
- genesis-breadcrumbs-visible
- genesis-singular-image-hidden
- genesis-singular-image-visible

Remove the Genesis feature classes if needed:

```php
remove_filter( 'body_class', 'genesis_title_hidden_body_class' );
remove_filter( 'body_class', 'genesis_breadcrumbs_hidden_body_class' );
remove_filter( 'body_class', 'genesis_singular_image_hidden_body_class' );
remove_filter( 'body_class', 'genesis_singular_image_visible_body_class' );

```

## Header classes

Genesis emits the following classes to help with header styling:

- header-image: if the site is displaying a header image.
- header-full-width: if the site is not displaying anything in the header-right area.
- custom-header: if the theme is using a custom header and the default header image and text color have been changed.

You can remove these header classes if you wish:

```php
remove_filter( 'body_class', 'genesis_header_body_classes' );
```

## Archive classes

Genesis adds the `archive-no-results` body class to archive pages that display no posts to help style empty archives.

To remove the archive class, remove the related filter:

```php
remove_filter( 'body_class', 'genesis_archive_no_results_body_class' );
```

## Custom classes
Genesis adds a “Custom Classes” area to the WordPress editor for public post types.

Classes that users enter into the Body Class field will appear in the body class attribute for that entry.
