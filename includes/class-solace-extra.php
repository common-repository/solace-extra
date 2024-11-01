<?php
defined( 'ABSPATH' ) || exit;

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://solacewp.com
 * @since      1.0.0
 *
 * @package    Solace_Extra
 * @subpackage Solace_Extra/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Solace_Extra
 * @subpackage Solace_Extra/includes
 * @author     Solace <solacewp@gmail.com>
 */
class Solace_Extra {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Solace_Extra_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'SOLACE_EXTRA_VERSION' ) ) {
			$this->version = SOLACE_EXTRA_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'solace-extra';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_admin_starter_templates_hooks();
		$this->define_admin_import_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Solace_Extra_Loader. Orchestrates the hooks of the plugin.
	 * - Solace_Extra_i18n. Defines internationalization functionality.
	 * - Solace_Extra_Admin. Defines all hooks for the admin area.
	 * - Solace_Extra_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-solace-extra-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-solace-extra-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-solace-extra-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area submenu starter.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/starter-templates.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area submenu starter.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/import.php';		

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-solace-extra-public.php';

		$this->loader = new Solace_Extra_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Solace_Extra_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Solace_Extra_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Solace_Extra_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_admin, 'add_memory_limit_to_wp_config' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'solace_register_theme_parentmenu' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'solace_register_theme_submenu_dashboard' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'solace_register_theme_submenu_dashboard_sitebuilder' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'solace_register_theme_submenu_dashboard_step1' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'solace_register_theme_submenu_dashboard_step2' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'solace_register_theme_submenu_dashboard_step5' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'solace_register_theme_submenu_dashboard_step6' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'solace_register_theme_submenu_dashboard_congratulations' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'solace_register_theme_submenu_dashboard_starter_templates' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'solace_register_theme_submenu_dashboard_progress' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'solace_register_theme_parentmenu' );

		$this->loader->add_action( 'wp_ajax_update_logo2', $plugin_admin, 'update_logo_url_callback');
		$this->loader->add_action( 'wp_ajax_nopriv_update_logo2', $plugin_admin, 'update_logo_url_callback'); 

		$this->loader->add_action( 'admin_init', $plugin_admin, 'hide_notifications_for_solace_page', 999 );
		
		$this->loader->add_action( 'wp_ajax_update_sol_color_base_font_elementor_system_color', $plugin_admin, 'update_sol_color_base_font_elementor_system_color');
		$this->loader->add_action( 'wp_ajax_nopriv_update_sol_color_base_font_elementor_system_color', $plugin_admin, 'update_sol_color_base_font_elementor_system_color'); 
		$this->loader->add_action( 'wp_ajax_update_solace_font_and_color', $plugin_admin, 'update_solace_font_and_color');
		$this->loader->add_action( 'wp_ajax_nopriv_update_solace_font_and_color', $plugin_admin, 'update_solace_font_and_color'); 

		// Remove cookies
		$this->loader->add_action( 'wp_ajax_remove-cookie-continue-page-access', $plugin_admin, 'remove_cookies_continue_page_access');
		$this->loader->add_action( 'wp_ajax_nopriv_remove-cookie-continue-page-access', $plugin_admin, 'remove_cookies_continue_page_access'); 

		// Redirects user after plugin solace extra activation if option is set.
		$this->loader->add_action( 'admin_init', $plugin_admin, 'activation_redirect', 999999 );
		
		// Handles the theme switch redirection.
		$this->loader->add_action( 'switch_theme', $plugin_admin, 'switch_theme_redirect', 9999, 3 );

	}

	/**
	 * Register all of the hooks related to the admin area submenu starter templates functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_starter_templates_hooks() {

		$plugin_admin = new Solace_Extra_Starter_Templates( $this->get_plugin_name(), $this->get_version() );

		// Enqueue dash-icon
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_admin_dashicons' );

		// Ajax Search
		$this->loader->add_action( 'wp_ajax_action_ajax_search', $plugin_admin, 'action_ajax_search_server' );
		$this->loader->add_action( 'wp_ajax_nopriv_action_ajax_search', $plugin_admin, 'action_ajax_search_server' );

		// Ajax Checkbox
		$this->loader->add_action( 'wp_ajax_action_ajax_checkbox', $plugin_admin, 'action_ajax_checkbox' );
		$this->loader->add_action( 'wp_ajax_nopriv_action_ajax_checkbox', $plugin_admin, 'action_ajax_checkbox' );

		// Ajax Load More
		$this->loader->add_action( 'wp_ajax_action_load_more', $plugin_admin, 'call_ajax_load_more' );
		$this->loader->add_action( 'wp_ajax_nopriv_action_load_more', $plugin_admin, 'call_ajax_load_more' );		

		// Add cookie page access.
		$this->loader->add_action( 'wp_ajax_continue-page-access', $plugin_admin, 'add_cookie_page_access' );
		$this->loader->add_action( 'wp_ajax_nopriv_continue-page-access', $plugin_admin, 'add_cookie_page_access' );

	}

	/**
	 * Register all of the hooks related to the admin area submenu starter templates functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_import_hooks() {

		$plugin_admin = new Solace_Extra_Import( $this->get_plugin_name(), $this->get_version() );

		// Import Customizer
		$this->loader->add_action( 'wp_ajax_action-import-customizer', $plugin_admin, 'call_ajax_import_customizer' );
		$this->loader->add_action( 'wp_ajax_nopriv_action-import-customizer', $plugin_admin, 'call_ajax_import_customizer' );		

		// Import Widgets
		$this->loader->add_action( 'wp_ajax_action-import-widgets', $plugin_admin, 'call_ajax_import_widget' );
		$this->loader->add_action( 'wp_ajax_nopriv_action-import-widgets', $plugin_admin, 'call_ajax_import_widget' );		

		// Delete Previously Import
		$this->loader->add_action( 'wp_ajax_action-delete-previously-imported', $plugin_admin, 'delete_previously_imported' );
		$this->loader->add_action( 'wp_ajax_nopriv_action-delete-previously-imported', $plugin_admin, 'delete_previously_imported' );		

		// Install Active Theme
		$this->loader->add_action( 'wp_ajax_action-install-activate-theme', $plugin_admin, 'install_and_activate_theme' );
		$this->loader->add_action( 'wp_ajax_nopriv_action-install-activate-theme', $plugin_admin, 'install_and_activate_theme' );		

		// Install Active Plugin
		$this->loader->add_action( 'wp_ajax_action-install-activate-plugin', $plugin_admin, 'install_and_activate_plugins' );
		$this->loader->add_action( 'wp_ajax_nopriv_action-install-activate-plugin', $plugin_admin, 'install_and_activate_plugins' );		

		// Import Zip Elementor
		$this->loader->add_action( 'wp_ajax_action-import-zip', $plugin_admin, 'import_zip' );
		$this->loader->add_action( 'wp_ajax_nopriv_action-import-zip', $plugin_admin, 'import_zip' );		

		// Import Menu
		$this->loader->add_action( 'wp_ajax_action_import_menu', $plugin_admin, 'call_ajax_import_menu');
		$this->loader->add_action( 'wp_ajax_nopriv_action_import_menu', $plugin_admin, 'call_ajax_import_menu');   

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new Solace_Extra_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'solace_render_customizer_social_share', $plugin_public, 'solace_render_customizer_social_share' );
		$this->loader->add_shortcode( 'solace_year', $plugin_public, 'solace_year_shortcode' );
		$this->loader->add_shortcode( 'solace_recent_posts', $plugin_public, 'solace_recent_posts_shortcode' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'add_color_style_soalceform' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Solace_Extra_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
