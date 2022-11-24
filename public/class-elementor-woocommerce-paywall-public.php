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

	public function has_bought_items($bought_product_id)
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

	function should_render($bool, $widget)
	{
		$post_product_settings = get_option('ep_settings_product_post_link');
		if ($post_product_settings == false || !is_array($post_product_settings)) {
			return true;
		}

		$postId = get_the_ID();

		foreach ($post_product_settings as $productId => $postArray) {
			if (in_array($postArray, $postId) && 'theme-post-excerpt' == $widget->get_name()) {
				return $this->has_bought_items($productId);
			}
		}

		return true;
	}

	function hide_content($widget_content, $widget)
	{
		if ('theme-post-excerpt' == $widget->get_name()) {
			var_dump($widget->get_settings_for_display()['_element_id']);
		}
		$post_product_settings = get_option('ep_settings_product_post_link');
		if ($post_product_settings == false || !is_array($post_product_settings)) {
			return $widget_content;
		}

		$postId = get_the_ID();

		foreach ($post_product_settings as $productId => $postArray) {
			if (in_array($postId, $postArray) && 'theme-post-excerpt' == $widget->get_name() && !$this->has_bought_items($productId)) {
				return null;
			}
		}

		return $widget_content;
	}
}
