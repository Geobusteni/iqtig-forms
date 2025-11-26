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
 * Proxy API request to IQTIG API.
 *
 * Sends a request to the IQTIG API and returns the response.
 *
 * @param string $endpoint The API endpoint path.
 * @param array  $body     The request body data.
 * @return array|\WP_Error The response data or WP_Error on failure.
 */
function proxy_api_request( $endpoint, $body ) {
	$api_base_url = get_setting( 'api_base_url', 'https://api.iqtig.org' );
	$url = trailingslashit( $api_base_url ) . ltrim( $endpoint, '/' );

	$response = wp_remote_post(
		$url,
		array(
			'headers' => array( 'Content-Type' => 'application/json' ),
			'body'    => wp_json_encode( $body ),
			'timeout' => 30,
		)
	);

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$response_code = wp_remote_retrieve_response_code( $response );
	$response_body = wp_remote_retrieve_body( $response );
	$decoded_body = json_decode( $response_body, true );

	return array(
		'code' => $response_code,
		'body' => $decoded_body ? $decoded_body : array(),
	);
}

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
				'username' => array(
					'required' => true,
					'type' => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
				'password' => array(
					'required' => true,
					'type' => 'string',
				),
				'redirect_url' => array(
					'required' => false,
					'type' => 'string',
					'sanitize_callback' => 'esc_url_raw',
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
				'surveyId' => array(
					'required' => true,
					'type' => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
				'reason' => array(
					'required' => false,
					'type' => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
				'redirect_url' => array(
					'required' => false,
					'type' => 'string',
					'sanitize_callback' => 'esc_url_raw',
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
 * Processes login form data via API proxy and sets JWT cookie.
 *
 * @param \WP_REST_Request $request The REST request object.
 * @return \WP_REST_Response The REST response.
 */
function handle_login_endpoint( $request ) {
	// Get and sanitize parameters.
	$username = sanitize_text_field( $request->get_param( 'username' ) );
	$password = $request->get_param( 'password' );
	$redirect_url = $request->get_param( 'redirect_url' );

	// Verify nonce for security.
	$nonce = $request->get_header( 'X-WP-Nonce' );
	if ( empty( $nonce ) ) {
		return new \WP_REST_Response(
			array(
				'success' => false,
				'message' => esc_html__( 'Security verification failed.', 'iqtig-forms' ),
			),
			403
		);
	}

	// Validate username is not empty.
	if ( empty( $username ) ) {
		return new \WP_REST_Response(
			array(
				'success' => false,
				'message' => esc_html__( 'Username is required.', 'iqtig-forms' ),
			),
			400
		);
	}

	// Validate password is not empty.
	if ( empty( $password ) ) {
		return new \WP_REST_Response(
			array(
				'success' => false,
				'message' => esc_html__( 'Password is required.', 'iqtig-forms' ),
			),
			400
		);
	}

	// Call API proxy.
	$api_response = proxy_api_request(
		'auth/login',
		array(
			'username' => $username,
			'password' => $password,
		)
	);

	// Handle API errors.
	if ( is_wp_error( $api_response ) ) {
		return new \WP_REST_Response(
			array(
				'success' => false,
				'message' => esc_html__( 'Unable to connect to authentication service.', 'iqtig-forms' ),
			),
			500
		);
	}

	// Check response code.
	if ( 200 !== $api_response['code'] ) {
		$error_message = isset( $api_response['body']['message'] )
			? $api_response['body']['message']
			: __( 'Authentication failed.', 'iqtig-forms' );

		return new \WP_REST_Response(
			array(
				'success' => false,
				'message' => esc_html( $error_message ),
			),
			$api_response['code']
		);
	}

	// Get token from response.
	$token = isset( $api_response['body']['token'] ) ? $api_response['body']['token'] : '';

	if ( empty( $token ) ) {
		return new \WP_REST_Response(
			array(
				'success' => false,
				'message' => esc_html__( 'Invalid response from authentication service.', 'iqtig-forms' ),
			),
			500
		);
	}

	// Set JWT cookie.
	$cookie_lifetime = get_setting( 'jwt_cookie_lifetime', 14400 );
	$expire = time() + absint( $cookie_lifetime );
	$secure = is_ssl();

	setcookie(
		'iqtig_jwt_token',
		$token,
		array(
			'expires'  => $expire,
			'path'     => '/',
			'domain'   => '',
			'secure'   => $secure,
			'httponly' => false,
			'samesite' => 'Lax',
		)
	);

	// Determine redirect URL.
	$final_redirect = home_url( '/' );

	if ( ! empty( $redirect_url ) ) {
		$final_redirect = esc_url_raw( $redirect_url );
	} else {
		$global_redirect = get_setting( 'login_redirect_url', '' );
		if ( ! empty( $global_redirect ) ) {
			$final_redirect = esc_url_raw( $global_redirect );
		}
	}

	return new \WP_REST_Response(
		array(
			'success' => true,
			'message' => esc_html__( 'Login successful.', 'iqtig-forms' ),
			'redirect_url' => $final_redirect,
		),
		200
	);
}

/**
 * Handle unsubscribe form submission.
 *
 * Processes unsubscribe requests via API proxy.
 *
 * @param \WP_REST_Request $request The REST request object.
 * @return \WP_REST_Response The REST response.
 */
function handle_unsubscribe_endpoint( $request ) {
	// Get and sanitize parameters.
	$survey_id = sanitize_text_field( $request->get_param( 'surveyId' ) );
	$reason = sanitize_text_field( $request->get_param( 'reason' ) );
	$redirect_url = $request->get_param( 'redirect_url' );

	// Verify nonce for security.
	$nonce = $request->get_header( 'X-WP-Nonce' );
	if ( empty( $nonce ) ) {
		return new \WP_REST_Response(
			array(
				'success' => false,
				'message' => esc_html__( 'Security verification failed.', 'iqtig-forms' ),
			),
			403
		);
	}

	// Validate survey ID.
	if ( empty( $survey_id ) ) {
		return new \WP_REST_Response(
			array(
				'success' => false,
				'message' => esc_html__( 'Survey ID is required.', 'iqtig-forms' ),
			),
			400
		);
	}

	// Call API proxy.
	$api_response = proxy_api_request(
		'survey/unsubscribe',
		array(
			'surveyId' => $survey_id,
			'reason'   => $reason,
		)
	);

	// Handle API errors.
	if ( is_wp_error( $api_response ) ) {
		return new \WP_REST_Response(
			array(
				'success' => false,
				'message' => esc_html__( 'Unable to connect to unsubscribe service.', 'iqtig-forms' ),
			),
			500
		);
	}

	// Check response code.
	if ( 200 !== $api_response['code'] ) {
		$error_message = isset( $api_response['body']['message'] )
			? $api_response['body']['message']
			: __( 'Unsubscribe failed.', 'iqtig-forms' );

		return new \WP_REST_Response(
			array(
				'success' => false,
				'message' => esc_html( $error_message ),
			),
			$api_response['code']
		);
	}

	// Determine redirect URL.
	$final_redirect = home_url( '/' );

	if ( ! empty( $redirect_url ) ) {
		$final_redirect = esc_url_raw( $redirect_url );
	} else {
		$global_redirect = get_setting( 'unsubscribe_redirect_url', '' );
		if ( ! empty( $global_redirect ) ) {
			$final_redirect = esc_url_raw( $global_redirect );
		}
	}

	return new \WP_REST_Response(
		array(
			'success' => true,
			'message' => esc_html__( 'You have been successfully unsubscribed.', 'iqtig-forms' ),
			'redirect_url' => $final_redirect,
		),
		200
	);
}
