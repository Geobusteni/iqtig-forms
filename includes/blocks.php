<?php
/**
 * Block registration functions.
 *
 * @package IQTIG_Forms
 */

namespace IQTIG_Forms;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Register blocks on init.
add_action( 'init', __NAMESPACE__ . '\register_blocks' );

/**
 * Register custom Gutenberg blocks.
 *
 * Registers the login form and unsubscribe form blocks.
 *
 * @return void
 */
function register_blocks() {
	// Register login form block.
	register_block_type(
		IQTIG_FORMS_PATH . 'blocks/login-form',
		array(
			'api_version' => 2,
			'editor_script' => 'iqtig-forms-login-form-editor',
			'editor_style' => 'iqtig-forms-login-form-editor-style',
			'style' => 'iqtig-forms-login-form-style',
		)
	);

	// Register unsubscribe form block.
	register_block_type(
		IQTIG_FORMS_PATH . 'blocks/unsubscribe-form',
		array(
			'api_version' => 2,
			'editor_script' => 'iqtig-forms-unsubscribe-form-editor',
			'editor_style' => 'iqtig-forms-unsubscribe-form-editor-style',
			'style' => 'iqtig-forms-unsubscribe-form-style',
		)
	);
}
