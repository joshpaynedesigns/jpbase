# JPBase Theme

## Getting Started
To get started, you will want to take the following steps:

1. Rename all instances of "exodus" to the name of your theme.
2. Install [Node.js](https://nodejs.org/en/) 4 (Node 5 might have some issues with gulp plugins).
3. Run `npm install -g gulp`
4. Run `npm install -g bower`
5. Navigate to the theme folder in terminal
6. Run `npm install`
7. Run `gulp`

This will get you up and running and able to start writing Sass and compiling. To see what else Gulp does, take a look at `gulpfile.js`.

## Using Bower
We use Bower to manage all of our JS dependencies. If you want to add a jQuery plugin or JS framework, all you need to do is run `bower install NAME_OF_DEPENDENCY --save`. This will install all of the files in the `/assets/components` directory for you to enqueue. This dependency will also be saved in bower.json. If adding a new developer to the project, they will need to run `bower install` to download all of the files for the components.

## Plugins
This theme comes built in with the following plugins added as Must-Use Plugins:

+ Advanced Custom Fields Pro
+ 434 Marketing CTA
+ ACF Flex Tools

**Plugins We Recommend**

* [TinyMCE Advanced](https://wordpress.org/plugins/tinymce-advanced/)
* [WP Migrate DB Pro](https://deliciousbrains.com/wp-migrate-db-pro/)
* [Imsanity](https://wordpress.org/plugins/imsanity/)
* [Force Regenerate Thumbnails](https://wordpress.org/plugins/force-regenerate-thumbnails/)
* [Gravity Forms](http://www.gravityforms.com/)
* [My Eyes Are Up Here](https://wordpress.org/plugins/my-eyes-are-up-here/screenshots/)

## TinyMCE Settings

To make importing these settings a bit simpler here is the import for our standard set up for the TinyMCE editor. You can import this by going to Settings >> TinyMCE Advanced and under Administration select "Import Settings."

```json
{"settings":{"toolbar_1":"pastetext,formatselect,styleselect,removeformat,bold,italic,blockquote,bullist,numlist,alignleft,aligncenter,alignright,link,unlink,fullscreen,hr,undo,redo,wp_code,wp_help","toolbar_2":"","toolbar_3":"","toolbar_4":"","options":"advlist","plugins":"advlist,importcss"},"admin_settings":{"options":"importcss,no_autop","disabled_editors":""}}
```

## ACF Flexible Sections
ACF's flexible sections are used to build out the majority of the landing pages on the sites. To utilize the flexible sections, ensure that ACF-Flex-Tools is loaded as a Must-Use Plugin. This plugin will create the appropriate classes and functions to display the flexible sections.

To use the flexible sections take the following steps:

1. Add the following to the loop on the page that you would like to display the flexible sections.

```php
function cgd_flexible_sections() {
	echo '<section id="flexible-section-repeater">';
	$fcs = FlexibleContentSectionFactory::create('page_flexible_sections');
	$fcs->run();
	echo '</section>';
}
```
2. Ensure that the `partials/sections` directory is installed.
3. For each section, create a folder. For example, we will make a "content" folder to hold the content function and markup.
4. Create two files: `function.php` and `item.php`
5. Update information in `function.php`
```php
return (object) array(
	'acf_name'  => 'content_section',
	'options'   => (object) array(
	'func'      => function ($padding_classes = '') {
		$p_loc = FlexibleContentSectionUtility::getSectionsDirectory();
		$fcta_loc = "$p_loc/content";
		$item = "$fcta_loc/item.php";

		$content = get_sub_field('content');

		require($item);
	},
	'has_padding'   => true
	)
);
```
6. Add the actual markup to `item.php`
7. Add any Sass files in `styles/sections`

## SVG Icon System
We use SVG for all of our icons instead of a font icon library for a number of different reasons. We recommend reading ["Practical SVG"](https://abookapart.com/products/practical-svg) by Chris Coyier to learn about how SVG. We follow his SVG icon library system that he explains in the book.

The base theme starts with a few commonly used icons in the `assets/icons/src` directory. To add new icons, export an SVG file from Illustrator or Sketch and drop it into this directory. Make sure that the name of the file accurately describes what the icon is. There is no need to add a prefix of "icon" or anything like that. Our system will automatically add a prefix of "ico" to be able to differentiate between icons when pulling them into the HTML.

To pull an icon into your HTML, use the following PHP function:
```php
echo get_svg_icon($file_name, $class_name, $height, $width);
```
The function will output an inline SVG element that can be style via CSS. To access a specific icon, you will just input the file name minus '.svg' as the first parameter. All other parameters are optional.

## Email Address Obfuscation

To display an obfuscated email address simply use the following snippet, passing in an email address. It will generate a mailto: link for you.

```php
<?php echo cgd_hide_email($email_address) ?>
```