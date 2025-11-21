/**
 * Validation utilities for IQTIG Forms
 *
 * @package IQTIGForms
 */

/**
 * Validate if a required field has a value
 *
 * @param {string|boolean|Array} value - The field value to validate
 * @return {Object} Validation result with isValid and message properties
 */
export function validateRequired( value ) {
	if ( Array.isArray( value ) ) {
		return {
			isValid: value.length > 0,
			message: value.length > 0 ? '' : 'This field is required.',
		};
	}

	if ( typeof value === 'boolean' ) {
		return {
			isValid: value === true,
			message: value === true ? '' : 'This field must be checked.',
		};
	}

	const trimmedValue = typeof value === 'string' ? value.trim() : '';
	return {
		isValid: trimmedValue.length > 0,
		message: trimmedValue.length > 0 ? '' : 'This field is required.',
	};
}

/**
 * Validate all fields in a form
 *
 * @param {Array} fields - Array of field objects with name, value, and required properties
 * @return {Object} Object with isValid boolean and errors object keyed by field name
 */
export function validateAllFields( fields ) {
	const errors = {};
	let isValid = true;

	fields.forEach( ( field ) => {
		if ( field.required ) {
			const validation = validateRequired( field.value );
			if ( ! validation.isValid ) {
				errors[ field.name ] = validation.message;
				isValid = false;
			}
		}
	} );

	return {
		isValid,
		errors,
	};
}
