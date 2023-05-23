var stylesheetUrl = data.stylesheetUrl;

var MobileMenu = {
    config: {},
    toggleNav: function() {
        var actualPage = MobileMenu.config.toggles.actualPage;
        var mobileMenu = MobileMenu.config.toggles.mobileMenu;
        var header = MobileMenu.config.toggles.header;
        var body = MobileMenu.config.toggles.body;

        actualPage.el.toggleClass(actualPage.classToggle);
        mobileMenu.el.toggleClass(mobileMenu.classToggle);
        body.el.toggleClass(body.classToggle);

        // If it's a fixed header, move it left as well. Otherwise, let the actual page move everything
        if(MobileMenu.config.flags.fixedHeader) {
            header.el.toggleClass(header.classToggle);
        }
    },
    toggleSubNav: function(parent) {
        parent.toggleClass("active");
        parent.children(".sub-menu").slideToggle(500);
    },
    slideToggleNav: function() {
        MobileMenu.config.targets.mobileNavContainer.slideToggle(300);
    },
    setUpIcons: function() {
        var ajax = new XMLHttpRequest();
        var closeIconContainer = MobileMenu.config.targets.mobileMenuCloseIconContainer;

        ajax.open("GET", stylesheetUrl + "/assets/icons/src/close.svg", true);
        ajax.send();
        ajax.onload = function(e) {
            closeIconContainer.html(ajax.responseText);
        }
    },
    init: function(template_url, configObj) {
        configObj = configObj || { toggles: {}, targets: {}, flags: {} };
        configObj.toggles.actualPage = configObj.toggles.actualPage || { el: jQuery(".site-container"), classToggle: 'move-left' };
        configObj.toggles.header = configObj.toggles.header || { el: jQuery("header"), classToggle: 'move-left' };
        configObj.toggles.mobileMenu = configObj.toggles.mobileMenu || { el: jQuery(".mobile-menu"), classToggle: 'move-in' };
        configObj.toggles.body = configObj.toggles.body || { el: jQuery("body"), classToggle: 'locked' };

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
        jQuery(".mobile-menu .menu").children("li.menu-item-has-children").on("click", function(){
            MobileMenu.toggleSubNav(jQuery(this));
        });

        MobileMenu.setUpIcons();
    }
};

// SiteHeader Animation on scroll
var scrollCheck = function () {
  var alertBarHeight = jQuery(".alert-bar").outerHeight();
  var position = jQuery(window).scrollTop();
  if (jQuery("body").hasClass("show-alert-bar")) {
    if (position > alertBarHeight) {
      jQuery(".site-header").css("top", 0);
      jQuery(".site-header, .site-title a").addClass("scrolled");
      jQuery(".site-header, .site-title a").removeClass("unscrolled");
    } else {
      jQuery(".site-header").css("top", alertBarHeight);
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
  MobileMenu.init();

  // Initialize accessible menus
  jQuery(document).gamajoAccessibleMenu();

  // Run the svg for everyone
  svg4everybody({
    polyfill: true,
  });

  //   scrollCheck();

  //   jQuery(window).scroll(function () {
  //     scrollCheck();
  //   });

  // Accordion
  jQuery(".accordion-row-header").on("click", function () {
    jQuery(".accordion-row-content").slideUp(300);
    if (jQuery(this).hasClass("active-ar")) {
      jQuery(this).removeClass("active-ar");
    } else {
      jQuery(".accordion-row-header").removeClass("active-ar");
      jQuery(this).next(".accordion-row-content").slideDown(300);
      jQuery(this).addClass("active-ar");
      return false;
    }
  });

  jQuery(".micro-accordion-title").on("click", function () {
    let content = jQuery(this).next(".micro-accordion-content");
    content.toggleClass("is-open");
  });

  // Check to see if menu goes outside of container
  jQuery(".primary-nav-wrap .menu-item.menu-item-has-children").on(
    "mouseover",
    function (e) {
      console.log("hover");
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

  jQuery('a[href^="#"]').on("click", function (event) {
    var target = jQuery(this.getAttribute("href"));
    if (target.length) {
      event.preventDefault();
      jQuery("html, body").stop().animate(
        {
          scrollTop: target.offset().top,
        },
        1000
      );
    }
  });

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
        autoplaySpeed: 10000,
        nextArrow: theSliderOuter.find(".right-arrow"),
        prevArrow: theSliderOuter.find(".left-arrow"),
      });
    });
  }

  let announcementsSliders = jQuery(".announcements-slider");
  if (announcementsSliders.length > 0) {
    announcementsSliders.each(function () {
      let sliderWrap = jQuery(this);
      let theSlider = sliderWrap.children(".slides-wrap");

      theSlider.slick({
        arrows: true,
        autoplay: false,
        nextArrow: sliderWrap.find(".right-arrow"),
        prevArrow: sliderWrap.find(".left-arrow"),
        slidesToShow: 1,
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

  jQuery("#cta-slide-video").on("canplaythrough", function () {
    this.play();
  });

  jQuery(".logos-slider .logos-slides").slick({
    arrows: true,
    autoplay: true,
    autoplaySpeed: 7000,
    infinite: true,
    slidesToShow: 5,
    slidesToScroll: 1,
    nextArrow: jQuery(".logos-slider .right-arrow"),
    prevArrow: jQuery(".logos-slider .left-arrow"),
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
