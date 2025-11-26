<?php
/**
 * Plugin Name: IQTIG Forms
 * Plugin URI: https://iqtig.org
 * Description: Custom forms plugin for IQTIG WordPress site with login and unsubscribe functionality
 * Version: 1.1.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: IQTIG
 * Author URI: https://iqtig.org
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: iqtig-forms
 * Domain Path: /languages
 *
 * @package IQTIG_Forms
 */

namespace IQTIG_Forms;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
define( 'IQTIG_FORMS_VERSION', '1.1.0' );
define( 'IQTIG_FORMS_PATH', plugin_dir_path( __FILE__ ) );
define( 'IQTIG_FORMS_URL', plugin_dir_url( __FILE__ ) );

// Load plugin files.
require_once IQTIG_FORMS_PATH . 'includes/activation.php';
require_once IQTIG_FORMS_PATH . 'includes/deactivation.php';
require_once IQTIG_FORMS_PATH . 'includes/blocks.php';
require_once IQTIG_FORMS_PATH . 'includes/assets.php';
require_once IQTIG_FORMS_PATH . 'includes/cookie-handler.php';
require_once IQTIG_FORMS_PATH . 'includes/ajax-handler.php';
require_once IQTIG_FORMS_PATH . 'includes/settings-page.php';

// Register activation and deactivation hooks.
register_activation_hook( __FILE__, __NAMESPACE__ . '\activate_plugin' );
register_deactivation_hook( __FILE__, __NAMESPACE__ . '\deactivate_plugin' );

// Load text domain for translations.
add_action( 'init', __NAMESPACE__ . '\load_textdomain' );

/**
 * Load plugin text domain for translations.
 *
 * @return void
 */
function load_textdomain() {
	load_plugin_textdomain(
		'iqtig-forms',
		false,
		dirname( plugin_basename( __FILE__ ) ) . '/languages'
	);
}
