/**
 * Accessibility utilities for IQTIG Forms
 *
 * @package IQTIGForms
 */

/**
 * Get or create the ARIA live region for announcements
 *
 * @return {HTMLElement} The live region element
 */
function getLiveRegion() {
	let liveRegion = document.getElementById( 'iqtig-forms-live-region' );

	if ( ! liveRegion ) {
		liveRegion = document.createElement( 'div' );
		liveRegion.id = 'iqtig-forms-live-region';
		liveRegion.setAttribute( 'aria-live', 'polite' );
		liveRegion.setAttribute( 'aria-atomic', 'true' );
		liveRegion.style.position = 'absolute';
		liveRegion.style.left = '-10000px';
		liveRegion.style.width = '1px';
		liveRegion.style.height = '1px';
		liveRegion.style.overflow = 'hidden';
		document.body.appendChild( liveRegion );
	}

	return liveRegion;
}

/**
 * Announce an error message to screen readers
 *
 * @param {string} message - The message to announce
 */
export function announceError( message ) {
	const liveRegion = getLiveRegion();
	liveRegion.textContent = '';

	setTimeout( () => {
		liveRegion.textContent = message;
	}, 100 );
}

/**
 * Focus the first field with an error
 *
 * @param {HTMLElement} formElement - The form element containing fields
 */
export function focusFirstError( formElement ) {
	if ( ! formElement ) {
		return;
	}

	const firstErrorField = formElement.querySelector( '[aria-invalid="true"]' );
	if ( firstErrorField ) {
		firstErrorField.focus();

		if ( firstErrorField.scrollIntoView ) {
			firstErrorField.scrollIntoView( {
				behavior: 'smooth',
				block: 'center',
			} );
		}
	}
}

/**
 * Setup keyboard navigation for a form
 *
 * @param {HTMLElement} formElement - The form element
 * @param {Function} submitCallback - Callback to execute on submit
 */
export function setupKeyboardNavigation( formElement, submitCallback ) {
	if ( ! formElement ) {
		return;
	}

	formElement.addEventListener( 'keydown', ( event ) => {
		if ( event.key === 'Enter' && event.target.tagName !== 'TEXTAREA' ) {
			event.preventDefault();
			if ( typeof submitCallback === 'function' ) {
				submitCallback( event );
			}
		}
	} );
}
