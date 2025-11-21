/**
 * FormField Component
 * Reusable field component for IQTIG Forms
 *
 * @package IQTIGForms
 */

import { createElement } from '@wordpress/element';

/**
 * FormField component for rendering different input types
 *
 * @param {Object} props - Component props
 * @param {string} props.type - Field type (text, textarea, checkbox, radio, select)
 * @param {string} props.name - Field name
 * @param {string} props.label - Field label
 * @param {boolean} props.required - Whether field is required
 * @param {*} props.value - Field value
 * @param {Function} props.onChange - Change handler
 * @param {Array} props.options - Options for select/radio fields
 * @param {string} props.error - Error message
 * @param {string} props.id - Field ID (auto-generated if not provided)
 * @return {Object} React element
 */
export default function FormField( {
	type = 'text',
	name,
	label,
	required = false,
	value = '',
	onChange,
	options = [],
	error = '',
	id = null,
} ) {
	const fieldId = id || `iqtig-field-${ name }`;
	const errorId = `${ fieldId }-error`;
	const hasError = error && error.length > 0;

	const commonProps = {
		id: fieldId,
		name,
		required,
		'aria-required': required ? 'true' : 'false',
		'aria-invalid': hasError ? 'true' : 'false',
		'aria-describedby': hasError ? errorId : undefined,
	};

	const handleChange = ( event ) => {
		if ( typeof onChange === 'function' ) {
			if ( type === 'checkbox' ) {
				onChange( event.target.checked );
			} else {
				onChange( event.target.value );
			}
		}
	};

	const renderInput = () => {
		switch ( type ) {
			case 'textarea':
				return createElement( 'textarea', {
					...commonProps,
					value: value || '',
					onChange: handleChange,
					className: 'iqtig-forms-textarea',
					rows: 4,
				} );

			case 'checkbox':
				return createElement( 'input', {
					...commonProps,
					type: 'checkbox',
					checked: value === true || value === 'true',
					onChange: handleChange,
					className: 'iqtig-forms-checkbox',
				} );

			case 'radio':
				return createElement(
					'div',
					{ className: 'iqtig-forms-radio-group', role: 'radiogroup' },
					options.map( ( option, index ) => {
						const optionId = `${ fieldId }-option-${ index }`;
						return createElement(
							'div',
							{ key: option.value, className: 'iqtig-forms-radio-option' },
							createElement( 'input', {
								type: 'radio',
								id: optionId,
								name,
								value: option.value,
								checked: value === option.value,
								onChange: handleChange,
								required,
								'aria-required': required ? 'true' : 'false',
								className: 'iqtig-forms-radio',
							} ),
							createElement(
								'label',
								{ htmlFor: optionId, className: 'iqtig-forms-radio-label' },
								option.label
							)
						);
					} )
				);

			case 'select':
				return createElement(
					'select',
					{
						...commonProps,
						value: value || '',
						onChange: handleChange,
						className: 'iqtig-forms-select',
					},
					[
						createElement(
							'option',
							{ key: 'empty', value: '' },
							'-- Select an option --'
						),
						...options.map( ( option ) =>
							createElement(
								'option',
								{ key: option.value, value: option.value },
								option.label
							)
						),
					]
				);

			case 'text':
			default:
				return createElement( 'input', {
					...commonProps,
					type: 'text',
					value: value || '',
					onChange: handleChange,
					className: 'iqtig-forms-text',
				} );
		}
	};

	return createElement(
		'div',
		{ className: `iqtig-forms-field iqtig-forms-field-${ type }${ hasError ? ' has-error' : '' }` },
		type !== 'radio' &&
			createElement(
				'label',
				{
					htmlFor: fieldId,
					className: 'iqtig-forms-label',
				},
				label,
				required &&
					createElement(
						'span',
						{
							className: 'iqtig-forms-required',
							'aria-label': 'required',
						},
						' *'
					)
			),
		type === 'radio' &&
			createElement(
				'div',
				{ className: 'iqtig-forms-label' },
				label,
				required &&
					createElement(
						'span',
						{
							className: 'iqtig-forms-required',
							'aria-label': 'required',
						},
						' *'
					)
			),
		renderInput(),
		hasError &&
			createElement(
				'div',
				{
					id: errorId,
					className: 'iqtig-forms-error',
					role: 'alert',
				},
				error
			)
	);
}
