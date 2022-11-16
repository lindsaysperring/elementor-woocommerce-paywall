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
class Elementor_Woocommerce_Paywall_Admin {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles($hook_suffix) {
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/elementor-woocommerce-paywall-admin.css', array(), $this->version, 'all' );
		wp_register_style( 'bootstrap-multiselect', 'https://phpcoder.tech/multiselect/css/jquery.multiselect.css');
		wp_enqueue_style( 'bootstrap-multiselect');
		wp_register_style( 'elementor-paywall-bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');				
		wp_enqueue_style( 'elementor-paywall-bootstrap');

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts($hook_suffix) {
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/elementor-woocommerce-paywall-admin.js', array( 'jquery' ), $this->version, false );
		wp_register_script( 'bootstrap-multiselect', 'https://phpcoder.tech/multiselect/js/jquery.multiselect.js');
		wp_enqueue_script( 'bootstrap-multiselect');
		wp_register_script( 'elementor-paywall-bootstrap', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.2/js/bootstrap.bundle.min.js');
		wp_enqueue_script( 'elementor-paywall-bootstrap');

	}

	public function admin_menu() {
		add_menu_page( "Elementor Paywall Settings", "Elementor Paywall", "manage_options", $this->slug, array($this, "admin_menu_page") );
	}

	public function admin_menu_page() {
		?>		
		<div class="wrap">
			<h2>Elementor Paywall Settings</h2>
			<p></p>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( $this->slug );
					do_settings_sections( $this->slug );
					submit_button();
				?>
			</form>
		</div>
		<?php
	}

	public function elementor_paywall_settings_page_init() {
		register_setting( $this->slug, 'ep_settings' );

		add_settings_section(
			'elementor-paywall-settings-section', 
			'Paywall Settings', 
			'', 
			$this->slug
		);
	
		add_settings_field( 
			'ep_post_type_select', 
			'Select Post Type where paywall applies', 
			array($this, "post_type_select"), 
			$this->slug, 
			'elementor-paywall-settings-section' 
		);
	}

	public function post_type_select() {
		$args = array(
			'public'   => true,
			'_builtin' => false
		 );
		$post_types = get_post_types( $args, "names", "or" );
		$options = get_option( 'ep_settings' );
		
		?>
			<select name="ep_settings[post_type][]" multiple class="select" id="post_type_multiselect">
				<?php
				foreach ($post_types as $key => $value) {
					$selected = in_array($key, $options['post_type']) ? 'selected' : '';
					?>
					<option value="<?php echo $key ?>" <?php echo $selected; ?>><?php echo $value;?></option>
					<?php
				}
				?>
			</select>
		<?php
	}

	public function post_product_select() {
		if (!isset($settings)) {
			$settings = get_option( 'ep_settings' );
		}
		$posts = [];
		foreach($settings as $key => $value) {
			$tempPosts = get_posts( ['post_type' => $value, 'numberposts' => -1] ;)
			$posts = array_merge($posts, $tempPosts);
		}
		?>
		<div id="post-product-select">

		</div>
		<select>
			<?php
			foreach ($posts as $key => $value){
				?>
				<option value="<?php echo $value-> ?>"></option>
				<?php
			}
			?>
		</select>
		<?php
	}

}
