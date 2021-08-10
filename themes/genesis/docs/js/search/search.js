/**
 * Adds the search filter to the navigation menu.
 *
 * Searching replaces the nav menu with a list of pages
 * that contain the search term as it is typed.
 *
 * Depends on the search-index.txt that is built
 * from the docs collection in `search.11ty.js`.
 */
docs.search = ( function(){
	// Cache query selectors.
	const menuFilterInput = document.querySelector("#menu-filter");
	const menuFilterForm = document.querySelector("#menu-filter-form");
	const nav = document.querySelector(".nav");
	const navItems = nav.querySelectorAll('.nav-item');
	const ariaText = document.querySelector('#menu-filter-aria-live');

	// Used in ARIA Live text to show how many of the total elements appear.
	const navLinksLength = document.querySelectorAll('.nav a').length;

	// Truthy when the search index has been remotely loaded from search-index.txt.
	// Set to false by default to prevent searches before the search index is available.
	let searchIndex = false;

	// True if the original static nav elements are hidden.
	let navIsHidden = false;

	// Initialize the search index.
	// Changes here should be reflected in the index in search.11ty.js.
	// See https://github.com/nextapps-de/flexsearch#flexsearch.create.
	let index = FlexSearch.create({
		doc: {
			id: "id",
			field: ["title" , "menuTitle", "content"], // Index these fields.
			store: ["menuTitle", "url"] // Return these fields in search results.
		},
		filter: "en" // Strip common English words.
	});

	/**
	 * Hide the original navigation items.
	 */
	function hideNavItems() {
		if ( navIsHidden ) return;
		navItems.forEach(function(item){
			item.setAttribute('hidden','');
		});
		navIsHidden = true;
	}

	/**
	 * Restore the original navigation items.
	 */
	function restoreNavItems() {
		if ( ! navIsHidden ) return;
		navItems.forEach(function(item){
			item.removeAttribute('hidden');
		});
		navIsHidden = false;
	}

	/**
	 * Remove the old results from the navigation area.
	 */
	function removeOldResults() {
		let oldResults = nav.querySelectorAll('.search-result');
		oldResults.forEach(function(item){
			item.parentNode.removeChild(item);
		});
	}

	/**
	 * Display search results.
	 * @param results An array of search results.
	 */
	function display(results) {
		let i = 1;
		results.forEach(function(result){
			let li = document.createElement("li");
			let link = document.createElement("a");
			let label = document.createTextNode(result.menuTitle);
			let url = (docs.config.rootURL + result.url).replace('//','/');

			li.setAttribute('class', 'search-result');
			li.appendChild(link);
			link.setAttribute('href', url);
			link.appendChild(label);
			nav.appendChild(li);

			if ( i === 1 ) {
				link.setAttribute('aria-label', result.menuTitle + ". First search result.");
			}

			if ( i === results.length ) {
				link.setAttribute('aria-label', result.menuTitle + ". Final search result.");
			}
			i++;
		});

		ariaText.textContent = `${results.length} pages shown of ${navLinksLength} (filtered).`;
	}

	/**
	 * Add event listeners.
	 */
	function init() {
		// On focus, retrieve the `search-index.txt` file and import it as the search index.
		menuFilterInput.addEventListener('focus', function(){
			if ( searchIndex ) return;
			fetch(`${docs.config.rootURL}search-index.txt`)
				.then(response => response.text())
				.then(data => {
					searchIndex = data;
					index.import(searchIndex);
				});
		});

		// On keyup, perform a search and update results.
		menuFilterInput.addEventListener('keyup', function(e){
			let searchText = e.target.value;
			ariaText.textContent = '';
			if ( searchText.length === 0 ) {
				removeOldResults();
				restoreNavItems();
				ariaText.textContent = `Showing all pages.`;
				return;
			}
			if ( ! searchIndex ) return;
			index.search(searchText, function(results){
				hideNavItems();
				removeOldResults();
				display(results);
			});
		});

		// Prevent submission of search form. Stops unneeded page refresh on submission attempts.
		// Search is as-you-type only with results replacing menu items.
		menuFilterForm.addEventListener( 'submit', function(e){
			e.preventDefault();
		});
	}

	return {
		init: init,
	}
})();

docs.search.init();
