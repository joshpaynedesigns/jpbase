---
title: Genesis Featured Images
menuTitle: Featured Images
layout: layouts/base.njk
permalink: developer-features/featured-images/index.html
tags: docs
minVersion: Genesis 3.1.0+
---

## Output featured images on selected post types
The `genesis-singular-images` post type support enables output of featured images and adds Customizer options to show images on supported post types.

Add support for each post type like this:

```php
add_post_type_support( 'post', 'genesis-singular-images' );
add_post_type_support( 'page', 'genesis-singular-images' );
```

<p class="notice-small">
When adding <code>genesis-singular-images</code> post type support to a post type other than 'post' or 'page', you must also add <code>custom-fields</code> support to your custom post type: <br><br><code>add_post_type_support( 'your-type', 'custom-fields' );</code><br><br>This ensures the “hide featured image” option appears in the Genesis editor sidebar when editing your post type.
</p>

Adding `genesis-singular-images` support results in these options in the Customizer at <em>Theme Settings → Singular Content</em>:

<img src="{{ '/img/featured-image-output.png' | url }}" alt="Customizer options showing checkboxes to display images on posts and pages.">

For entries using the block editor, users also gain an option in the Genesis sidebar to hide images in the Genesis editor sidebar:

<img src="{{ '/img/genesis-sidebar-images-panel.png' | url }}" alt="Genesis sidebar in the WordPress block editor showing the Images panel that allows users to hide featured images for the current entry.">


### Change the image output position

When each “show featured images” option is checked, Genesis will output the images for you via the `genesis_entry_content` hook. Adjust the image output position like this:

```php
remove_action( 'genesis_entry_content', 'genesis_do_singular_image', 8 );
add_action( 'genesis_[your_choice]', 'genesis_do_singular_image' );
```

### Change the image size

Images use the archive image size from <em>Theme Settings → Content Archive → Featured Image Size</em> by default. Theme developers can set a default image size for all singular featured images by adding a custom image size to their theme:

```php
add_image_size( 'genesis-singular-images', 200, 300, true );
```

And override that for specific post types:

```php
add_image_size( 'genesis-singular-image-[type]', 600, 300, true );
```

A `genesis_singular_image_size` filter is available to override the image size.
