/**
 * Unsubscribe Form Block Editor
 *
 * @package IQTIGForms
 */

import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	Button,
	SelectControl,
	ToggleControl,
	IconButton,
} from '@wordpress/components';
import { createElement, Fragment } from '@wordpress/element';

export default function Edit( { attributes, setAttributes } ) {
	const { domain, formId, redirectUrl, fields } = attributes;
	const blockProps = useBlockProps();

	const addField = () => {
		const newField = {
			id: `field-${ Date.now() }`,
			type: 'text',
			name: '',
			label: '',
			required: false,
			options: [],
		};
		setAttributes( { fields: [ ...fields, newField ] } );
	};

	const updateField = ( index, key, value ) => {
		const updatedFields = [ ...fields ];
		updatedFields[ index ] = { ...updatedFields[ index ], [ key ]: value };
		setAttributes( { fields: updatedFields } );
	};

	const deleteField = ( index ) => {
		const updatedFields = fields.filter( ( _, i ) => i !== index );
		setAttributes( { fields: updatedFields } );
	};

	const moveField = ( index, direction ) => {
		const updatedFields = [ ...fields ];
		const newIndex = direction === 'up' ? index - 1 : index + 1;
		if ( newIndex >= 0 && newIndex < updatedFields.length ) {
			const temp = updatedFields[ index ];
			updatedFields[ index ] = updatedFields[ newIndex ];
			updatedFields[ newIndex ] = temp;
			setAttributes( { fields: updatedFields } );
		}
	};

	const addFieldOption = ( fieldIndex ) => {
		const updatedFields = [ ...fields ];
		const currentOptions = updatedFields[ fieldIndex ].options || [];
		updatedFields[ fieldIndex ].options = [
			...currentOptions,
			{ label: '', value: '' },
		];
		setAttributes( { fields: updatedFields } );
	};

	const updateFieldOption = ( fieldIndex, optionIndex, key, value ) => {
		const updatedFields = [ ...fields ];
		const updatedOptions = [ ...( updatedFields[ fieldIndex ].options || [] ) ];
		updatedOptions[ optionIndex ] = {
			...updatedOptions[ optionIndex ],
			[ key ]: value,
		};
		updatedFields[ fieldIndex ].options = updatedOptions;
		setAttributes( { fields: updatedFields } );
	};

	const deleteFieldOption = ( fieldIndex, optionIndex ) => {
		const updatedFields = [ ...fields ];
		updatedFields[ fieldIndex ].options = updatedFields[
			fieldIndex
		].options.filter( ( _, i ) => i !== optionIndex );
		setAttributes( { fields: updatedFields } );
	};

	return createElement(
		Fragment,
		null,
		createElement(
			InspectorControls,
			null,
			createElement(
				PanelBody,
				{ title: __( 'Form Settings', 'iqtig-forms' ), initialOpen: true },
				createElement( TextControl, {
					label: __( 'Domain', 'iqtig-forms' ),
					value: domain,
					onChange: ( value ) => setAttributes( { domain: value } ),
					help: __( 'The domain for unsubscribe processing', 'iqtig-forms' ),
				} ),
				createElement( TextControl, {
					label: __( 'Form ID', 'iqtig-forms' ),
					value: formId,
					onChange: ( value ) => setAttributes( { formId: value } ),
					help: __( 'Unique identifier for this form', 'iqtig-forms' ),
				} ),
				createElement( TextControl, {
					label: __( 'Redirect URL', 'iqtig-forms' ),
					value: redirectUrl,
					onChange: ( value ) => setAttributes( { redirectUrl: value } ),
					help: __( 'URL to redirect to after submission', 'iqtig-forms' ),
					type: 'url',
				} )
			)
		),
		createElement(
			'div',
			blockProps,
			createElement(
				'div',
				{ className: 'iqtig-forms-editor' },
				createElement(
					'h3',
					null,
					__( 'IQTIG Unsubscribe Form', 'iqtig-forms' )
				),
				createElement(
					'div',
					{ className: 'iqtig-forms-fields-manager' },
					createElement(
						'h4',
						null,
						__( 'Form Fields', 'iqtig-forms' )
					),
					fields.length === 0 &&
						createElement(
							'p',
							{ className: 'iqtig-forms-no-fields' },
							__( 'No fields added yet. Click "Add Field" to get started.', 'iqtig-forms' )
						),
					fields.map( ( field, index ) =>
						createElement(
							'div',
							{
								key: field.id,
								className: 'iqtig-forms-field-editor',
							},
							createElement(
								'div',
								{ className: 'iqtig-forms-field-header' },
								createElement(
									'span',
									{ className: 'iqtig-forms-field-number' },
									`${ index + 1 }.`
								),
								createElement(
									'div',
									{ className: 'iqtig-forms-field-controls' },
									createElement( Button, {
										icon: 'arrow-up-alt2',
										label: __( 'Move up', 'iqtig-forms' ),
										disabled: index === 0,
										onClick: () => moveField( index, 'up' ),
										isSmall: true,
									} ),
									createElement( Button, {
										icon: 'arrow-down-alt2',
										label: __( 'Move down', 'iqtig-forms' ),
										disabled: index === fields.length - 1,
										onClick: () => moveField( index, 'down' ),
										isSmall: true,
									} ),
									createElement( Button, {
										icon: 'trash',
										label: __( 'Delete field', 'iqtig-forms' ),
										onClick: () => deleteField( index ),
										isDestructive: true,
										isSmall: true,
									} )
								)
							),
							createElement( SelectControl, {
								label: __( 'Field Type', 'iqtig-forms' ),
								value: field.type,
								options: [
									{ label: __( 'Text', 'iqtig-forms' ), value: 'text' },
									{ label: __( 'Textarea', 'iqtig-forms' ), value: 'textarea' },
									{ label: __( 'Checkbox', 'iqtig-forms' ), value: 'checkbox' },
									{ label: __( 'Radio', 'iqtig-forms' ), value: 'radio' },
									{ label: __( 'Select', 'iqtig-forms' ), value: 'select' },
								],
								onChange: ( value ) => updateField( index, 'type', value ),
							} ),
							createElement( TextControl, {
								label: __( 'Field Name', 'iqtig-forms' ),
								value: field.name,
								onChange: ( value ) => updateField( index, 'name', value ),
								help: __( 'Used for cookie storage (no spaces)', 'iqtig-forms' ),
							} ),
							createElement( TextControl, {
								label: __( 'Field Label', 'iqtig-forms' ),
								value: field.label,
								onChange: ( value ) => updateField( index, 'label', value ),
							} ),
							createElement( ToggleControl, {
								label: __( 'Required', 'iqtig-forms' ),
								checked: field.required,
								onChange: ( value ) => updateField( index, 'required', value ),
							} ),
							( field.type === 'radio' || field.type === 'select' ) &&
								createElement(
									'div',
									{ className: 'iqtig-forms-field-options' },
									createElement(
										'h5',
										null,
										__( 'Options', 'iqtig-forms' )
									),
									( field.options || [] ).map( ( option, optionIndex ) =>
										createElement(
											'div',
											{
												key: optionIndex,
												className: 'iqtig-forms-option-editor',
											},
											createElement( TextControl, {
												label: __( 'Label', 'iqtig-forms' ),
												value: option.label,
												onChange: ( value ) =>
													updateFieldOption( index, optionIndex, 'label', value ),
											} ),
											createElement( TextControl, {
												label: __( 'Value', 'iqtig-forms' ),
												value: option.value,
												onChange: ( value ) =>
													updateFieldOption( index, optionIndex, 'value', value ),
											} ),
											createElement( Button, {
												icon: 'trash',
												label: __( 'Delete option', 'iqtig-forms' ),
												onClick: () => deleteFieldOption( index, optionIndex ),
												isDestructive: true,
												isSmall: true,
											} )
										)
									),
									createElement( Button, {
										variant: 'secondary',
										onClick: () => addFieldOption( index ),
										isSmall: true,
									}, __( 'Add Option', 'iqtig-forms' ) )
								)
						)
					),
					createElement(
						Button,
						{
							variant: 'primary',
							onClick: addField,
						},
						__( 'Add Field', 'iqtig-forms' )
					)
				),
				fields.length > 0 &&
					createElement(
						'div',
						{ className: 'iqtig-forms-preview' },
						createElement(
							'h4',
							null,
							__( 'Form Preview', 'iqtig-forms' )
						),
						createElement(
							'div',
							{ className: 'iqtig-forms-preview-fields' },
							fields.map( ( field ) =>
								createElement(
									'div',
									{
										key: field.id,
										className: 'iqtig-forms-preview-field',
									},
									createElement(
										'label',
										null,
										field.label,
										field.required && ' *'
									),
									field.type === 'textarea' &&
										createElement( 'textarea', {
											placeholder: field.label,
											disabled: true,
										} ),
									field.type === 'text' &&
										createElement( 'input', {
											type: 'text',
											placeholder: field.label,
											disabled: true,
										} ),
									field.type === 'checkbox' &&
										createElement( 'input', {
											type: 'checkbox',
											disabled: true,
										} ),
									field.type === 'radio' &&
										( field.options || [] ).map( ( option, i ) =>
											createElement(
												'div',
												{ key: i },
												createElement( 'input', {
													type: 'radio',
													name: field.name,
													disabled: true,
												} ),
												createElement( 'span', null, option.label )
											)
										),
									field.type === 'select' &&
										createElement(
											'select',
											{ disabled: true },
											createElement( 'option', null, '-- Select --' ),
											( field.options || [] ).map( ( option, i ) =>
												createElement( 'option', { key: i }, option.label )
											)
										)
								)
							),
							createElement(
								'button',
								{
									type: 'button',
									disabled: true,
									className: 'iqtig-forms-submit',
								},
								__( 'Unsubscribe', 'iqtig-forms' )
							)
						)
					)
			)
		)
	);
}
