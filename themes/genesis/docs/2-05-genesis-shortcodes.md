---
title: Genesis Shortcodes
menuTitle: Genesis Shortcodes
layout: layouts/base.njk
permalink: basics/genesis-shortcodes/index.html
tags: docs
---

Shortcodes are a feature supported by WordPress that can be used to easily display dynamic text elements. They are short, text-based “codes” that you can enter into certain areas of your site to display relevant information such as a post’s date and time, author, and tags. These shortcodes are often used to customize things like the Entry Header for posts and the Credits line in the footer of your site.

For example, if you enter “Published by on January 31, 2017” as the post byline for your blog posts, displays as the user who published the post and January 31, 2017 displays as the date the post was published.

The WordPress comes with a number of handy shortcodes which can be used in a variety of ways on your site.

There are also several Genesis-Specific shortcodes you can use as well.

## Post Shortcodes

Below is a list of shortcodes that can be used in the Entry Header and Entry Footer sections. Following each shortcode is an example format and the corresponding output.

### [post_date]

This shortcode produces the date of post publication. Here is a list of attributes for this shortcode:

* format – The format for the date. Defaults to the date format configured in your WordPress settings.
* before – Text/markup to place before the post date.
* after – Text/markup to place after the post date.
* label – Text to place before the post date.

Example output and corresponding shortcode:

Dated: November 12, 2012
```
[post_date format="F j, Y" label="Dated: "]
```

### [post_modified_date]

This shortcode produces the date on which the post was last modified. Here is a list of attributes for this shortcode:

* format – The format for the date. Defaults to the date format configured in your WordPress settings.
* before – Text/markup to place before the post modified date.
* after – Text/markup to place after the post modified date.
* label – Text to place before the post modified date.

Example output and corresponding shortcode:

Last Modified: March 13, 2017
```
[post_modified_date format="F j, Y" label="Last Modified: "]
```

### [post_time]

This shortcode produces the time of post publication. Here is a list of attributes for this shortcode:

* format – The format for the time. Defaults to the time format configured in your WordPress settings.
* before – Text/markup to place before the post time.
* after – Text/markup to place after the post time.
* label – Text to place before the post time.

See the [WordPress Codex](https://codex.wordpress.org/Formatting_Date_and_Time) for more information on formatting date and time.

Example output and corresponding shortcode:

12:01 pm
```
[post_time format="g:i a"]
```

### [post_modified_time]

This shortcode produces the time at which the post was last modified. Here is a list of attributes for this shortcode:

* format – The format for the time. Defaults to the time format configured in your WordPress settings.
* before – Text/markup to place before the post modified time.
* after – Text/markup to place after the post modified time.
* label – Text to place before the post modified time.

Example output and corresponding shortcode:

6:15 pm

```
[post_modified_time format="g:i a"]
```

### [post_author]

This shortcode produces the author of the post (display name). Here is a list of attributes for this shortcode:

* before – Text/markup to place before the post author name.
* after – Text/markup to place after the post author name.

Example output and corresponding shortcode:

Sample User
```
[post_author before="<em>" after="</em>"]
```

### [post_author_link]

This shortcode produces the author of the post with a link to the author URL. Here is a list of attributes for this shortcode:

* before – Text/markup to place before the post author link.
* after – Text/markup to place after the post author link.

Example output and corresponding shortcode:

*[Sample User](#)*
[post_author_link before="<b>" after="</b>"]

### [post_author_posts_link]

This shortcode produces the author of the post with a link to author archive. Here is a list of attributes for this shortcode:

* before – Text/markup to place before the post author link.
* after – Text/markup to place after the post author link.

Example output and corresponding shortcode:

*[Sample User](#)*
```
[post_author_posts_link before="<b>" after="</b>"]
```

### [post_comments]

This shortcode produces the comment link. Here is a list of attributes for this shortcode:

* zero – Text to display if zero comments on the post.
* one – Text to display if one comment on the post.
* more – Text to display if more than one comment on the post.
* hide_if_off – Enable the comment link even if comments are off.
* before – Text/markup to place before the post comment link.
* after – Text/markup to place after the post comment link.

Example output and corresponding shortcode:

No Comments: [No Comments ](#)
1 Comment: [1 Comment ](#)
Multiple Comments: [7 Comments](#)
```
[post_comments zero="No Comments" one="1 Comment" more="% Comments" hide_if_off="disabled"]
```

### [post_tags]

This shortcode produces the tag link list. Here is a list of attributes for this shortcode:

* sep – Separator between post tags.
* before – Text/markup to place before the tag list. Default “Tagged With: “
* after – Text/markup to place after the tag list.

Example output and corresponding shortcode:

Tags: [Tag Name](#)
```
[post_tags sep=", " before="Tags: "]
```

### [post_categories]

This shortcode produces the category link list. Here is a list of attributes for this shortcode:

* sep – Separator between post categories.
* before – Text/markup to place before the post category list. Default “Filed Under: “
* after – Text/markup to place after the post category list.

Example output and corresponding shortcode:

Posted Under: [Category #1](#)
```
[post_categories sep=", " before="Posted Under: "]
```

### [post_edit]

This shortcode produces the edit post link for logged in users. Here is a list of attributes for this shortcode:

* link – Text for edit link. Default “(Edit)”
* before – Text/markup to place before the edit post link. Default “Filed Under: “
* after – Text/markup to place after the edit post link.

Example output and corresponding shortcode (This link displays only for logged-in users with a role that permits editing):

[(Edit)](#)
```
[post_edit  before="<b>" after="</b>"]
```

### [post_terms]

This shortcode produces a list of terms associated with the post from the specified taxonomy. Here is a list of attributes for this shortcode:

* sep – Separator between the terms.
* before – Text/markup to place before the post terms list. Default “Filed Under: “
* after – Text/markup to place after the terms list.
* taxonomy – Which taxonomy to use. The default taxonomy is “category”. See the WordPress codex for more information on taxonomies.

Example output and corresponding shortcode:

Category terms: term1, term2, term3
```
[post_terms sep=", " before="Category terms: "]
```

## Footer Shortcodes

Below is a list of shortcodes that can be used in the site footer. Following each shortcode is an example format and the corresponding output.

###[footer_copyright]
This shortcode produces the copyright. Here is a list of attributes for this shortcode:

* copyright – Default: ©
* first – Text/markup to place between the copyright symbol and the copyright date.
* before – Text/markup to place before the copyright.
* after – Text/markup to place after the copyright.

Example output and corresponding shortcode:

© 2005–2016
```
[footer_copyright first="2005"]
```

### [footer_childtheme_link]
This shortcode produces the child theme link. Here is a list of attributes for this shortcode:

* before – Text/markup to place before the childtheme link. Default: &middot
* after – Text/markup to place after the childtheme link.

Example output and corresponding shortcode (Note: This feature must be supported by your child theme.):

· [Agency Pro](#)
```
[footer_childtheme_link before ="&middot; "]
```

### [footer_genesis_link]
This shortcode produces the genesis theme link. Here is a list of attributes for this shortcode:

* before – Text/markup to place before the genesis theme link.
* after – Text/markup to place after the genesis theme link.

Example output and corresponding shortcode:

[Genesis Framework](https://my.studiopress.com/themes/genesis/)
```
[footer_genesis_link]
```

### [footer_studiopress_link]
This shortcode produces the StudioPress link. Here is a list of attributes for this shortcode:

* before – Text/markup to place before the StudioPress link. Default: “by”
* after – Text/markup to place after the StudioPress link.

Example output and corresponding shortcode:

by [StudioPress](https://www.studiopress.com/)
```
[footer_studiopress_link]
```

### [footer_wordpress_link]
This shortcode produces the WordPress link. Here is a list of attributes for this shortcode:

* before – Text/markup to place before the WordPress link.
* after – Text/markup to place after the WordPress link.

Example output and corresponding shortcode:

[WordPress](https://wordpress.org/)
```
[footer_wordpress_link]
```

### [footer_loginout]
This shortcode produces the log in/out link. Here is a list of attributes for this shortcode:

* redirect – Set redirect to a URL on login.
* before – Text/markup to place before the log in/out link.
* after – Text/markup to place after the log in/out link.

Example output and corresponding shortcode:

[Log out](#)
```
[footer_loginout redirect="http://www.studiopress.com/features/genesis"]
```
