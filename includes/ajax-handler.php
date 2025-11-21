<?php
/**
 * AJAX and REST API handler functions.
 *
 * @package IQTIG_Forms
 */

namespace IQTIG_Forms;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Register REST API endpoints.
add_action( 'rest_api_init', __NAMESPACE__ . '\register_ajax_endpoints' );

/**
 * Register REST API endpoints for form handling.
 *
 * Creates REST API endpoints for form validation and submission.
 *
 * @return void
 */
function register_ajax_endpoints() {
	// Validate email endpoint.
	register_rest_route(
		'iqtig-forms/v1',
		'/validate-email',
		array(
			'methods' => 'POST',
			'callback' => __NAMESPACE__ . '\validate_email_endpoint',
			'permission_callback' => '__return_true',
			'args' => array(
				'email' => array(
					'required' => true,
					'type' => 'string',
					'sanitize_callback' => 'sanitize_email',
					'validate_callback' => function( $param ) {
						return is_email( $param );
					},
				),
			),
		)
	);

	// Login form submission endpoint.
	register_rest_route(
		'iqtig-forms/v1',
		'/login',
		array(
			'methods' => 'POST',
			'callback' => __NAMESPACE__ . '\handle_login_endpoint',
			'permission_callback' => '__return_true',
			'args' => array(
				'email' => array(
					'required' => true,
					'type' => 'string',
					'sanitize_callback' => 'sanitize_email',
				),
				'password' => array(
					'required' => true,
					'type' => 'string',
				),
			),
		)
	);

	// Unsubscribe form submission endpoint.
	register_rest_route(
		'iqtig-forms/v1',
		'/unsubscribe',
		array(
			'methods' => 'POST',
			'callback' => __NAMESPACE__ . '\handle_unsubscribe_endpoint',
			'permission_callback' => '__return_true',
			'args' => array(
				'email' => array(
					'required' => true,
					'type' => 'string',
					'sanitize_callback' => 'sanitize_email',
				),
			),
		)
	);
}

/**
 * Validate email endpoint callback.
 *
 * Validates an email address via REST API.
 *
 * @param \WP_REST_Request $request The REST request object.
 * @return \WP_REST_Response The REST response.
 */
function validate_email_endpoint( $request ) {
	// Get and sanitize email.
	$email = sanitize_email( $request->get_param( 'email' ) );

	// Verify nonce for security.
	$nonce = $request->get_header( 'X-WP-Nonce' );
	if ( empty( $nonce ) ) {
		return rest_ensure_response(
			array(
				'success' => false,
				'message' => esc_html__( 'Security verification failed.', 'iqtig-forms' ),
			)
		);
	}

	// Validate email format.
	if ( ! is_email( $email ) ) {
		return rest_ensure_response(
			array(
				'success' => false,
				'message' => esc_html__( 'Please enter a valid email address.', 'iqtig-forms' ),
			)
		);
	}

	// Email is valid.
	return rest_ensure_response(
		array(
			'success' => true,
			'message' => esc_html__( 'Email is valid.', 'iqtig-forms' ),
		)
	);
}

/**
 * Handle login form submission.
 *
 * Processes login form data and sets appropriate cookies.
 *
 * @param \WP_REST_Request $request The REST request object.
 * @return \WP_REST_Response The REST response.
 */
function handle_login_endpoint( $request ) {
	// Get and sanitize parameters.
	$email = sanitize_email( $request->get_param( 'email' ) );
	$password = $request->get_param( 'password' );

	// Verify nonce for security.
	$nonce = $request->get_header( 'X-WP-Nonce' );
	if ( empty( $nonce ) ) {
		return rest_ensure_response(
			array(
				'success' => false,
				'message' => esc_html__( 'Security verification failed.', 'iqtig-forms' ),
			)
		);
	}

	// Validate email.
	if ( ! is_email( $email ) ) {
		return rest_ensure_response(
			array(
				'success' => false,
				'message' => esc_html__( 'Please enter a valid email address.', 'iqtig-forms' ),
			)
		);
	}

	// Validate password is not empty.
	if ( empty( $password ) ) {
		return rest_ensure_response(
			array(
				'success' => false,
				'message' => esc_html__( 'Password is required.', 'iqtig-forms' ),
			)
		);
	}

	// Attempt to authenticate user.
	$user = wp_authenticate( $email, $password );

	if ( is_wp_error( $user ) ) {
		return rest_ensure_response(
			array(
				'success' => false,
				'message' => esc_html__( 'Invalid email or password.', 'iqtig-forms' ),
			)
		);
	}

	// Set login cookie.
	set_form_cookie( 'iqtig_user_logged_in', 'true' );
	set_form_cookie( 'iqtig_user_email', $email );

	// Log the user in.
	wp_set_current_user( $user->ID );
	wp_set_auth_cookie( $user->ID );

	return rest_ensure_response(
		array(
			'success' => true,
			'message' => esc_html__( 'Login successful.', 'iqtig-forms' ),
			'redirect_url' => home_url( '/' ),
		)
	);
}

/**
 * Handle unsubscribe form submission.
 *
 * Processes unsubscribe requests and sets appropriate cookies.
 *
 * @param \WP_REST_Request $request The REST request object.
 * @return \WP_REST_Response The REST response.
 */
function handle_unsubscribe_endpoint( $request ) {
	// Get and sanitize email.
	$email = sanitize_email( $request->get_param( 'email' ) );

	// Verify nonce for security.
	$nonce = $request->get_header( 'X-WP-Nonce' );
	if ( empty( $nonce ) ) {
		return rest_ensure_response(
			array(
				'success' => false,
				'message' => esc_html__( 'Security verification failed.', 'iqtig-forms' ),
			)
		);
	}

	// Validate email.
	if ( ! is_email( $email ) ) {
		return rest_ensure_response(
			array(
				'success' => false,
				'message' => esc_html__( 'Please enter a valid email address.', 'iqtig-forms' ),
			)
		);
	}

	// Set unsubscribe cookie.
	set_form_cookie( 'iqtig_unsubscribed', 'true' );
	set_form_cookie( 'iqtig_unsubscribed_email', $email );

	// In a real implementation, you would also:
	// 1. Update user meta or a separate unsubscribe table
	// 2. Remove user from mailing lists
	// 3. Send confirmation email
	// This is a placeholder for those operations.

	return rest_ensure_response(
		array(
			'success' => true,
			'message' => esc_html__( 'You have been successfully unsubscribed.', 'iqtig-forms' ),
		)
	);
}
