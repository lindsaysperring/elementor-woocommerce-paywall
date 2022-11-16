<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://lindsaysperring.com
 * @since             1.0.0
 * @package           Elementor_Woocommerce_Paywall
 *
 * @wordpress-plugin
 * Plugin Name:       Elementor Woocommerce Paywall
 * Plugin URI:        https://github.com/lindsaysperring/elementor-woocommerce-pawyall
 * Description:       A plugin to hide blocks if a user hasn't purchased a specific woocommerce product
 * Version:           1.0.0
 * Author:            Lindsay Sperring
 * Author URI:        https://lindsaysperring.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       elementor-woocommerce-paywall
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ELEMENTOR_WOOCOMMERCE_PAYWALL_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-elementor-woocommerce-paywall-activator.php
 */
function activate_elementor_woocommerce_paywall() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-elementor-woocommerce-paywall-activator.php';
	Elementor_Woocommerce_Paywall_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-elementor-woocommerce-paywall-deactivator.php
 */
function deactivate_elementor_woocommerce_paywall() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-elementor-woocommerce-paywall-deactivator.php';
	Elementor_Woocommerce_Paywall_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_elementor_woocommerce_paywall' );
register_deactivation_hook( __FILE__, 'deactivate_elementor_woocommerce_paywall' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-elementor-woocommerce-paywall.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_elementor_woocommerce_paywall() {

	$plugin = new Elementor_Woocommerce_Paywall();
	$plugin->run();

}
run_elementor_woocommerce_paywall();
