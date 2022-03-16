// SiteHeader Animation on scroll
var scrollCheck = function () {
	var alertBarHeight = jQuery('.alert-bar').outerHeight();
	var position = jQuery(window).scrollTop();
	if (jQuery('body').hasClass('show-alert-bar')) {
		if (position > alertBarHeight) {
			jQuery('.site-header').css('top', 0);
			jQuery('.site-header, .site-title a').addClass('scrolled');
			jQuery('.site-header, .site-title a').removeClass('unscrolled');
		} else {
			jQuery('.site-header').css('top', alertBarHeight);
			jQuery('.site-header, .site-title a').removeClass('scrolled');
			jQuery('.site-header, .site-title a').addClass('unscrolled');
		}
	} else {
		if (position > 0) {
			jQuery('.site-header, .site-title a').addClass('scrolled');
			jQuery('.site-header, .site-title a').removeClass('unscrolled');
		} else {
			jQuery('.site-header, .site-title a').removeClass('scrolled');
			jQuery('.site-header, .site-title a').addClass('unscrolled');
		}
	}
};

jQuery(document).ready(function() {
	// Initialize Mobile Menu
	MobileMenu.init();

	// Initialize accessible menus
	jQuery(document).gamajoAccessibleMenu();

	// Run the svg for everyone
	svg4everybody({
		polyfill: true
	});

	scrollCheck();

	jQuery(window).scroll(function () {
		scrollCheck();
	});

	// Accordion
	jQuery('.accordion-row-header').on('click', function() {
        jQuery('.accordion-row-content').slideUp(300);
        if (jQuery(this).hasClass('active-ar')){
            jQuery(this).removeClass("active-ar");
        } else {
            jQuery('.accordion-row-header').removeClass("active-ar");
            jQuery(this).next('.accordion-row-content').slideDown(300);
            jQuery(this).addClass("active-ar");
            return false;
        }
    });

	// Check to see if menu goes outside of container
	jQuery('.genesis-nav-menu .menu-item').on('mouseenter mouseleave', function(e) {
		if (jQuery(this).find('.sub-menu').length) {
			var elm = jQuery(this).children('.sub-menu');
			var off = elm.offset();
			var l = off.left;
			var w = elm.width();
			var docH = jQuery("body").height();
			var docW = jQuery("body").width();

			var isEntirelyVisible = (l + w <= docW);

			if (!isEntirelyVisible) {
				console.log("inside menu edge");
				jQuery(this).children('.sub-menu').addClass('edge');
			} else {
				jQuery(this).children('.sub-menu').removeClass('edge');
			}
		}
	});

	// Simply Smooth scrolling. Requires two things.
	// 1. Use an a tag with an href contianing the id of an element so <a href="#target-div">Click to go to Target</a>
	// 2. Have a div/element with the id of you use in the href above
	// This was adapted from here - https://www.abeautifulsite.net/smoothly-scroll-to-an-element-without-a-jquery-plugin-2

	jQuery('a[href^="#"]').on('click', function(event) {
	    var target = jQuery(this.getAttribute('href'));
	    if( target.length ) {
	        event.preventDefault();
	        jQuery('html, body').stop().animate({
	            scrollTop: target.offset().top
	        }, 1000);
	    }
	});

	// Setting up Modaal Gallery for our photo grid section
	jQuery('.section.gallery').modaal({
		type: 'image'
	});

	// Setting up Modaal Gallery for any linked photos
	jQuery('a[href$=".gif"], a[href$=".jpg"], a[href$=".jpeg"], a[href$=".png"], a[href$=".bmp"]').modaal({
		type: 'image'
	});

	// Click to display search form in header
	jQuery( '.top-icon-section__search, .header-search-box__close-search' ).on( 'click', function( event ) {
		jQuery('.header-search-box, body').toggleClass("search-is-open");
		jQuery('.header-search-box input[type="search"]').focus();
	});

	jQuery('.page-banner-slider.actual-slider').slick({
		arrows: true,
		autoplay: true,
		autoplaySpeed: 7000,
		nextArrow: jQuery('.page-banner-slider .right-arrow'),
		prevArrow: jQuery('.page-banner-slider .left-arrow'),
	});

	jQuery('#banner-slide-video').on('canplaythrough', function() {
	   this.play();
	});

	jQuery('.testimonials-slider').slick({
		arrows: true,
		autoplay: true,
		autoplaySpeed: 7000,
		adaptiveHeight: true,
		nextArrow: jQuery('.testimonials-slider-wrap .right-arrow'),
		prevArrow: jQuery('.testimonials-slider-wrap .left-arrow'),
	});

	jQuery('.blog-feed-slides').slick({
        arrows: true,
        autoplay: true,
        autoplaySpeed: 7000,
        infinite: true,
        slidesToShow: 3,
        slidesToScroll: 1,
        nextArrow: jQuery('.blog-feed-slider .right-arrow'),
        prevArrow: jQuery('.blog-feed-slider .left-arrow'),
        responsive: [
        {
          breakpoint: 900,
          settings: {
            slidesToShow: 2
          }
        },
        {
	      breakpoint: 550,
	      settings: {
	        slidesToShow: 1
	      }
	    }
      ]
    });

    jQuery('.projects-feed').slick({
		arrows: true,
		autoplay: true,
		autoplaySpeed: 7000,
		infinite: true,
		slidesToShow: 4,
		slidesToScroll: 1,
		nextArrow: jQuery('.projects-feed-outer .right-arrow'),
        prevArrow: jQuery('.projects-feed-outer .left-arrow'),
		responsive: [{
			breakpoint: 1100,
			settings: {
				slidesToScroll: 1,
				slidesToShow: 3,
			}
		},
		{
			breakpoint: 900,
			settings: {
				slidesToShow: 2,
			}
		},
		{
			breakpoint: 600,
			settings: {
				slidesToShow: 1,
			}
		}]
	});

});