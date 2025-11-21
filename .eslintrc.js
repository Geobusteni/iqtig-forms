module.exports = {
	extends: [ 'plugin:@wordpress/eslint-plugin/recommended' ],
	env: {
		browser: true,
		es2021: true,
	},
	parserOptions: {
		ecmaVersion: 2021,
		sourceType: 'module',
		ecmaFeatures: {
			jsx: true,
		},
	},
	rules: {
		// Console warnings (no console.log in production)
		'no-console': [ 'warn', { allow: [ 'warn', 'error' ] } ],
		'jsdoc/require-jsdoc': 'off',

		// WordPress coding standards
		'@wordpress/no-unused-vars-before-return': 'error',
		'@wordpress/dependency-group': 'error',

		// Code quality
		'no-unused-vars': [
			'error',
			{
				argsIgnorePattern: '^_',
				varsIgnorePattern: '^_',
			},
		],
		'prefer-const': 'error',
		'no-var': 'error',

		// Formatting
		indent: [ 'error', 'tab' ],
		quotes: [ 'error', 'single' ],
		semi: [ 'error', 'always' ],
	},
	settings: {
		react: {
			version: 'detect',
		},
	},
};
