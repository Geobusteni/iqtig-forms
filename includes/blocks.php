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
			'render_callback' => __NAMESPACE__ . '\render_login_form_block',
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
			'render_callback' => __NAMESPACE__ . '\render_unsubscribe_form_block',
		)
	);
}

/**
 * Render callback for login form block.
 *
 * Adds global settings as data attributes to the login form.
 *
 * @param array  $attributes Block attributes.
 * @param string $content    Block content.
 * @return string Modified block content.
 */
function render_login_form_block( $attributes, $content ) {
	// Get global redirect URL setting.
	$global_redirect = get_setting( 'login_redirect_url', '' );

	// If no global redirect is set, return content as is.
	if ( empty( $global_redirect ) ) {
		return $content;
	}

	// Add data attribute to form element.
	$content = str_replace(
		'<form',
		sprintf(
			'<form data-global-redirect-url="%s"',
			esc_attr( $global_redirect )
		),
		$content
	);

	return $content;
}

/**
 * Render callback for unsubscribe form block.
 *
 * Adds global settings and URL parameters as data attributes to the unsubscribe form.
 *
 * @param array  $attributes Block attributes.
 * @param string $content    Block content.
 * @return string Modified block content.
 */
function render_unsubscribe_form_block( $attributes, $content ) {
	// Get global settings.
	$global_redirect = get_setting( 'unsubscribe_redirect_url', '' );
	$default_survey_id = get_setting( 'default_survey_id', '' );
	$use_default = get_setting( 'use_default_survey_id', false );

	// Check URL for surveyId parameter.
	$url_survey_id = isset( $_GET['surveyId'] ) ? sanitize_text_field( wp_unslash( $_GET['surveyId'] ) ) : '';

	// Build data attributes array.
	$data_attrs = array();

	if ( ! empty( $global_redirect ) ) {
		$data_attrs[] = sprintf(
			'data-global-redirect-url="%s"',
			esc_attr( $global_redirect )
		);
	}

	if ( ! empty( $default_survey_id ) ) {
		$data_attrs[] = sprintf(
			'data-default-survey-id="%s"',
			esc_attr( $default_survey_id )
		);
	}

	if ( $use_default ) {
		$data_attrs[] = 'data-use-default-survey-id="true"';
	}

	if ( ! empty( $url_survey_id ) ) {
		$data_attrs[] = sprintf(
			'data-url-survey-id="%s"',
			esc_attr( $url_survey_id )
		);
	}

	// Add data attributes to form element if any exist.
	if ( ! empty( $data_attrs ) ) {
		$attrs_string = implode( ' ', $data_attrs );
		$content = str_replace(
			'<form',
			'<form ' . $attrs_string,
			$content
		);
	}

	return $content;
}
