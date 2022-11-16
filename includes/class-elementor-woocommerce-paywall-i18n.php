<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://lindsaysperring.com
 * @since      1.0.0
 *
 * @package    Elementor_Woocommerce_Paywall
 * @subpackage Elementor_Woocommerce_Paywall/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Elementor_Woocommerce_Paywall
 * @subpackage Elementor_Woocommerce_Paywall/includes
 * @author     Lindsay Sperring <lindsay@lindsaysperring.com>
 */
class Elementor_Woocommerce_Paywall_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'elementor-woocommerce-paywall',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
