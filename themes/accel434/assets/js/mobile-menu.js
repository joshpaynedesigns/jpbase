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
