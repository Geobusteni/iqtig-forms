<?php
/**
 * Plugin activation functions.
 *
 * @package IQTIG_Forms
 */

namespace IQTIG_Forms;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin activation callback.
 *
 * Runs when the plugin is activated. Checks WordPress version compatibility
 * and sets up default options if needed.
 *
 * @return void
 */
function activate_plugin() {
	// Check WordPress version compatibility.
	global $wp_version;
	$required_wp_version = '6.0';

	if ( version_compare( $wp_version, $required_wp_version, '<' ) ) {
		wp_die(
			sprintf(
				/* translators: 1: Required WordPress version, 2: Current WordPress version */
				esc_html__( 'IQTIG Forms requires WordPress version %1$s or higher. You are running version %2$s. Please upgrade WordPress and try again.', 'iqtig-forms' ),
				esc_html( $required_wp_version ),
				esc_html( $wp_version )
			),
			esc_html__( 'Plugin Activation Error', 'iqtig-forms' ),
			array( 'back_link' => true )
		);
	}

	// Set default plugin options if they don't exist.
	$default_options = array(
		'version' => IQTIG_FORMS_VERSION,
		'activated_time' => time(),
	);

	if ( ! get_option( 'iqtig_forms_options' ) ) {
		update_option( 'iqtig_forms_options', $default_options );
	}

	// Initialize default settings if they don't exist.
	$default_settings = array(
		'api_base_url'              => 'https://api.iqtig.org',
		'jwt_cookie_lifetime'       => 14400,
		'login_redirect_url'        => '',
		'unsubscribe_redirect_url'  => '',
		'default_survey_id'         => '',
		'use_default_survey_id'     => false,
	);

	if ( ! get_option( 'iqtig_forms_settings' ) ) {
		add_option( 'iqtig_forms_settings', $default_settings );
	}

	// Flush rewrite rules to ensure REST API endpoints are available.
	flush_rewrite_rules();
}
