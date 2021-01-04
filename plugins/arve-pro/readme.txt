=== ARVE Pro Addon ===
Donate link: https://nextgenthemes.com/donate/
Requires at least: 4.9.0
Tested up to: 5.2.2
Requires PHP: 5.3.0
License: GPL 3.0
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Lazyload, Lightbox and more for ARVE

== Description ==

Lazyload, Lightbox and more for ARVE

== Installation ==

Please refer to [Installing and License Management](https://nextgenthemes.com/plugins/arve/documentation/installing-and-license-management/)

== Changelog ==

= 2019-10-09 4.3.0 =

* Improved: SEO data for `description` and `uploadDate` is now filled in automatically!
* Fix: Last version string was not updated correctly.

= 2019-10-01 4.2.9 =

* New: Auto thumbnails for Wistia
* New: `img_srcset` shortcode attribute to manually set thumbnail `srcset` attribute `thumbnail` also needs to be set with a image url.

= 2019-08-22 4.2.8 =

* Fix: Facebook thumbnails work again but only very small images at this point.

= 2019-08-19 4.2.7 =

* Possible fix for Thumbnails not showing for Vimeo private videos.

= 2019-08-05 4.2.6 =

* Added `lightbox_maxwidth` shortcode attribute, default is 1200.

= 2019-01-16 4.2.5 =

* Fix: Titles not showing up.

= 2018-12-14 4.2.4 =

* Fix: Link lightbox thumbnail issue.

= 2018-12-10 4.2.2 =

* Fix: Auto updates work again.

= 4.2.1 =

* Fix: Load really all assets of that option is enabled.

= 2018-11-28 4.2.0 =

* Fix: Really load all Pro assets when the "Always load assets" option is enabled.

= 2018-11-28 4.2.0 =

* Improved: Prevent ARVE from loading high resolution thumbnails when they are not needed based on the 'Maximal Video Width' setting (defaults to your themes `$content_width` setting if not set) and `maxwidth` shortcode attribute. If you have specific pages where you display video in a grid or multiple in a row you can optimize by giving all videos that are displayed smaller on big screens then your content width or your global 'Maximal Video Width' setting a specific `maxwidth` attribute.

= 2018-11-16 4.1.1 =

* Fix HTML5 video displayed above thumbnail and titles.

= 2018-09-07 4.1.0 =

* Note: PHP versions below 5.6 will no longer be tested and future versions will require at least PHP 5.6
* Improved: Updated 3rd party libs Mobile Detect, objectFitPolyfill and Lity.
* Improved: 3rd party assets are now loaded from jsDelivr CDN. If you do not want this you can load them from your site or let a CDN plugin pick them up with `add_filter( 'nextgenthemes_use_cdn', '__return_false' );` inside a mu-plugin.
* Fix: Some auto thumbnail code was not working properly.
* Fix: Removes WordFence malware warning by removing a comment in Mobile Detect that pointed to a malware infected domain.

= 2018-02-20 4.0.4 =

* This is also a test if the auto update process works for everyone, please let me know if you have issues.
* Improved: Updated Mobile Detect library.
* Fix: Thumbnails detection for YouTube playlists.

= 2017-08-12 4.0.3 =

* Fix: Vimeo private videos showing 404 API errors instead of videos.
* Fix: Prevent a PHP notice.
* Improved: Renamed \"Grow on click\" to \"Expand on click\" in dialog. Better English right?

= 2017-05-04 4.0.1 =

* Fixed: inview-lazyload not working correctly.

= 2017-05-04 4.0.0 =

* New: \'volume\' attribute takes 1-100 for HTML5 video.
* Improved: Simplified the inview-lazyload option to on/off - was getting to complicated. On uses it when it makes sense (on all mobiles, and when there is no thumbnail detected or set).
* Improved: Scripts like inview and lity lightbox are now only loaded when they are actually needed.
* Improved: Pointer cursor when hovering over the thumbnails, for themes that do not already add it.

= 2017-04-30 3.9.5 =

* Code needed for the new way ARVE handles sandbox
* Updated objectFitPolyfill

= 2017-04-03 3.9.4 =

* Fix: Deals with other crappy coded plugins that load the Mobile_Detect class without checking if its already loaded.
* Improved: Make sure ARVE Pro always loads its own, possible more up to date version of Mobile_Detect
* Improved: Updated that Mobile_Detect class.
* Improved how aspect ratio is handled with HTML5 videos.

= 2017-03-25 3.9.3 =

* Fix: Licensing storage mess. Some users may have to reenter their keys on the licensing page when updating form very old versions.
* Improved: Better Browser support by using Autoprefixer with the [config from bootstrap](https://github.com/twbs/bootstrap/blob/v4-dev/grunt/postcss.config.js)
* Some minor code improvements.

= 2017-03-20 3.9.2 =

* Fix: Broken CSS.

= 2017-03-20 3.9.1 =

* Fix: Lightboxes with not html5 videos did not close.

= 2017-03-20 3.9.0 =

* Fix: Lightboxes are now sized correctly.
* Fix: HTML5 videos in lightbox mode could not be opened twice.
* Improved: HTML5 video now automatically pauses when lightboxes are closes and resume when reopened.
* Improved: Now minified files are served when `WP_DEBUG` is not `true`

= 2017-03-12 3.8.4 =

* Improved: Removed incorrect href on
