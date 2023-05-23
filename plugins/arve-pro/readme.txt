=== ARVE Pro Addon ===
Donate link: https://nextgenthemes.com/donate/
Requires at least: 4.9.0
Tested up to: 5.7.0
Requires PHP: 5.6.0
License: GPL 3.0
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Lazyload, Lightbox and more for ARVE

== Description ==

Lazyload, Lightbox and more for ARVE

== Installation ==

Please read the [official documentation](https://wordpress.org/support/article/managing-plugins/#installing-plugins) for that.

## Changelog ##

= 2023-04-18 5.3.6 =

* Fix: Some styles not correctly applying in the Block editor.

= 2023-04-05 5.3.5 =

* Fix: JS errors in some circumstaces like ARVE mixing with other players.
* Fix: Some styles not correctly applying in the Block editor.

= 2023-01-25 5.3.4 =

* Fix: Link-Lightbox mode not working.

= 2023-01-21 5.3.3 =

* Improved: Accessibility for the play button.

= 2022-11-10 5.3.2 =

* Fix: Not working when audio embeds were on the same page as ARVE embeds.

= 2022-09-22 5.3.1 =

* Make sure the addon only runs when ARVE 9.7.14 or later is installed.

= 2022-09-22 5.3.0 =

* Fix: HTML5 video lightboxes closed unintentionally in chromium based browsers.
* Improved: Changed lightbox script from Big-Picture to its successer Bigger-Picture.
* New: Custom hover effect class for your own CSS styles.
* Removed old lightox script Lity as an option.

= 2022-09-05 5.2.3 =

* Fix: Initial thumbnail dimentions.

= 2022-09-05 5.2.2 =

* Fix: Vimeo thumbnials blury, not all sizes loading.
* New: "Darken" hover effect option.

= 2022-09-05 5.2.2 =

* Fix: Vimeo thumbnials blury, not all sizes loading.
* New: "Darken" hover effect option.

= 2021-12-08 5.2.0 =

* New "Reset after played" option.

= 2021-03-11 5.1.17 =

* Improved: Cachebusting for assets.
* Tested with WP 5.7.0

= 2021-02-20 5.1.16 =

* Fix: Thumbnail not showing up in certain situations.

= 2021-02-13 5.1.12 =

* Compatibility with latest ARVE caching system.

= 2021-02-10 5.1.11 =

* Fix: Lightbox compatibility with Divi.

= 2021-01-30 5.1.10 =

* Fix: Invisible videos

= 2021-01-24 5.1.8 =

* Improved: Corrected colors for Vimeo style play button.
* Improved: Simplified CSS and reduced size.
* Improved: Code improved and adjusted to ARVE 9.3.0

= 2021-01-21 5.1.7 =

* Fix: Wrong version check caused the plugin not working correctly.

= 2021-01-21 5.1.4 =

* Adjustment to new ARVE.

= 2021-01-07 5.1.3 =
* Fix: Removed no longer working Facebook auto thumbnail code.

= 2021-01-06 5.1.2 =
* Fix: Vimeo play button style not clickable.
* Fix: vimeo play button line break issue.

= 2021-01-03 5.1.1 =
* New: Vimeo play button style.

= 2020-12-14 5.0.1 =
* Fix: Issue with thumbnail positioning.

= 2020-12-12 5.0.0-beta13 =
* Fix: Error handling issue #2.

= 2020-12-09 5.0.0-beta10 =
* Fix: Error handling issue.

= 2020-12-05 5.0.0-beta9 =
* Fix: Some links not opening lightboxes.

= 2020-12-03 5.0.0-beta7 =
* Fix: Thumbnail height calculation caused errors in some cases.

= 2020-12-01 5.0.0-beta6 =
* Fix: Rebuild oembed cache after activation and version updates.

= 2020-11-28 5.0.0-beta5 =
* New: Big Picture Lightbox script.
* New: Option to go fullscreen when opening lightbox, at optionally stay fullscreen even after closing it.
* New: (Only YouTube & Vimeo, HTML5) Go back to original preview after video ends.
* New: Auto-pause/auto-reset other videos when another video is played.
* New: Auto close Lightbox when Video ends (YouTube, Vimeo, HTML5 video)
* Improved: Dropped dependencies: jQuery, Lity Lightbox, Object-Fit Polyfill.
* Improved: Lots of code and build improvements.
* Improved: When aligned get expanded, their align class will be removed. When a video end is detected (only YouTube, Vimeo, HTML5 Video) the original state is restored.

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
