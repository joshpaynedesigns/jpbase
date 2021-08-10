/**
 * Adds the menu toggle and responsive menu logic.
 */
docs.menu = ( function(){
	let screenWidth = 'wide';
	const element = document.querySelector('.nav');
	const button = document.querySelector('#menu-button');
	const filterForm = document.querySelector('#menu-filter-form');

	function init() {
		setVisibility();
		button.addEventListener('click', toggle, false);
		window.addEventListener('resize', setVisibility);
	}

	function toggle() {
		let expanded = button.getAttribute('aria-expanded') === 'true' || false;
		button.setAttribute('aria-expanded', !expanded);
		element.hidden = !element.hidden;
		filterForm.hidden = !filterForm.hidden;
	}

	function setVisibility() {
		if (window.innerWidth >= docs.config.breakPoint) {
			button.setAttribute('aria-expanded', true);
			button.hidden = true;
			element.hidden = false;
			filterForm.hidden = false;
			screenWidth = 'wide';
			return;
		}

		// Hide menu only on load and if screen changed from wide state
		// to narrow. Prevents issue with iOS collapsing open menus on scroll,
		// due to Mobile Safari firing resize events when scrolling down.
		if (screenWidth == 'wide') {
			button.setAttribute('aria-expanded', false);
			button.hidden = false;
			filterForm.hidden = true;
			element.hidden = true;
			screenWidth = 'narrow';
		}
	}

	return {
		init: init
	}
})();

docs.menu.init();
