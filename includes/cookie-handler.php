<?php
/**
 * Cookie handling functions.
 *
 * @package IQTIG_Forms
 */

namespace IQTIG_Forms;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Set a form-related cookie with secure parameters.
 *
 * Sets a cookie with proper security settings including SameSite attribute.
 * Cookie is set for 30 days, accessible to JavaScript, and applies to entire site.
 *
 * @param string $name The cookie name (will be sanitized).
 * @param string $value The cookie value (will be sanitized).
 * @return bool True if cookie was set successfully, false otherwise.
 */
function set_form_cookie( $name, $value ) {
	// Sanitize inputs.
	$name = sanitize_text_field( $name );
	$value = sanitize_text_field( $value );

	// Validate cookie name.
	if ( empty( $name ) ) {
		return false;
	}

	// Set expiration to 30 days from now.
	$expiration = time() + ( 30 * DAY_IN_SECONDS );

	// Determine if site is using HTTPS.
	$secure = is_ssl();

	// Cookie parameters.
	$path = '/';
	$domain = '';
	$httponly = false; // JavaScript needs access to read the cookie.
	$samesite = 'Lax';

	// For PHP 7.3+, use options array for setcookie.
	if ( PHP_VERSION_ID >= 70300 ) {
		$result = setcookie(
			$name,
			$value,
			array(
				'expires' => $expiration,
				'path' => $path,
				'domain' => $domain,
				'secure' => $secure,
				'httponly' => $httponly,
				'samesite' => $samesite,
			)
		);
	} else {
		// Fallback for older PHP versions.
		// Append SameSite to the cookie header manually.
		$result = setcookie(
			$name,
			$value,
			$expiration,
			$path . '; SameSite=' . $samesite,
			$domain,
			$secure,
			$httponly
		);
	}

	return $result;
}

/**
 * Delete a form-related cookie.
 *
 * Removes a cookie by setting its expiration to the past.
 *
 * @param string $name The cookie name to delete.
 * @return bool True if cookie deletion was initiated, false otherwise.
 */
function delete_form_cookie( $name ) {
	// Sanitize input.
	$name = sanitize_text_field( $name );

	// Validate cookie name.
	if ( empty( $name ) ) {
		return false;
	}

	// Set expiration to the past.
	$expiration = time() - YEAR_IN_SECONDS;

	// Determine if site is using HTTPS.
	$secure = is_ssl();

	// Cookie parameters.
	$path = '/';
	$domain = '';
	$httponly = false;
	$samesite = 'Lax';

	// For PHP 7.3+, use options array.
	if ( PHP_VERSION_ID >= 70300 ) {
		$result = setcookie(
			$name,
			'',
			array(
				'expires' => $expiration,
				'path' => $path,
				'domain' => $domain,
				'secure' => $secure,
				'httponly' => $httponly,
				'samesite' => $samesite,
			)
		);
	} else {
		// Fallback for older PHP versions.
		$result = setcookie(
			$name,
			'',
			$expiration,
			$path . '; SameSite=' . $samesite,
			$domain,
			$secure,
			$httponly
		);
	}

	return $result;
}

/**
 * Get a form-related cookie value.
 *
 * Safely retrieves and sanitizes a cookie value.
 *
 * @param string $name The cookie name to retrieve.
 * @return string|null The cookie value if it exists, null otherwise.
 */
function get_form_cookie( $name ) {
	// Sanitize input.
	$name = sanitize_text_field( $name );

	// Check if cookie exists.
	if ( ! isset( $_COOKIE[ $name ] ) ) {
		return null;
	}

	// Return sanitized cookie value.
	return sanitize_text_field( wp_unslash( $_COOKIE[ $name ] ) );
}
