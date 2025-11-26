<?php
/**
 * Settings page functions.
 *
 * @package IQTIG_Forms
 */

namespace IQTIG_Forms;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Register admin menu and settings.
add_action( 'admin_menu', __NAMESPACE__ . '\register_settings_page' );
add_action( 'admin_init', __NAMESPACE__ . '\register_settings' );

/**
 * Register the settings page in the admin menu.
 *
 * Adds the IQTIG Forms settings page under the Settings menu.
 *
 * @return void
 */
function register_settings_page() {
	add_options_page(
		__( 'IQTIG Forms Settings', 'iqtig-forms' ),
		__( 'IQTIG Forms', 'iqtig-forms' ),
		'manage_options',
		'iqtig-forms-settings',
		__NAMESPACE__ . '\render_settings_page'
	);
}

/**
 * Register plugin settings.
 *
 * Registers the settings option and sanitization callback.
 *
 * @return void
 */
function register_settings() {
	register_setting(
		'iqtig_forms_settings_group',
		'iqtig_forms_settings',
		array(
			'type' => 'array',
			'sanitize_callback' => __NAMESPACE__ . '\sanitize_settings',
			'default' => array(),
		)
	);

	// Add API Configuration section.
	add_settings_section(
		'iqtig_forms_api_section',
		__( 'API Configuration', 'iqtig-forms' ),
		__NAMESPACE__ . '\render_api_section_description',
		'iqtig-forms-settings'
	);

	// Add Login Configuration section.
	add_settings_section(
		'iqtig_forms_login_section',
		__( 'Login Configuration', 'iqtig-forms' ),
		__NAMESPACE__ . '\render_login_section_description',
		'iqtig-forms-settings'
	);

	// Add Unsubscribe Configuration section.
	add_settings_section(
		'iqtig_forms_unsubscribe_section',
		__( 'Unsubscribe Configuration', 'iqtig-forms' ),
		__NAMESPACE__ . '\render_unsubscribe_section_description',
		'iqtig-forms-settings'
	);

	// API Base URL field.
	add_settings_field(
		'api_base_url',
		__( 'API Base URL', 'iqtig-forms' ),
		__NAMESPACE__ . '\render_text_field',
		'iqtig-forms-settings',
		'iqtig_forms_api_section',
		array(
			'label_for' => 'api_base_url',
			'class' => 'regular-text',
			'description' => __( 'The base URL for the IQTIG API.', 'iqtig-forms' ),
		)
	);

	// JWT Cookie Lifetime field.
	add_settings_field(
		'jwt_cookie_lifetime',
		__( 'JWT Cookie Lifetime', 'iqtig-forms' ),
		__NAMESPACE__ . '\render_number_field',
		'iqtig-forms-settings',
		'iqtig_forms_api_section',
		array(
			'label_for' => 'jwt_cookie_lifetime',
			'class' => 'small-text',
			'description' => __( 'Cookie lifetime in seconds (default: 14400 = 4 hours).', 'iqtig-forms' ),
			'min' => 60,
		)
	);

	// Login Redirect URL field.
	add_settings_field(
		'login_redirect_url',
		__( 'Login Redirect URL', 'iqtig-forms' ),
		__NAMESPACE__ . '\render_text_field',
		'iqtig-forms-settings',
		'iqtig_forms_login_section',
		array(
			'label_for' => 'login_redirect_url',
			'class' => 'regular-text',
			'description' => __( 'Default redirect URL after successful login (leave empty for homepage).', 'iqtig-forms' ),
		)
	);

	// Unsubscribe Redirect URL field.
	add_settings_field(
		'unsubscribe_redirect_url',
		__( 'Unsubscribe Redirect URL', 'iqtig-forms' ),
		__NAMESPACE__ . '\render_text_field',
		'iqtig-forms-settings',
		'iqtig_forms_unsubscribe_section',
		array(
			'label_for' => 'unsubscribe_redirect_url',
			'class' => 'regular-text',
			'description' => __( 'Default redirect URL after unsubscribe (leave empty for homepage).', 'iqtig-forms' ),
		)
	);

	// Default Survey ID field.
	add_settings_field(
		'default_survey_id',
		__( 'Default Survey ID', 'iqtig-forms' ),
		__NAMESPACE__ . '\render_text_field',
		'iqtig-forms-settings',
		'iqtig_forms_unsubscribe_section',
		array(
			'label_for' => 'default_survey_id',
			'class' => 'regular-text',
			'description' => __( 'Default survey ID for unsubscribe forms.', 'iqtig-forms' ),
		)
	);

	// Use Default Survey ID checkbox.
	add_settings_field(
		'use_default_survey_id',
		__( 'Use Default Survey ID', 'iqtig-forms' ),
		__NAMESPACE__ . '\render_checkbox_field',
		'iqtig-forms-settings',
		'iqtig_forms_unsubscribe_section',
		array(
			'label_for' => 'use_default_survey_id',
			'description' => __( 'Always use the default survey ID instead of URL parameter.', 'iqtig-forms' ),
		)
	);
}

/**
 * Render API section description.
 *
 * @return void
 */
function render_api_section_description() {
	echo '<p>' . esc_html__( 'Configure the IQTIG API connection settings.', 'iqtig-forms' ) . '</p>';
}

/**
 * Render login section description.
 *
 * @return void
 */
function render_login_section_description() {
	echo '<p>' . esc_html__( 'Configure default settings for login forms.', 'iqtig-forms' ) . '</p>';
}

/**
 * Render unsubscribe section description.
 *
 * @return void
 */
function render_unsubscribe_section_description() {
	echo '<p>' . esc_html__( 'Configure default settings for unsubscribe forms.', 'iqtig-forms' ) . '</p>';
}

/**
 * Render text field.
 *
 * @param array $args Field arguments.
 * @return void
 */
function render_text_field( $args ) {
	$settings = get_option( 'iqtig_forms_settings', array() );
	$field_id = isset( $args['label_for'] ) ? $args['label_for'] : '';
	$value = isset( $settings[ $field_id ] ) ? $settings[ $field_id ] : '';
	$class = isset( $args['class'] ) ? $args['class'] : 'regular-text';
	$description = isset( $args['description'] ) ? $args['description'] : '';

	printf(
		'<input type="text" id="%s" name="iqtig_forms_settings[%s]" value="%s" class="%s" />',
		esc_attr( $field_id ),
		esc_attr( $field_id ),
		esc_attr( $value ),
		esc_attr( $class )
	);

	if ( ! empty( $description ) ) {
		printf(
			'<p class="description">%s</p>',
			esc_html( $description )
		);
	}
}

/**
 * Render number field.
 *
 * @param array $args Field arguments.
 * @return void
 */
function render_number_field( $args ) {
	$settings = get_option( 'iqtig_forms_settings', array() );
	$field_id = isset( $args['label_for'] ) ? $args['label_for'] : '';
	$value = isset( $settings[ $field_id ] ) ? $settings[ $field_id ] : '';
	$class = isset( $args['class'] ) ? $args['class'] : 'small-text';
	$description = isset( $args['description'] ) ? $args['description'] : '';
	$min = isset( $args['min'] ) ? $args['min'] : 0;

	printf(
		'<input type="number" id="%s" name="iqtig_forms_settings[%s]" value="%s" class="%s" min="%d" />',
		esc_attr( $field_id ),
		esc_attr( $field_id ),
		esc_attr( $value ),
		esc_attr( $class ),
		absint( $min )
	);

	if ( ! empty( $description ) ) {
		printf(
			'<p class="description">%s</p>',
			esc_html( $description )
		);
	}
}

/**
 * Render checkbox field.
 *
 * @param array $args Field arguments.
 * @return void
 */
function render_checkbox_field( $args ) {
	$settings = get_option( 'iqtig_forms_settings', array() );
	$field_id = isset( $args['label_for'] ) ? $args['label_for'] : '';
	$checked = isset( $settings[ $field_id ] ) && $settings[ $field_id ];
	$description = isset( $args['description'] ) ? $args['description'] : '';

	printf(
		'<input type="checkbox" id="%s" name="iqtig_forms_settings[%s]" value="1" %s />',
		esc_attr( $field_id ),
		esc_attr( $field_id ),
		checked( $checked, true, false )
	);

	if ( ! empty( $description ) ) {
		printf(
			' <label for="%s">%s</label>',
			esc_attr( $field_id ),
			esc_html( $description )
		);
	}
}

/**
 * Render the settings page.
 *
 * Displays the settings form with all configuration options.
 *
 * @return void
 */
function render_settings_page() {
	// Check user capabilities.
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Display success message if settings were updated.
	if ( isset( $_GET['settings-updated'] ) ) {
		add_settings_error(
			'iqtig_forms_messages',
			'iqtig_forms_message',
			__( 'Settings saved successfully.', 'iqtig-forms' ),
			'updated'
		);
	}

	settings_errors( 'iqtig_forms_messages' );
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'iqtig_forms_settings_group' );
			do_settings_sections( 'iqtig-forms-settings' );
			submit_button( __( 'Save Settings', 'iqtig-forms' ) );
			?>
		</form>
	</div>
	<?php
}

/**
 * Sanitize settings input.
 *
 * Validates and sanitizes all settings before saving.
 *
 * @param array $input The input values to sanitize.
 * @return array The sanitized values.
 */
function sanitize_settings( $input ) {
	$sanitized = array();

	// Sanitize API Base URL.
	if ( isset( $input['api_base_url'] ) ) {
		$sanitized['api_base_url'] = esc_url_raw( $input['api_base_url'] );
	}

	// Sanitize JWT Cookie Lifetime.
	if ( isset( $input['jwt_cookie_lifetime'] ) ) {
		$sanitized['jwt_cookie_lifetime'] = absint( $input['jwt_cookie_lifetime'] );
	}

	// Sanitize Login Redirect URL.
	if ( isset( $input['login_redirect_url'] ) ) {
		$sanitized['login_redirect_url'] = esc_url_raw( $input['login_redirect_url'] );
	}

	// Sanitize Unsubscribe Redirect URL.
	if ( isset( $input['unsubscribe_redirect_url'] ) ) {
		$sanitized['unsubscribe_redirect_url'] = esc_url_raw( $input['unsubscribe_redirect_url'] );
	}

	// Sanitize Default Survey ID.
	if ( isset( $input['default_survey_id'] ) ) {
		$sanitized['default_survey_id'] = sanitize_text_field( $input['default_survey_id'] );
	}

	// Sanitize Use Default Survey ID checkbox.
	if ( isset( $input['use_default_survey_id'] ) ) {
		$sanitized['use_default_survey_id'] = (bool) $input['use_default_survey_id'];
	} else {
		$sanitized['use_default_survey_id'] = false;
	}

	return $sanitized;
}

/**
 * Get a single setting value.
 *
 * Helper function to retrieve a specific setting with a default fallback.
 *
 * @param string $key     The setting key to retrieve.
 * @param mixed  $default The default value if setting doesn't exist.
 * @return mixed The setting value or default.
 */
function get_setting( $key, $default = '' ) {
	$settings = get_option( 'iqtig_forms_settings', array() );

	// Set defaults for specific keys if not in settings.
	$defaults = array(
		'api_base_url'              => 'https://api.iqtig.org',
		'jwt_cookie_lifetime'       => 14400,
		'login_redirect_url'        => '',
		'unsubscribe_redirect_url'  => '',
		'default_survey_id'         => '',
		'use_default_survey_id'     => false,
	);

	// If key exists in settings, return it.
	if ( isset( $settings[ $key ] ) ) {
		return $settings[ $key ];
	}

	// If key has a default, return it.
	if ( isset( $defaults[ $key ] ) ) {
		return $defaults[ $key ];
	}

	// Otherwise return provided default.
	return $default;
}
