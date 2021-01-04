/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/#registering-a-block
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * All files containing `style` keyword are bundled together. The code used
 * gets applied both to the front of your site and to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */

import './style.scss';

const el = wp.element.createElement;

const ac_logo = el('svg', { width: 20, height: 20, viewBox: "0 0 288 288"},
	el('path', {
		d: "M35.8 0H251.8C271.7 0 287.8 16.1 287.8 36V252C287.8 271.9 271.7 288 251.8 288H35.8C15.9 288 -0.200012 271.9 -0.200012 252V36C-0.200012 16.1 15.9 0 35.8 0Z",
		fill: "#356AE6"
	} ),
	el('path', {
		d:"M146.6 150.7C149.2 150.7 151.8 149.7 154.6 147.6C157.9 145.3 160.8 143.4 160.8 143.4L161.8 142.7L160.8 142C160.4 141.7 116.9 111.5 112.4 108.6C110.3 107.1 107.9 106.8 106 107.7C104.2 108.6 103.2 110.5 103.2 112.8V123.1L103.6 123.3C103.9 123.5 133.8 144.3 139.6 148.2C142 149.9 144.3 150.7 146.6 150.7Z",
		fill: "#ffffff"
	}),
	el('path', {
		d:"M204.9 132.6C202.3 130.7 109 65.6 105 62.8L103.7 61.9V77.9C103.7 83 106.4 84.9 109.7 87.3C109.7 87.3 181.3 137.2 190.2 143.3C181.3 149.5 113.9 196.2 109.6 198.9C104.5 202.3 104 204.5 104 209.1V226.3C104 226.3 202.7 155.7 204.8 154.1C209.3 150.7 210.3 146.7 210.4 143.8V142C210.5 138.4 208.6 135.2 204.9 132.6Z",
		fill: "#ffffff"
	}),
);

/**
 * Internal dependencies
 */
import Edit from './edit';
import save from './save';

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/#registering-a-block
 */
registerBlockType( 'activecampaign-form/activecampaign-form-block', {
	/**
	 * This is the display title for your block, which can be translated with `i18n` functions.
	 * The block inserter will show this name.
	 */
	title: __( 'ActiveCampaign Forms', 'activecampaign-form' ),

	/**
	 * This is a short description for your block, can be translated with `i18n` functions.
	 * It will be shown in the Block Tab in the Settings Sidebar.
	 */
	description: __(
		'Embed ActiveCampaign forms into your page',
		'activecampaign-form'
	),

	/**
	 * Blocks are grouped into categories to help users browse and discover them.
	 * The categories provided by core are `common`, `embed`, `formatting`, `layout` and `widgets`.
	 */
	category: 'embed',

	/**
	 * An icon property should be specified to make it easier to identify a block.
	 * These can be any of WordPress’ Dashicons, or a custom svg element.
	 */
	icon: ac_logo,

	/**
	 * Optional block extended support features.
	 */
	supports: {
		// Removes support for an HTML mode.
		html: false,
	},

	/**
	 * @see ./edit.js
	 */
	edit: Edit,

	/**
	 * @see ./save.js
	 */
	save,
} );
