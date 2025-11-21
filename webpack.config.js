const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );

module.exports = {
	...defaultConfig,
	entry: {
		'login-form/index': path.resolve( __dirname, 'blocks/login-form/index.js' ),
		'login-form/view': path.resolve( __dirname, 'blocks/login-form/view.js' ),
		'unsubscribe-form/index': path.resolve( __dirname, 'blocks/unsubscribe-form/index.js' ),
		'unsubscribe-form/view': path.resolve( __dirname, 'blocks/unsubscribe-form/view.js' ),
	},
};
