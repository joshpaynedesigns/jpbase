/**
 * Generates the static `search-index.txt` file at build time using the docs
 * collection. Improves speed of browser-based searches and allows the menu
 * search filter to include page content as well as page titles.
 *
 * The `search-index.txt` only loads if the user focuses the search field,
 * as controlled by `docs/js/search/search.js`.
 */
class Search {
	data() {
		return {
			permalink: "search-index.txt",
		};
	}

	render(data) {
		let FlexSearch = require("flexsearch");
		require("flexsearch/lang/en.min.js");
		// Initialize the search index.
		// Changes here should be reflected in the index in search.js.
		// See https://github.com/nextapps-de/flexsearch#flexsearch.create.
		let index = FlexSearch.create({
			doc: {
				id: "id",
				field: ["title" , "menuTitle", "content"], // Index these fields.
				store: ["menuTitle", "url"] // Return these fields in search results.
			},
			filter: "en" // Strip common English words.
		});

		let i = 0;
		data.collections.docs.forEach(doc => {
			if ( doc.data.menuTitle ) {
				index.add( {
					id: i,
					title: doc.data.title,
					menuTitle: doc.data.menuTitle,
					content: doc.templateContent,
					url: doc.url
				} );
			}
			i++;
		});

		return index.export();
	}
}

module.exports = Search;
