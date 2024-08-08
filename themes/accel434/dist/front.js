/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./assets/js/acf-maps.js":
/*!*******************************!*\
  !*** ./assets/js/acf-maps.js ***!
  \*******************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ACFMaps: () => (/* binding */ ACFMaps)
/* harmony export */ });
var ACFMaps = {
  initMarker: function initMarker($marker, map) {
    // Get position from marker.
    var lat = $marker.data("lat");
    var lng = $marker.data("lng");
    var latLng = {
      lat: parseFloat(lat),
      lng: parseFloat(lng)
    };

    // Create marker instance.
    var marker = new google.maps.Marker({
      position: latLng,
      map: map
    });

    // Append to reference for later use.
    map.markers.push(marker);

    // If marker contains HTML, add it to an infoWindow.
    if ($marker.html()) {
      // Create info window.
      var infowindow = new google.maps.InfoWindow({
        content: $marker.html()
      });

      // Show info window when marker is clicked.
      google.maps.event.addListener(marker, "click", function () {
        infowindow.open(map, marker);
      });
    }
  },
  centerMap: function centerMap(map) {
    // Create map boundaries from all map markers.
    var bounds = new google.maps.LatLngBounds();
    map.markers.forEach(function (marker) {
      bounds.extend({
        lat: marker.position.lat(),
        lng: marker.position.lng()
      });
    });

    // Case: Single marker.
    if (map.markers.length == 1) {
      map.setCenter(bounds.getCenter());

      // Case: Multiple markers.
    } else {
      map.fitBounds(bounds);
    }
  },
  initMap: function initMap($el) {
    // Find marker elements within map.
    var $markers = $el.find(".marker");

    // Create gerenic map.
    var mapArgs = {
      zoom: $el.data("zoom") || 16,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    var map = new google.maps.Map($el[0], mapArgs);

    // Add markers.
    map.markers = [];
    $markers.each(function () {
      ACFMaps.initMarker(jQuery(this), map);
    });

    // Center map based on markers.
    ACFMaps.centerMap(map);

    // Return map instance.
    return map;
  }
};

/***/ }),

/***/ "./assets/js/mobile-menu.js":
/*!**********************************!*\
  !*** ./assets/js/mobile-menu.js ***!
  \**********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   MobileMenu: () => (/* binding */ MobileMenu)
/* harmony export */ });
var stylesheetUrl = data.stylesheetUrl;
var MobileMenu = {
  config: {},
  toggleNav: function toggleNav() {
    var actualPage = MobileMenu.config.toggles.actualPage;
    var mobileMenu = MobileMenu.config.toggles.mobileMenu;
    var header = MobileMenu.config.toggles.header;
    var body = MobileMenu.config.toggles.body;
    actualPage.el.toggleClass(actualPage.classToggle);
    mobileMenu.el.toggleClass(mobileMenu.classToggle);
    body.el.toggleClass(body.classToggle);

    // If it's a fixed header, move it left as well. Otherwise, let the actual page move everything
    if (MobileMenu.config.flags.fixedHeader) {
      header.el.toggleClass(header.classToggle);
    }
  },
  toggleSubNav: function toggleSubNav(parent) {
    parent.toggleClass("active");
    parent.children(".sub-menu").slideToggle(500);
  },
  slideToggleNav: function slideToggleNav() {
    MobileMenu.config.targets.mobileNavContainer.slideToggle(300);
  },
  setUpIcons: function setUpIcons() {
    var ajax = new XMLHttpRequest();
    var closeIconContainer = MobileMenu.config.targets.mobileMenuCloseIconContainer;
    ajax.open("GET", stylesheetUrl + "/assets/icons/close.svg", true);
    ajax.send();
    ajax.onload = function (e) {
      closeIconContainer.html(ajax.responseText);
    };
  },
  init: function init(template_url, configObj) {
    configObj = configObj || {
      toggles: {},
      targets: {},
      flags: {}
    };
    configObj.toggles.actualPage = configObj.toggles.actualPage || {
      el: jQuery(".site-container"),
      classToggle: "move-left"
    };
    configObj.toggles.header = configObj.toggles.header || {
      el: jQuery("header"),
      classToggle: "move-left"
    };
    configObj.toggles.mobileMenu = configObj.toggles.mobileMenu || {
      el: jQuery(".mobile-menu"),
      classToggle: "move-in"
    };
    configObj.toggles.body = configObj.toggles.body || {
      el: jQuery("body"),
      classToggle: "locked"
    };
    configObj.targets.mobileNavigation = configObj.targets.mobileNavigation || jQuery(".mobile-navigation");
    configObj.targets.mobileNavContainer = configObj.targets.mobileNavContainer || jQuery(".mobile-navigation-container");
    configObj.targets.mobileMenuIcon = configObj.targets.mobileMenuIcon || jQuery(".mobile-menu-icon");
    configObj.targets.mobileMenuCloseIconContainer = configObj.targets.mobileMenuCloseIconContainer || configObj.toggles.mobileMenu.el.find(".icon-close-container");
    configObj.flags.fixedHeader = configObj.flags.fixedHeader || false;
    configObj.templateUrl = configObj.templateUrl || template_url || "";
    MobileMenu.config = configObj;
    MobileMenu.config.targets.mobileMenuIcon.on("click", MobileMenu.toggleNav);
    MobileMenu.config.targets.mobileMenuIcon.on("click", MobileMenu.slideToggleNav);
    MobileMenu.config.targets.mobileMenuCloseIconContainer.on("click", MobileMenu.toggleNav);
    jQuery(".mobile-menu .menu").children("li.menu-item-has-children").on("click", function () {
      MobileMenu.toggleSubNav(jQuery(this));
    });
    MobileMenu.setUpIcons();
  }
};

/***/ }),

/***/ "./assets/js/sitewide.js":
/*!*******************************!*\
  !*** ./assets/js/sitewide.js ***!
  \*******************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _mobile_menu_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./mobile-menu.js */ "./assets/js/mobile-menu.js");
/* harmony import */ var _acf_maps_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./acf-maps.js */ "./assets/js/acf-maps.js");



// SiteHeader Animation on scroll
var scrollCheck = function scrollCheck() {
  var alertBarHeight = jQuery(".alert-bar").outerHeight();
  var position = jQuery(window).scrollTop();
  if (jQuery("body").hasClass("show-alert-bar")) {
    if (position > alertBarHeight) {
      jQuery(".site-header, .site-title a").addClass("scrolled");
      jQuery(".site-header, .site-title a").removeClass("unscrolled");
    } else {
      jQuery(".site-header, .site-title a").removeClass("scrolled");
      jQuery(".site-header, .site-title a").addClass("unscrolled");
    }
  } else {
    if (position > 0) {
      jQuery(".site-header, .site-title a").addClass("scrolled");
      jQuery(".site-header, .site-title a").removeClass("unscrolled");
    } else {
      jQuery(".site-header, .site-title a").removeClass("scrolled");
      jQuery(".site-header, .site-title a").addClass("unscrolled");
    }
  }
};
jQuery(document).ready(function () {
  // Initialize Mobile Menu
  _mobile_menu_js__WEBPACK_IMPORTED_MODULE_0__.MobileMenu.init();
  scrollCheck();
  jQuery(window).scroll(function () {
    scrollCheck();
  });

  // Initialize Google Map on single location
  jQuery(".acf-map").each(function () {
    _acf_maps_js__WEBPACK_IMPORTED_MODULE_1__.ACFMaps.initMap(jQuery(this));
  });

  // Accordion
  jQuery(".accordion-row-header").on("click", function () {
    var accordionRow = jQuery(this).parent(".accordion-row");
    var content = accordionRow.find(".accordion-row-content");
    if (accordionRow.hasClass("active-ar")) {
      content.slideUp(300);
      accordionRow.removeClass("active-ar");
    } else {
      jQuery(".accordion-row.active-ar").find(".accordion-row-content").slideUp(300);
      jQuery(".accordion-row.active-ar").removeClass("active-ar");
      content.slideDown(300);
      accordionRow.addClass("active-ar");
    }
  });

  // Check to see if menu goes outside of container
  jQuery(".primary-nav-wrap .menu-item.menu-item-has-children").on("mouseover", function (e) {
    if (jQuery(this).find(".sub-menu").length) {
      var elm = jQuery(this).children(".sub-menu");
      var off = elm.offset();
      var l = off.left;
      var w = elm.width();
      var docH = jQuery("body").height();
      var docW = jQuery("body").width();
      var isEntirelyVisible = l + w <= docW;
      if (!isEntirelyVisible) {
        jQuery(this).children(".sub-menu").addClass("edge");
      }
    }
  });

  // Simply Smooth scrolling. Requires two things.
  // 1. Use an a tag with an href contianing the id of an element so <a href="#target-div">Click to go to Target</a>
  // 2. Have a div/element with the id of you use in the href above
  // This was adapted from here - https://www.abeautifulsite.net/smoothly-scroll-to-an-element-without-a-jquery-plugin-2

  //   Probably don't need this anymore will comment out for now, using scroll-behavior: smooth; in css instead

  //   jQuery('a[href^="#"]').on("click", function (event) {
  //     var target = jQuery(this.getAttribute("href"));
  //     if (target.length) {
  //       event.preventDefault();
  //       jQuery("html, body").stop().animate(
  //         {
  //           scrollTop: target.offset().top,
  //         },
  //         1000
  //       );
  //     }
  //   });

  // Setting up Modaal Gallery for our photo grid section
  jQuery(".section.gallery").modaal({
    type: "image"
  });

  // Setting up Modaal Gallery for any linked photos
  jQuery('a[href$=".gif"], a[href$=".jpg"], a[href$=".jpeg"], a[href$=".png"], a[href$=".bmp"]').modaal({
    type: "image"
  });

  // Click to display search form in header
  jQuery(".top-icon-section__search, .header-search-box__close-search").on("click", function (event) {
    jQuery(".header-search-box, body").toggleClass("search-is-open");
    jQuery('.header-search-box input[type="search"]').focus();
  });
  var bannerSliderOuter = jQuery(".banner-slider-outer");
  if (bannerSliderOuter.length > 0) {
    bannerSliderOuter.each(function () {
      var theSliderOuter = jQuery(this);
      var theSlider = theSliderOuter.find(".banner-slider");
      theSlider.slick({
        arrows: true,
        autoplay: false,
        adaptiveHeight: true,
        nextArrow: theSliderOuter.find(".right-arrow"),
        prevArrow: theSliderOuter.find(".left-arrow")
      });
    });
  }
  var ctaSliderWrap = jQuery(".cta-slider-wrap");
  if (ctaSliderWrap.length > 0) {
    ctaSliderWrap.each(function () {
      var theSliderOuter = jQuery(this);
      var theSlider = theSliderOuter.find(".cta-slider");
      theSlider.slick({
        arrows: true,
        autoplay: true,
        adaptiveHeight: true,
        autoplaySpeed: 10000,
        nextArrow: theSliderOuter.find(".right-arrow"),
        prevArrow: theSliderOuter.find(".left-arrow")
      });
    });
  }
  var testimonialSliders = jQuery(".testimonials-slider-wrap");
  if (testimonialSliders.length > 0) {
    testimonialSliders.each(function () {
      var sliderWrap = jQuery(this);
      var theSlider = sliderWrap.children(".testimonials-slider");
      theSlider.slick({
        arrows: true,
        autoplay: false,
        adaptiveHeight: true,
        nextArrow: sliderWrap.find(".right-arrow"),
        prevArrow: sliderWrap.find(".left-arrow"),
        slidesToShow: 1
      });
    });
  }
  var storiesSliders = jQuery(".stories-slider-wrap");
  if (storiesSliders.length > 0) {
    storiesSliders.each(function () {
      var sliderWrap = jQuery(this);
      var theSlider = sliderWrap.children(".stories-slider");
      theSlider.slick({
        arrows: true,
        autoplay: false,
        nextArrow: sliderWrap.find(".right-arrow"),
        prevArrow: sliderWrap.find(".left-arrow"),
        slidesToShow: 1,
        adaptiveHeight: true
      });
    });
  }
  var eventsSlider = jQuery(".events-list-slider");
  if (eventsSlider.length > 0) {
    eventsSlider.each(function () {
      var sliderWrap = jQuery(this);
      var theSlider = sliderWrap.children(".events-list-slides");
      theSlider.slick({
        arrows: true,
        autoplay: false,
        nextArrow: sliderWrap.find(".right-arrow"),
        prevArrow: sliderWrap.find(".left-arrow"),
        slidesToShow: 2,
        responsive: [{
          breakpoint: 700,
          settings: {
            slidesToShow: 1
          }
        }]
      });
    });
  }
  var blogSlider = jQuery(".blog-feed-slider");
  if (blogSlider.length > 0) {
    blogSlider.each(function () {
      var sliderWrap = jQuery(this);
      var theSlider = sliderWrap.children(".blog-feed-slides");
      theSlider.slick({
        arrows: true,
        autoplay: false,
        nextArrow: sliderWrap.find(".right-arrow"),
        prevArrow: sliderWrap.find(".left-arrow"),
        slidesToShow: 3,
        responsive: [{
          breakpoint: 960,
          settings: {
            slidesToShow: 2
          }
        }, {
          breakpoint: 600,
          settings: {
            slidesToShow: 1
          }
        }]
      });
    });
  }
  var projectsSlider = jQuery(".projects-feed-outer");
  if (projectsSlider.length > 0) {
    projectsSlider.each(function () {
      var sliderWrap = jQuery(this);
      var theSlider = sliderWrap.children(".projects-feed");
      theSlider.slick({
        arrows: true,
        autoplay: false,
        nextArrow: sliderWrap.find(".right-arrow"),
        prevArrow: sliderWrap.find(".left-arrow"),
        slidesToShow: 3,
        responsive: [{
          breakpoint: 960,
          settings: {
            slidesToShow: 2
          }
        }, {
          breakpoint: 600,
          settings: {
            slidesToShow: 1
          }
        }]
      });
    });
  }
  jQuery(".cta-slide-video").on("canplaythrough", function () {
    this.play();
  });
  var logoSliders = jQuery(".logos-slider");
  if (logoSliders.length > 0) {
    logoSliders.each(function () {
      var sliderWrap = jQuery(this);
      var theSlider = sliderWrap.children(".logos-slides");
      theSlider.slick({
        arrows: true,
        autoplay: true,
        nextArrow: sliderWrap.find(".right-arrow"),
        prevArrow: sliderWrap.find(".left-arrow"),
        autoplaySpeed: 7000,
        infinite: true,
        slidesToShow: 5,
        slidesToScroll: 1,
        responsive: [{
          breakpoint: 1100,
          settings: {
            slidesToShow: 4
          }
        }, {
          breakpoint: 900,
          settings: {
            slidesToShow: 3
          }
        }, {
          breakpoint: 600,
          settings: {
            slidesToShow: 2
          }
        }, {
          breakpoint: 400,
          settings: {
            slidesToShow: 1
          }
        }]
      });
    });
  }
});

/***/ }),

/***/ "./assets/styles/style.scss":
/*!**********************************!*\
  !*** ./assets/styles/style.scss ***!
  \**********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./assets/styles/editor-style.scss":
/*!*****************************************!*\
  !*** ./assets/styles/editor-style.scss ***!
  \*****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	(() => {
/******/ 		var deferred = [];
/******/ 		__webpack_require__.O = (result, chunkIds, fn, priority) => {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			var notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				var [chunkIds, fn, priority] = deferred[i];
/******/ 				var fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every((key) => (__webpack_require__.O[key](chunkIds[j])))) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					var r = fn();
/******/ 					if (r !== undefined) result = r;
/******/ 				}
/******/ 			}
/******/ 			return result;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	(() => {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"/front": 0,
/******/ 			"editor-style": 0,
/******/ 			"front": 0
/******/ 		};
/******/ 		
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = (chunkId) => (installedChunks[chunkId] === 0);
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = (parentChunkLoadingFunction, data) => {
/******/ 			var [chunkIds, moreModules, runtime] = data;
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			if(chunkIds.some((id) => (installedChunks[id] !== 0))) {
/******/ 				for(moduleId in moreModules) {
/******/ 					if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 						__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 					}
/******/ 				}
/******/ 				if(runtime) var result = runtime(__webpack_require__);
/******/ 			}
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkId] = 0;
/******/ 			}
/******/ 			return __webpack_require__.O(result);
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = self["webpackChunkaccel434"] = self["webpackChunkaccel434"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	__webpack_require__.O(undefined, ["editor-style","front"], () => (__webpack_require__("./assets/js/sitewide.js")))
/******/ 	__webpack_require__.O(undefined, ["editor-style","front"], () => (__webpack_require__("./assets/styles/style.scss")))
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, ["editor-style","front"], () => (__webpack_require__("./assets/styles/editor-style.scss")))
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;
//# sourceMappingURL=front.js.map