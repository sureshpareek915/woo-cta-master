<?php
/**
 * Plugin Name: CTA
 * Plugin URI: https://www.tidbitsolution.com/
 * Description: CTA is provide advanced add to cart functionality.
 * Version: 1.0.0
 * Author: Shanay
 * Author URI: https://www.tidbitsolution.com/
 * Text Domain: cta
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define WC_PLUGIN_FILE.
if ( ! defined( 'CTA_PLUGIN_FILE' ) ) {
	define( 'CTA_PLUGIN_FILE', __FILE__ );
}

define( 'CTA_VERSION', '1.0.0' );
define( 'CTA_TEMPLATE_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/' );
define( 'CTA_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
define( 'CTA_MAIN_FILE', __FILE__ );
define( 'CTA_ABSPATH', dirname( __FILE__ ) . '/' );

function CTA_Active() {

	// Require parent plugin
	if( ! is_plugin_active('woocommerce/woocommerce.php') && current_user_can('activate_plugins')) {

		// Stop activation redirect and show error
        wp_die('Sorry, but this plugin requires the Woocommerce Plugin to be installed and active. <br><a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>');
	}
}
register_activation_hook( CTA_PLUGIN_FILE , 'CTA_Active');

// Include the main WooCommerce class.
if ( ! class_exists( 'WooTicketBooking' ) ) {
	include_once dirname( __FILE__ ) . '/includes/settings.php';
}