<?php
/**
 * Plugin deactivation functions.
 *
 * @package IQTIG_Forms
 */

namespace IQTIG_Forms;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin deactivation callback.
 *
 * Runs when the plugin is deactivated. Performs cleanup tasks.
 *
 * @return void
 */
function deactivate_plugin() {
	// Flush rewrite rules to clean up REST API endpoints.
	flush_rewrite_rules();

	// Note: We intentionally do NOT delete plugin options here.
	// Options are preserved in case the plugin is reactivated.
	// If users want to completely remove data, they should uninstall the plugin.
}
