const defaultConfig = require( './node_modules/@wordpress/scripts/config/webpack.config.js' );


module.exports = {
	...defaultConfig,
	output: {
		jsonpFunction: 'wpJsonpActiveCampaign',
		path: defaultConfig.output.path,
	}
};
