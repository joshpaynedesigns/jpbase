import { MobileMenu } from "./mobile-menu.js";
import { ACFMaps } from "./acf-maps.js";

// SiteHeader Animation on scroll
var scrollCheck = function () {
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

// Remove banner bg videos on mobile
jQuery(function() {
  const bgv = jQuery('.cta-slide-video');

  if (bgv.is(':visible')) {
    jQuery('source', bgv).each(
      function() {
        const el = jQuery(this);
        el.attr('src', el.data('src'));
      }
    );
    bgv[0].load();
  }
});

jQuery(document).ready(function () {
  // Initialize Mobile Menu
  MobileMenu.init();

  scrollCheck();

  jQuery(window).scroll(function () {
    scrollCheck();
  });

  // Initialize Google Map on single location
  jQuery(".acf-map").each(function () {
    ACFMaps.initMap(jQuery(this));
  });

  // Accordion
  jQuery(".accordion-row-header").on("click", function () {
    var accordionRow = jQuery(this).parent(".accordion-row");
    var content = accordionRow.find(".accordion-row-content");

    if (accordionRow.hasClass("active-ar")) {
      content.slideUp(300);
      accordionRow.removeClass("active-ar");
    } else {
      jQuery(".accordion-row.active-ar")
        .find(".accordion-row-content")
        .slideUp(300);
      jQuery(".accordion-row.active-ar").removeClass("active-ar");

      content.slideDown(300);
      accordionRow.addClass("active-ar");
    }
  });

  // Check to see if menu goes outside of container
  jQuery(".primary-nav-wrap .menu-item.menu-item-has-children").on(
    "mouseover",
    function (e) {
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
    }
  );

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
    type: "image",
  });

  // Setting up Modaal Gallery for any linked photos
  jQuery(
    'a[href$=".gif"], a[href$=".jpg"], a[href$=".jpeg"], a[href$=".png"], a[href$=".bmp"]'
  ).modaal({
    type: "image",
  });

  // Click to display search form in header
  jQuery(".top-icon-section__search, .header-search-box__close-search").on(
    "click",
    function (event) {
      jQuery(".header-search-box, body").toggleClass("search-is-open");
      jQuery('.header-search-box input[type="search"]').focus();
    }
  );

  let bannerSliderOuter = jQuery(".banner-slider-outer");
  if (bannerSliderOuter.length > 0) {
    bannerSliderOuter.each(function () {
      let theSliderOuter = jQuery(this);
      let theSlider = theSliderOuter.find(".banner-slider");

      theSlider.slick({
        arrows: true,
        autoplay: false,
        adaptiveHeight: true,
        nextArrow: theSliderOuter.find(".right-arrow"),
        prevArrow: theSliderOuter.find(".left-arrow"),
      });
    });
  }

  let ctaSliderWrap = jQuery(".cta-slider-wrap");
  if (ctaSliderWrap.length > 0) {
    ctaSliderWrap.each(function () {
      let theSliderOuter = jQuery(this);
      let theSlider = theSliderOuter.find(".cta-slider");

      theSlider.slick({
        arrows: true,
        autoplay: true,
        adaptiveHeight: true,
        autoplaySpeed: 10000,
        nextArrow: theSliderOuter.find(".right-arrow"),
        prevArrow: theSliderOuter.find(".left-arrow"),
      });
    });
  }

  let testimonialSliders = jQuery(".testimonials-slider-wrap");
  if (testimonialSliders.length > 0) {
    testimonialSliders.each(function () {
      let sliderWrap = jQuery(this);
      let theSlider = sliderWrap.children(".testimonials-slider");

      theSlider.slick({
        arrows: true,
        autoplay: false,
        adaptiveHeight: true,
        nextArrow: sliderWrap.find(".right-arrow"),
        prevArrow: sliderWrap.find(".left-arrow"),
        slidesToShow: 1,
      });
    });
  }

  let storiesSliders = jQuery(".stories-slider-wrap");
  if (storiesSliders.length > 0) {
    storiesSliders.each(function () {
      let sliderWrap = jQuery(this);
      let theSlider = sliderWrap.children(".stories-slider");

      theSlider.slick({
        arrows: true,
        autoplay: false,
        nextArrow: sliderWrap.find(".right-arrow"),
        prevArrow: sliderWrap.find(".left-arrow"),
        slidesToShow: 1,
        adaptiveHeight: true,
      });
    });
  }

  let eventsSlider = jQuery(".events-list-slider");
  if (eventsSlider.length > 0) {
    eventsSlider.each(function () {
      let sliderWrap = jQuery(this);
      let theSlider = sliderWrap.children(".events-list-slides");

      theSlider.slick({
        arrows: true,
        autoplay: false,
        nextArrow: sliderWrap.find(".right-arrow"),
        prevArrow: sliderWrap.find(".left-arrow"),
        slidesToShow: 2,
        responsive: [
          {
            breakpoint: 700,
            settings: {
              slidesToShow: 1,
            },
          },
        ],
      });
    });
  }

  let blogSlider = jQuery(".blog-feed-slider");
  if (blogSlider.length > 0) {
    blogSlider.each(function () {
      let sliderWrap = jQuery(this);
      let theSlider = sliderWrap.children(".blog-feed-slides");

      theSlider.slick({
        arrows: true,
        autoplay: false,
        nextArrow: sliderWrap.find(".right-arrow"),
        prevArrow: sliderWrap.find(".left-arrow"),
        slidesToShow: 3,
        responsive: [
          {
            breakpoint: 960,
            settings: {
              slidesToShow: 2,
            },
          },
          {
            breakpoint: 600,
            settings: {
              slidesToShow: 1,
            },
          },
        ],
      });
    });
  }

  let projectsSlider = jQuery(".projects-feed-outer");
  if (projectsSlider.length > 0) {
    projectsSlider.each(function () {
      let sliderWrap = jQuery(this);
      let theSlider = sliderWrap.children(".projects-feed");

      theSlider.slick({
        arrows: true,
        autoplay: false,
        nextArrow: sliderWrap.find(".right-arrow"),
        prevArrow: sliderWrap.find(".left-arrow"),
        slidesToShow: 3,
        responsive: [
          {
            breakpoint: 960,
            settings: {
              slidesToShow: 2,
            },
          },
          {
            breakpoint: 600,
            settings: {
              slidesToShow: 1,
            },
          },
        ],
      });
    });
  }

  jQuery(".cta-slide-video").on("canplaythrough", function () {
    this.play();
  });

  let logoSliders = jQuery(".logos-slider");
  if (logoSliders.length > 0) {
    logoSliders.each(function () {
      let sliderWrap = jQuery(this);
      let theSlider = sliderWrap.children(".logos-slides");

      theSlider.slick({
        arrows: true,
        autoplay: true,
        nextArrow: sliderWrap.find(".right-arrow"),
        prevArrow: sliderWrap.find(".left-arrow"),
        autoplaySpeed: 7000,
        infinite: true,
        slidesToShow: 5,
        slidesToScroll: 1,
        responsive: [
          {
            breakpoint: 1100,
            settings: {
              slidesToShow: 4,
            },
          },
          {
            breakpoint: 900,
            settings: {
              slidesToShow: 3,
            },
          },
          {
            breakpoint: 600,
            settings: {
              slidesToShow: 2,
            },
          },
          {
            breakpoint: 400,
            settings: {
              slidesToShow: 1,
            },
          },
        ],
      });
    });
  }
});
