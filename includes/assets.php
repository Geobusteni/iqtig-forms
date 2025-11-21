<?php
/**
 * Asset enqueueing functions.
 *
 * @package IQTIG_Forms
 */

namespace IQTIG_Forms;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Enqueue block assets.
add_action( 'enqueue_block_assets', __NAMESPACE__ . '\enqueue_block_assets' );

/**
 * Enqueue frontend scripts and styles for blocks.
 *
 * Loads validation scripts and cookie handling for the frontend.
 *
 * @return void
 */
function enqueue_block_assets() {
	// Only enqueue on frontend (not in editor).
	if ( is_admin() ) {
		return;
	}

	// Enqueue validation script for forms.
	wp_enqueue_script(
		'iqtig-forms-validation',
		IQTIG_FORMS_URL . 'assets/js/validation.js',
		array(),
		IQTIG_FORMS_VERSION,
		true
	);

	// Enqueue cookie handling script.
	wp_enqueue_script(
		'iqtig-forms-cookies',
		IQTIG_FORMS_URL . 'assets/js/cookies.js',
		array(),
		IQTIG_FORMS_VERSION,
		true
	);

	// Pass nonce and AJAX URL to scripts.
	wp_localize_script(
		'iqtig-forms-validation',
		'iqtigFormsData',
		array(
			'nonce' => wp_create_nonce( 'iqtig_forms_nonce' ),
			'ajaxUrl' => rest_url( 'iqtig-forms/v1' ),
			'strings' => array(
				'requiredField' => esc_html__( 'This field is required.', 'iqtig-forms' ),
				'invalidEmail' => esc_html__( 'Please enter a valid email address.', 'iqtig-forms' ),
				'genericError' => esc_html__( 'An error occurred. Please try again.', 'iqtig-forms' ),
			),
		)
	);

	// Enqueue frontend styles.
	wp_enqueue_style(
		'iqtig-forms-frontend',
		IQTIG_FORMS_URL . 'assets/css/frontend.css',
		array(),
		IQTIG_FORMS_VERSION
	);
}
