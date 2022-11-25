<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://lindsaysperring.com
 * @since      1.0.0
 *
 * @package    Elementor_Woocommerce_Paywall
 * @subpackage Elementor_Woocommerce_Paywall/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Elementor_Woocommerce_Paywall
 * @subpackage Elementor_Woocommerce_Paywall/admin
 * @author     Lindsay Sperring <lindsay@lindsaysperring.com>
 */
class Elementor_Woocommerce_Paywall_Admin
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
	 * The slug of the menu page of this plugin
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $slug    The slug of the menu page of this plugin.
	 */
	private $slug = 'elementor-paywall/admin.php';

	/**
	 * Settings to be used by admin and public
	 * 
	 * @since	1.0.0
	 * @access	private
	 * @var		array
	 */
	private $settings;

	/**
	 * Products so it doesn't have to be gotten twice
	 * 
	 * @since 1.0.0
	 * @access private
	 * @var array
	 */
	private $products;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->settings = get_option('ep_settings');


		if (!is_array($this->settings)) {
			$this->settings = array();
		}
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles($hook_suffix)
	{
		if ($hook_suffix != 'toplevel_page_elementor-paywall/admin') {
			return;
		}
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

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/elementor-woocommerce-paywall-admin.css', array(), $this->version, 'all');
		wp_register_style('bootstrap-multiselect', 'https://phpcoder.tech/multiselect/css/jquery.multiselect.css');
		wp_enqueue_style('bootstrap-multiselect');
		wp_register_style('elementor-paywall-bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');
		wp_enqueue_style('elementor-paywall-bootstrap');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts($hook_suffix)
	{
		if ($hook_suffix != 'toplevel_page_elementor-paywall/admin') {
			return;
		}
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

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/elementor-woocommerce-paywall-admin.js', array('jquery'), $this->version, false);
		wp_register_script('bootstrap-multiselect', 'https://phpcoder.tech/multiselect/js/jquery.multiselect.js');
		wp_enqueue_script('bootstrap-multiselect');
		wp_register_script('elementor-paywall-bootstrap', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.2/js/bootstrap.bundle.min.js');
		wp_enqueue_script('elementor-paywall-bootstrap');
	}

	public function admin_menu()
	{
		add_menu_page("Elementor Paywall Settings", "Elementor Paywall", "manage_options", $this->slug, array($this, "admin_menu_page"));
	}

	public function admin_menu_page()
	{
?>
		<div class="wrap">
			<h2>Elementor Paywall Settings</h2>
			<p></p>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
				settings_fields($this->slug);
				do_settings_sections($this->slug);
				submit_button();
				?>
			</form>

			<?php echo $this->product_select(); ?>

			<form method="post" action="options.php">
				<?php
				settings_fields($this->slug . "2");
				do_settings_sections($this->slug . "2");
				submit_button();
				?>
			</form>
		</div>
	<?php
	}

	public function elementor_paywall_settings_page_init()
	{
		register_setting($this->slug, 'ep_settings');

		register_setting($this->slug . "2", 'ep_settings_product_post_link');

		add_settings_section(
			'elementor-paywall-settings-section',
			'Paywall Settings',
			'',
			$this->slug
		);

		add_settings_section('product-post-binding-section', 'Link Posts to Products', '', $this->slug . "2");

		add_settings_field(
			'ep_post_type_select',
			'Select Post Type where paywall applies',
			array($this, "post_type_select"),
			$this->slug,
			'elementor-paywall-settings-section'
		);

		add_settings_field(
			'ep_paywall_css_id',
			'CSS ID of element to hide',
			array($this, "paywall_css_id"),
			$this->slug,
			'elementor-paywall-settings-section'
		);

		add_settings_field(
			'ep_paywall_css_id_to_show',
			'CSS ID of element to show when not bought',
			array($this, "paywall_css_id_show"),
			$this->slug,
			'elementor-paywall-settings-section'
		);

		add_settings_field(
			'ep_post_product_select',
			'Select posts',
			array($this, 'post_product_select'),
			$this->slug . "2",
			'product-post-binding-section'
		);
	}

	public function post_type_select()
	{
		$args = array(
			'public'   => true,
			'_builtin' => false
		);
		$post_types = get_post_types($args, "names", "or");

		if ($this->settings == false || !array_key_exists("post_type", $this->settings)) {
			$this->settings['post_type'] = array();
		}

	?>
		<select name="ep_settings[post_type][]" multiple class="select" id="post_type_multiselect">
			<?php
			foreach ($post_types as $key => $value) {
				$selected = in_array($key, $this->settings['post_type']) ? 'selected' : '';
			?>
				<option value="<?php echo $key ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
			<?php
			}
			?>
		</select>
	<?php
	}

	public function paywall_css_id()
	{
		
		if (!array_key_exists('paywall_id', $this->settings)) {
			$this->settings['paywall_id'] = "";
		}

	?>
		<input name="ep_settings[paywall_id]" type="text" value="<?php echo esc_attr($this->settings['paywall_id']); ?>" />
	<?php
	}

	public function paywall_css_id_show()
	{

		if (!array_key_exists('paywall_id_show', $this->settings)) {
			$this->settings['paywall_id_show'] = "";
		}

	?>
		<input name=" ep_settings[paywall_id_show]" type="text" value="<?php echo esc_attr($this->settings['paywall_id_show']); ?>" />
	<?php
	}

	public function product_select()
	{
		$args = array(
			'post_type'      => 'product',
			'numberposts' => -1
		);
		$products = get_posts($args);

	?>
		<label for="product_select">Select Product</label>
		<select id="product_select">
			<?php
			foreach ($products as $product) {
			?>
				<option value="<?php echo $product->ID; ?>"><?php echo $product->post_title; ?></option>
			<?php
			}
			?>
		</select>
		<button id="product_select_button">Select</button>
	<?php
	}

	public function post_product_select()
	{

		$posts = [];
		if ($this->settings != false && array_key_exists("post_type", $this->settings)) {
			foreach ($this->settings["post_type"] as $value) {
				$tempPosts = get_posts(['post_type' => $value, 'numberposts' => -1]);
				$posts = array_merge($posts, $tempPosts);
			}
		}

		$existing = get_option('ep_settings_product_post_link');
		if ($existing == false || !is_array($existing)) {
			$existing = array();
		}

		$product_post_array = array();
	?>
		<script>
			const posts = <?php echo json_encode($posts); ?>
		</script>
		<div id="post-product-select">
			<?php
			foreach ($existing as $key => $values) {
				array_push($product_post_array, "ep_settings_product_post_link_" . $key);
			?>
				<div id="ep_settings_product_post_link_wrapper_<?php echo $key; ?>">
					<label for="<?php echo $key; ?>"><span onclick="deleteInput(<?php echo $key; ?>)" class="ep_product_delete">&#x2715;</span><?php echo $this->getProductFromId($key); ?></label>
					<select name="ep_settings_product_post_link[<?php echo $key; ?>][]" id="ep_settings_product_post_link_<?php echo $key; ?>" multiple class="select" style="display:inline;">
						<?php
						foreach ($posts as $post) {
						?>
							<option value="<?php echo $post->ID; ?>" <?php echo (in_array($post->ID, $values) ? "selected" : "") ?>><?php echo $post->post_title; ?></option>
						<?php
						}
						?>
					</select>					
				</div>
			<?php
			}
			?>
		</div>
		<?php if (count($product_post_array) > 0) {
		?>
			<script>
				<?php foreach ($product_post_array as $select) { ?>
						(function($) {
							"use strict";

							$(document).ready(function() {
								$(`#<?php echo $select; ?>`).multiselect({
									nonSelectedText: "Select Framework",
									enableFiltering: true,
									enableCaseInsensitiveFiltering: true,
									buttonWidth: "400px",
								});
							});
						})(jQuery);
				<?php } ?>
			</script>
<?php
		}
	}

	public function getProductFromId($id)
	{
		if (!isset($this->products)) {
			$args = array(
				'post_type'      => 'product',
				'numberposts' => -1
			);
			$this->products = get_posts($args);
		}

		foreach ($this->products as $product) {
			if ($product->ID == $id) {
				return $product->post_title;
			}
		}

		return "";
	}
}
