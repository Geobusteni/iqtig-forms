/**
 * Login Form Block Editor
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
} from '@wordpress/components';

export default function Edit( { attributes, setAttributes } ) {
	const { domain, formId, redirectUrl, useGlobalRedirect, fields } = attributes;
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

	const renderFieldPreview = ( field ) => {
		switch ( field.type ) {
			case 'textarea':
				return <textarea placeholder={ field.label } disabled />;
			case 'text':
				return <input type="text" placeholder={ field.label } disabled />;
			case 'checkbox':
				return <input type="checkbox" disabled />;
			case 'radio':
				return ( field.options || [] ).map( ( option, i ) => (
					<div key={ i }>
						<input type="radio" name={ field.name } disabled />
						<span>{ option.label }</span>
					</div>
				) );
			case 'select':
				return (
					<select disabled>
						<option>-- Select --</option>
						{ ( field.options || [] ).map( ( option, i ) => (
							<option key={ i }>{ option.label }</option>
						) ) }
					</select>
				);
			default:
				return null;
		}
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Form Settings', 'iqtig-forms' ) } initialOpen={ true }>
					<TextControl
						label={ __( 'Domain', 'iqtig-forms' ) }
						value={ domain }
						onChange={ ( value ) => setAttributes( { domain: value } ) }
						help={ __( 'The domain for authentication', 'iqtig-forms' ) }
					/>
					<TextControl
						label={ __( 'Form ID', 'iqtig-forms' ) }
						value={ formId }
						onChange={ ( value ) => setAttributes( { formId: value } ) }
						help={ __( 'Unique identifier for this form', 'iqtig-forms' ) }
					/>
					<ToggleControl
						label={ __( 'Use Global Redirect URL', 'iqtig-forms' ) }
						checked={ useGlobalRedirect }
						onChange={ ( value ) => setAttributes( { useGlobalRedirect: value } ) }
						help={ __( 'When enabled, uses the redirect URL from plugin settings. Disable to set a custom URL for this form.', 'iqtig-forms' ) }
					/>
					{ ! useGlobalRedirect && (
						<TextControl
							label={ __( 'Redirect URL', 'iqtig-forms' ) }
							value={ redirectUrl }
							onChange={ ( value ) => setAttributes( { redirectUrl: value } ) }
							help={ __( 'URL to redirect to after submission', 'iqtig-forms' ) }
							type="url"
						/>
					) }
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<div className="iqtig-forms-editor">
					<h3>{ __( 'IQTIG Login Form', 'iqtig-forms' ) }</h3>

					<div className="iqtig-forms-fields-manager">
						<h4>{ __( 'Form Fields', 'iqtig-forms' ) }</h4>

						{ fields.length === 0 && (
							<p className="iqtig-forms-no-fields">
								{ __( 'No fields added yet. Click "Add Field" to get started.', 'iqtig-forms' ) }
							</p>
						) }

						{ fields.map( ( field, index ) => (
							<div key={ field.id } className="iqtig-forms-field-editor">
								<div className="iqtig-forms-field-header">
									<span className="iqtig-forms-field-number">{ `${ index + 1 }.` }</span>
									<div className="iqtig-forms-field-controls">
										<Button
											icon="arrow-up-alt2"
											label={ __( 'Move up', 'iqtig-forms' ) }
											disabled={ index === 0 }
											onClick={ () => moveField( index, 'up' ) }
											isSmall
										/>
										<Button
											icon="arrow-down-alt2"
											label={ __( 'Move down', 'iqtig-forms' ) }
											disabled={ index === fields.length - 1 }
											onClick={ () => moveField( index, 'down' ) }
											isSmall
										/>
										<Button
											icon="trash"
											label={ __( 'Delete field', 'iqtig-forms' ) }
											onClick={ () => deleteField( index ) }
											isDestructive
											isSmall
										/>
									</div>
								</div>

								<SelectControl
									label={ __( 'Field Type', 'iqtig-forms' ) }
									value={ field.type }
									options={ [
										{ label: __( 'Text', 'iqtig-forms' ), value: 'text' },
										{ label: __( 'Textarea', 'iqtig-forms' ), value: 'textarea' },
										{ label: __( 'Checkbox', 'iqtig-forms' ), value: 'checkbox' },
										{ label: __( 'Radio', 'iqtig-forms' ), value: 'radio' },
										{ label: __( 'Select', 'iqtig-forms' ), value: 'select' },
									] }
									onChange={ ( value ) => updateField( index, 'type', value ) }
								/>

								<TextControl
									label={ __( 'Field Name', 'iqtig-forms' ) }
									value={ field.name }
									onChange={ ( value ) => updateField( index, 'name', value ) }
									help={ __( 'Used for cookie storage (no spaces)', 'iqtig-forms' ) }
								/>

								<TextControl
									label={ __( 'Field Label', 'iqtig-forms' ) }
									value={ field.label }
									onChange={ ( value ) => updateField( index, 'label', value ) }
								/>

								<ToggleControl
									label={ __( 'Required', 'iqtig-forms' ) }
									checked={ field.required }
									onChange={ ( value ) => updateField( index, 'required', value ) }
								/>

								{ ( field.type === 'radio' || field.type === 'select' ) && (
									<div className="iqtig-forms-field-options">
										<h5>{ __( 'Options', 'iqtig-forms' ) }</h5>
										{ ( field.options || [] ).map( ( option, optionIndex ) => (
											<div key={ optionIndex } className="iqtig-forms-option-editor">
												<TextControl
													label={ __( 'Label', 'iqtig-forms' ) }
													value={ option.label }
													onChange={ ( value ) =>
														updateFieldOption( index, optionIndex, 'label', value )
													}
												/>
												<TextControl
													label={ __( 'Value', 'iqtig-forms' ) }
													value={ option.value }
													onChange={ ( value ) =>
														updateFieldOption( index, optionIndex, 'value', value )
													}
												/>
												<Button
													icon="trash"
													label={ __( 'Delete option', 'iqtig-forms' ) }
													onClick={ () => deleteFieldOption( index, optionIndex ) }
													isDestructive
													isSmall
												/>
											</div>
										) ) }
										<Button
											variant="secondary"
											onClick={ () => addFieldOption( index ) }
											isSmall
										>
											{ __( 'Add Option', 'iqtig-forms' ) }
										</Button>
									</div>
								) }
							</div>
						) ) }

						<Button variant="primary" onClick={ addField }>
							{ __( 'Add Field', 'iqtig-forms' ) }
						</Button>
					</div>

					{ fields.length > 0 && (
						<div className="iqtig-forms-preview">
							<h4>{ __( 'Form Preview', 'iqtig-forms' ) }</h4>
							<div className="iqtig-forms-preview-fields">
								{ fields.map( ( field ) => (
									<div key={ field.id } className="iqtig-forms-preview-field">
										<label>
											{ field.label }
											{ field.required && ' *' }
										</label>
										{ renderFieldPreview( field ) }
									</div>
								) ) }
								<button type="button" disabled className="iqtig-forms-submit">
									{ __( 'Submit', 'iqtig-forms' ) }
								</button>
							</div>
						</div>
					) }
				</div>
			</div>
		</>
	);
}
