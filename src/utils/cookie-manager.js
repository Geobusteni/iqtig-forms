/**
 * Cookie management utilities for IQTIG Forms
 *
 * @package IQTIGForms
 */

/**
 * Set a cookie with name, value, and expiration
 *
 * @param {string} name - Cookie name
 * @param {string} value - Cookie value
 * @param {number} days - Number of days until expiration (default 30)
 */
export function setCookie( name, value, days = 30 ) {
	const date = new Date();
	date.setTime( date.getTime() + days * 24 * 60 * 60 * 1000 );
	const expires = 'expires=' + date.toUTCString();
	document.cookie = `${ name }=${ encodeURIComponent( value ) };${ expires };path=/;SameSite=Lax`;
}

/**
 * Get a cookie value by name
 *
 * @param {string} name - Cookie name
 * @return {string|null} Cookie value or null if not found
 */
export function getCookie( name ) {
	const nameEQ = name + '=';
	const cookies = document.cookie.split( ';' );

	for ( let i = 0; i < cookies.length; i++ ) {
		let cookie = cookies[ i ];
		while ( cookie.charAt( 0 ) === ' ' ) {
			cookie = cookie.substring( 1 );
		}
		if ( cookie.indexOf( nameEQ ) === 0 ) {
			return decodeURIComponent( cookie.substring( nameEQ.length ) );
		}
	}
	return null;
}

/**
 * Set form field data in a cookie
 *
 * @param {string} formId - Form identifier
 * @param {string} fieldName - Field name
 * @param {*} value - Field value (will be JSON encoded)
 */
export function setFormData( formId, fieldName, value ) {
	const cookieName = `iqtig_form_${ formId }_${ fieldName }`;
	const cookieValue = JSON.stringify( value );
	setCookie( cookieName, cookieValue, 30 );
}

/**
 * Get all form data for a specific form
 *
 * @param {string} formId - Form identifier
 * @return {Object} Object with field names as keys and field values
 */
export function getFormData( formId ) {
	const formData = {};
	const prefix = `iqtig_form_${ formId }_`;
	const cookies = document.cookie.split( ';' );

	cookies.forEach( ( cookie ) => {
		const trimmedCookie = cookie.trim();
		if ( trimmedCookie.startsWith( prefix ) ) {
			const equalIndex = trimmedCookie.indexOf( '=' );
			if ( equalIndex > -1 ) {
				const cookieName = trimmedCookie.substring( 0, equalIndex );
				const fieldName = cookieName.substring( prefix.length );
				const cookieValue = trimmedCookie.substring( equalIndex + 1 );
				try {
					formData[ fieldName ] = JSON.parse( decodeURIComponent( cookieValue ) );
				} catch ( e ) {
					formData[ fieldName ] = decodeURIComponent( cookieValue );
				}
			}
		}
	} );

	return formData;
}
