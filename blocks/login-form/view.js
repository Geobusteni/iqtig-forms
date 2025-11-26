/**
 * Login Form Block Frontend
 *
 * @package IQTIGForms
 */

import { __ } from '@wordpress/i18n';
import { setFormData, getFormData } from '../../src/utils/cookie-manager';
import { validateAllFields } from '../../src/utils/validation';
import {
	announceError,
	focusFirstError,
	setupKeyboardNavigation,
} from '../../src/utils/accessibility';

document.addEventListener( 'DOMContentLoaded', () => {
	const forms = document.querySelectorAll( '.wp-block-iqtig-forms-login-form' );

	forms.forEach( ( formContainer ) => {
		const formElement = formContainer.querySelector( 'form' );
		if ( ! formElement ) {
			return;
		}

		const formId = formElement.dataset.formId;
		const redirectUrl = formElement.dataset.redirectUrl;

		if ( ! formId ) {
			return;
		}

		// Restore field values from cookies
		const savedData = getFormData( formId );
		Object.keys( savedData ).forEach( ( fieldName ) => {
			const field = formElement.querySelector( `[name="${ fieldName }"]` );
			if ( field ) {
				if ( field.type === 'checkbox' ) {
					field.checked = savedData[ fieldName ] === true;
				} else if ( field.type === 'radio' ) {
					const radioButton = formElement.querySelector(
						`[name="${ fieldName }"][value="${ savedData[ fieldName ] }"]`
					);
					if ( radioButton ) {
						radioButton.checked = true;
					}
				} else {
					field.value = savedData[ fieldName ];
				}
			}
		} );

		// Clear error states
		const clearErrors = () => {
			const errorMessages = formElement.querySelectorAll( '.iqtig-forms-error' );
			errorMessages.forEach( ( error ) => {
				error.remove();
			} );

			const invalidFields = formElement.querySelectorAll( '[aria-invalid="true"]' );
			invalidFields.forEach( ( field ) => {
				field.setAttribute( 'aria-invalid', 'false' );
				field.classList.remove( 'has-error' );
			} );
		};

		// Display errors
		const displayErrors = ( errors ) => {
			clearErrors();

			Object.keys( errors ).forEach( ( fieldName ) => {
				const field = formElement.querySelector( `[name="${ fieldName }"]` );
				if ( field ) {
					field.setAttribute( 'aria-invalid', 'true' );
					field.classList.add( 'has-error' );

					const errorElement = document.createElement( 'div' );
					errorElement.className = 'iqtig-forms-error';
					errorElement.setAttribute( 'role', 'alert' );
					errorElement.textContent = errors[ fieldName ];

					const fieldWrapper =
						field.closest( '.iqtig-forms-field' ) || field.parentElement;
					fieldWrapper.appendChild( errorElement );

					const errorId = `${ field.id || fieldName }-error`;
					errorElement.id = errorId;
					field.setAttribute( 'aria-describedby', errorId );
				}
			} );

			const errorCount = Object.keys( errors ).length;
			const errorMessage =
				errorCount === 1
					? 'There is 1 error in the form. Please correct it before submitting.'
					: `There are ${ errorCount } errors in the form. Please correct them before submitting.`;

			announceError( errorMessage );
			focusFirstError( formElement );
		};

		// Save field value on change
		const saveFieldValue = ( fieldName, value ) => {
			setFormData( formId, fieldName, value );
		};

		// Attach change listeners to all fields
		const fields = formElement.querySelectorAll( 'input, textarea, select' );
		fields.forEach( ( field ) => {
			const fieldName = field.name;

			if ( field.type === 'checkbox' ) {
				field.addEventListener( 'change', () => {
					saveFieldValue( fieldName, field.checked );
				} );
			} else if ( field.type === 'radio' ) {
				field.addEventListener( 'change', () => {
					if ( field.checked ) {
						saveFieldValue( fieldName, field.value );
					}
				} );
			} else {
				field.addEventListener( 'input', () => {
					saveFieldValue( fieldName, field.value );
				} );
			}
		} );

		// Handle form submission
		const handleSubmit = ( event ) => {
			event.preventDefault();

			// Collect all field data
			const formFields = [];
			const formData = {};

			fields.forEach( ( field ) => {
				let fieldValue;
				if ( field.type === 'checkbox' ) {
					fieldValue = field.checked;
				} else if ( field.type === 'radio' ) {
					if ( field.checked ) {
						fieldValue = field.value;
					} else {
						return; // Skip unchecked radio buttons
					}
				} else {
					fieldValue = field.value;
				}

				const isRequired = field.hasAttribute( 'required' );

				// For radio buttons, only add once per group
				if ( field.type === 'radio' ) {
					const existingField = formFields.find( ( f ) => f.name === field.name );
					if ( ! existingField ) {
						const radioGroup = formElement.querySelectorAll(
							`[name="${ field.name }"]`
						);
						const checkedRadio = Array.from( radioGroup ).find( ( r ) => r.checked );
						const radioValue = checkedRadio ? checkedRadio.value : '';
						formFields.push( {
							name: field.name,
							value: radioValue,
							required: isRequired,
						} );
						formData[ field.name ] = radioValue;
					}
				} else {
					formFields.push( {
						name: field.name,
						value: fieldValue,
						required: isRequired,
					} );
					formData[ field.name ] = fieldValue;
				}
			} );

			// Validate fields
			const validation = validateAllFields( formFields );

			if ( ! validation.isValid ) {
				displayErrors( validation.errors );
				return;
			}

			// Clear errors if validation passes
			clearErrors();

			// Add loading state
			const submitButton = formElement.querySelector( '.iqtig-forms-submit' );
			const originalButtonText = submitButton.textContent;
			submitButton.disabled = true;
			submitButton.setAttribute( 'aria-busy', 'true' );
			submitButton.textContent = __( 'Submitting...', 'iqtig-forms' );

			// Announce loading state for screen readers
			announceError( __( 'Submitting form, please wait...', 'iqtig-forms' ) );

			// Prepare API request data
			const requestData = {
				...formData,
				useGlobalRedirect: formElement.dataset.useGlobalRedirect === 'true',
				redirectUrl: formElement.dataset.redirectUrl || '',
			};

			// Submit to API
			fetch( formElement.dataset.apiUrl + 'iqtig-forms/v1/login', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': formElement.dataset.nonce,
				},
				body: JSON.stringify( requestData ),
			} )
				.then( ( response ) => response.json() )
				.then( ( data ) => {
					if ( data.success && data.redirect_url ) {
						// Announce success for screen readers
						announceError( __( 'Login successful, redirecting...', 'iqtig-forms' ) );
						// Redirect to the URL provided by the API
						window.location.href = data.redirect_url;
					} else {
						// Handle error response
						const errorMessage = data.message || __( 'An error occurred during login. Please try again.', 'iqtig-forms' );
						displayErrors( { general: errorMessage } );

						// Reset button state
						submitButton.disabled = false;
						submitButton.setAttribute( 'aria-busy', 'false' );
						submitButton.textContent = originalButtonText;
					}
				} )
				.catch( ( error ) => {
					// Handle network or other errors
					const errorMessage = __( 'Network error. Please check your connection and try again.', 'iqtig-forms' );
					displayErrors( { general: errorMessage } );

					// Reset button state
					submitButton.disabled = false;
					submitButton.setAttribute( 'aria-busy', 'false' );
					submitButton.textContent = originalButtonText;
				} );
		};

		// Submit button click handler
		const submitButton = formElement.querySelector( '.iqtig-forms-submit' );
		if ( submitButton ) {
			submitButton.addEventListener( 'click', handleSubmit );
		}

		// Setup keyboard navigation
		setupKeyboardNavigation( formElement, handleSubmit );
	} );
} );
