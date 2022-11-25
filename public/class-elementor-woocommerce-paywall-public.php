<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://lindsaysperring.com
 * @since      1.0.0
 *
 * @package    Elementor_Woocommerce_Paywall
 * @subpackage Elementor_Woocommerce_Paywall/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Elementor_Woocommerce_Paywall
 * @subpackage Elementor_Woocommerce_Paywall/public
 * @author     Lindsay Sperring <lindsay@lindsaysperring.com>
 */
class Elementor_Woocommerce_Paywall_Public
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * @since 	1.0.0
	 * @access 	private
	 * @var 	array	$post_product_settings		array of product-post bindings
	 */
	private $post_product_settings;

	/**
	 * @since 	1.0.0
	 * @access 	private
	 * @var 	string	$element_to_hide_css_id		element to hide when user hasn't bought product
	 */
	private $element_to_hide_css_id = "";

	/**
	 * @since 	1.0.0
	 * @access 	private
	 * @var 	string	$element_to_show_css_id		element to show when user hasn't bought product
	 */
	private $element_to_show_css_id = "";

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->post_product_settings = get_option('ep_settings_product_post_link');
		if (!is_array($this->post_product_settings)) {
			$this->post_product_settings = array();
		}
		$settings = get_option('ep_settings');
		if (!is_array($settings)) {
			$this->settings = array();
		}
		if (array_key_exists('paywall_id', $settings)) {
			$this->element_to_hide_css_id = $settings['paywall_id'];
		}
		if (array_key_exists('paywall_id_show', $settings)) {
			$this->element_to_show_css_id = $settings['paywall_id_show'];
		}
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Elementor_Woocommerce_Paywall_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Elementor_Woocommerce_Paywall_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/elementor-woocommerce-paywall-public.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Elementor_Woocommerce_Paywall_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Elementor_Woocommerce_Paywall_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/elementor-woocommerce-paywall-public.js', array('jquery'), $this->version, false);
	}

	/**
	 * Check if current user has bought a product with specific id
	 * 
	 * @since 1.0.0
	 * 
	 * @var	int ID of purchased product to be checked
	 * snippet modified from https://stackoverflow.com/a/38772202/18434026
	 */

	private function has_bought_items($bought_product_id)
	{
		$bought = false;

		// Get all customer orders
		$customer_orders = get_posts(array(
			'numberposts' => -1,
			'meta_key'    => '_customer_user',
			'meta_value'  => get_current_user_id(),
			'post_type'   => 'shop_order', // WC orders post type
			'post_status' => 'wc-completed' // Only orders with status "completed"
		));
		foreach ($customer_orders as $customer_order) {
			// Updated compatibility with WooCommerce 3+
			$order = wc_get_order($customer_order);
			$order_id = method_exists($order, 'get_id') ? $order->get_id() : $order->id;

			// Iterating through each current customer products bought in the order
			foreach ($order->get_items() as $item) {
				// WC 3+ compatibility
				if (version_compare(WC_VERSION, '3.0', '<'))
					$product_id = $item['product_id'];
				else
					$product_id = $item->get_product_id();

				// Your condition related to your 2 specific products Ids
				if ($bought_product_id == $product_id)
					$bought = true;
			}
		}
		// return "true" if one the specifics products have been bought before by customer
		return $bought;
	}

	public function should_render($bool, $widget)
	{
		if (
			$this->post_product_settings == false || !is_array($this->post_product_settings)
			|| ($widget->get_settings_for_display()['_element_id'] != $this->element_to_hide_css_id
				&& $widget->get_settings_for_display()['_element_id'] != $this->element_to_show_css_id)
		) {
			return true;
		}


		if ($this->paywall_is_active()) {
			if ($widget->get_settings_for_display()['_element_id'] == $this->element_to_hide_css_id) {
				return false;
			} elseif ($widget->get_settings_for_display()['_element_id'] == $this->element_to_show_css_id) {
				return true;
			}
		}

		if ($widget->get_settings_for_display()['_element_id'] == $this->element_to_show_css_id) {
			return false;
		}

		return $bool;
	}

	public function hide_content($widget_content, $widget)
	{
		if (
			$this->post_product_settings == false
			|| !is_array($this->post_product_settings)
			|| $widget->get_settings_for_display()['_element_id'] != $this->element_to_hide_css_id
		) {
			return $widget_content;
		}

		if ($this->paywall_is_active()) {
			var_dump("hello");
			return null;
		}

		return $widget_content;
	}

	public function paywall_is_active()
	{
		$postId = get_the_ID();
		foreach ($this->post_product_settings as $productId => $postArray) {
			if (in_array($postId, $postArray) && !$this->has_bought_items($productId)) {
				return true;
			}
		}

		return false;
	}
}
