<?php
defined( 'ABSPATH' ) || exit;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://solacewp.com
 * @since      1.0.0
 *
 * @package    Solace_Extra
 * @subpackage Solace_Extra/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Solace_Extra
 * @subpackage Solace_Extra/admin
 * @author     Solace <solacewp@gmail.com>
 */

 
class Solace_Extra_Admin {

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
	public function enqueue_styles($hook) {

		if ( is_customize_preview() ) {
			return;
		}

		// Global.
		wp_enqueue_style( 'solace-extra-disable-menu', plugin_dir_url( __FILE__ ) . 'css/disable-menu.min.css', array(), $this->version, 'all' );

		if ( 
			'toplevel_page_solace' === $hook ||
			'solace_page_dashboard' === $hook ||
			'solace_page_dashboard-video' === $hook ||
			'solace_page_dashboard-sitebuilder' === $hook ||
			'solace_page_dashboard-type' === $hook ||
			'solace_page_dashboard-starter-templates' === $hook ||
			'solace_page_dashboard-step5' === $hook ||
			'solace_page_dashboard-step6' === $hook ||
			'solace_page_dashboard-progress' === $hook ||
			'solace_page_dashboard-congratulations' === $hook ||
			'appearance_page_tgmpa-install-plugins' === $hook
		) {
			wp_enqueue_style( 'solace-extra-admin-style', plugin_dir_url( __FILE__ ) . 'css/admin-style.min.css', array(), $this->version, 'all' );
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts($hook) {

		wp_enqueue_media();


		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/solace-extra-admin.js', array( 'jquery' ), $this->version, true );

		wp_localize_script( $this->plugin_name, 'ajax_object', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce'    => wp_create_nonce('ajax-nonce')
		));

		$site_url = site_url();
		wp_localize_script( $this->plugin_name, 'step5', array(
			'site_url' => $site_url
		));
	
		$plugin_url = plugins_url();
		$plugin_dir_url = plugins_url();
		$admin_url = admin_url();
		wp_localize_script( $this->plugin_name, 'pluginUrl', array(
			'plugins_url'    => $plugin_url,
			'plugin_dir_url' => $plugin_dir_url,
			'admin_url' => $admin_url,
		));
		$timestamp = time();

		// Only page progress
		if ( $hook === 'solace_page_dashboard-progress' ) {
			wp_enqueue_script( 'solace-extra-import', plugin_dir_url( __FILE__ ) . 'js/import.js', array( 'jquery' ), $timestamp, true );
		}

		// Only page starter templates
		if ( $hook === 'solace_page_dashboard-starter-templates' ) {
			// Starter Templates
			wp_enqueue_script( 'solace-extra-starter-templates', plugin_dir_url( __FILE__ ) . 'js/starter-templates.js', array( 'jquery' ), '1.0.0', true );
		}		

		// Only page preview
		if ( $hook === 'solace_page_dashboard-step5' ) {
			// preview
			wp_enqueue_script( 'solace-extra-preview', plugin_dir_url( __FILE__ ) . 'js/preview.js', array( 'jquery' ), '1.0.0', true );

			// Sweetalert
			wp_enqueue_script( 'solace-extra-sweetalert', plugin_dir_url( __FILE__ ) . 'js/sweetalert.min.js', array(), '1.0.0', true );

			// Register dan enqueue script JavaScript untuk postMessage
			wp_enqueue_script('solace-iframe-loader', plugin_dir_url( __FILE__ ) . 'js/solace-iframe-loader.js', array( 'jquery' ), $this->version, true );

			// Localize preview
			wp_localize_script( 'solace-extra-preview', 'required_plugin', array(
				'plugins'    => Solace_Extra_Admin::get_required_plugin()
			));			
		}

		// Only page step6
		if ( 'solace_page_dashboard-step6' === $hook ) {
			// Form email
			wp_enqueue_script( 'solace-extra-form-email', plugin_dir_url( __FILE__ ) . 'js/form-email.js', array( 'jquery' ), $this->version, true );			

			// Sweetalert
			wp_enqueue_script( 'solace-extra-form-email-sweetalert', plugin_dir_url( __FILE__ ) . 'js/sweetalert.min.js', array(), $this->version, true );
		}			

	}

	/**
	 * Adds a memory limit to the wp-config.php file.
	 *
	 * This function checks if the wp-config.php file is writable and if the WP_MEMORY_LIMIT constant is not already defined.
	 * If both conditions are met, it adds the WP_MEMORY_LIMIT and WP_MAX_MEMORY_LIMIT constants with a value of '768' to the file.
	 *
	 * @return void
	 */
	function add_memory_limit_to_wp_config() {
		global $wp_filesystem;

		// Initialize the WP_Filesystem
		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		WP_Filesystem();

		$wp_config_file = ABSPATH . 'wp-config.php';

		// Check if wp-config.php exists and is writable
		if ( $wp_filesystem->is_writable( $wp_config_file ) ) {
			// Get the content of wp-config.php
			$config_content = $wp_filesystem->get_contents( $wp_config_file );

			// Check if the WP_MEMORY_LIMIT is already defined
			if ( strpos( $config_content, "define('WP_MEMORY_LIMIT'" ) === false ) {
				// Modify the content
				$config_content = str_replace(
					"/* That's all, stop editing! Happy publishing. */",
					"define('WP_MEMORY_LIMIT', '768M');\ndefine('WP_MAX_MEMORY_LIMIT', '768M');\n\n/* That's all, stop editing! Happy publishing. */",
					$config_content
				);

				// Write the updated content back to wp-config.php
				$wp_filesystem->put_contents( $wp_config_file, $config_content, FS_CHMOD_FILE );
			}
		}
	}

	/**
	 * Register Solace page for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function solace_register_theme_parentmenu() {

		add_menu_page(
			__( 'Solace', 'solace-extra' ),
			__( 'Solace', 'solace-extra' ),
			'manage_options',
			'solace',
			array( $this, 'solace_template_theme_submenu_dashboard' ),
			'data:image/svg+xml;base64,' . base64_encode('<svg width="55" height="69" viewBox="0 0 55 69" fill="none" xmlns="http://www.w3.org/2000/svg">
<path fill-rule="evenodd" clip-rule="evenodd" d="M20.8883 30.9094V39.3014L28.1559 43.4973V35.1053L20.8883 30.9094ZM18.7759 29.2657C18.7656 29.2058 18.7604 29.1441 18.7602 29.0807C18.7601 28.8849 18.8098 28.7032 18.9026 28.547C18.9914 28.3861 19.1249 28.2506 19.296 28.1518C19.3513 28.1199 19.4078 28.0933 19.4653 28.0721L28.6929 22.7445L37.5361 17.6388L37.5362 9.24695L19.9744 19.3862L11.5083 24.2741V43.4973L16.7825 40.4521L18.7759 39.3014V29.2657ZM31.3247 23.6643L38.9592 28.0721C39.0167 28.0934 39.0733 28.12 39.1286 28.1519C39.2987 28.2503 39.4314 28.3847 39.5201 28.5443L39.5208 28.5453C39.6142 28.7021 39.6643 28.8843 39.6641 29.0807C39.6641 29.1439 39.6588 29.2058 39.6486 29.2657V50.7423V50.7471L39.6485 50.756C39.6467 51.1358 39.4584 51.4612 39.1298 51.6516L11.1408 67.8112C11.0935 67.8504 11.042 67.8863 10.9864 67.9183C10.8175 68.0155 10.636 68.0629 10.4549 68.0607C10.2711 68.0641 10.0866 68.0163 9.91517 67.917C9.86017 67.8853 9.80925 67.8498 9.7624 67.8109L0.540822 62.4868C0.205562 62.2957 0.013635 61.9637 0.0153829 61.5778L0.0154995 50.9279C0.00536126 50.8683 0.000117352 50.8067 8.20725e-07 50.7438C-0.000232242 50.5482 0.049177 50.3667 0.141819 50.2104C0.230616 50.0492 0.364161 49.9133 0.535811 49.8142C0.591047 49.7823 0.647681 49.7557 0.705131 49.7345L8.33944 45.3269L0.540356 40.824C0.205445 40.6329 0.0137515 40.3011 0.0154995 39.9154V18.4338C0.00536126 18.3738 0.000117352 18.312 0.000117352 18.2487C0.000117352 18.0544 0.049177 17.8739 0.140887 17.7184C0.229684 17.5566 0.363578 17.42 0.535695 17.3206C0.591163 17.2885 0.647914 17.262 0.705597 17.2406L28.6902 1.08371C29.0205 0.892246 29.399 0.891314 29.73 1.08126L38.9596 6.40988C39.017 6.43109 39.0735 6.45766 39.1286 6.48959C39.2993 6.58817 39.4324 6.72323 39.5211 6.88346C39.6143 7.03985 39.6641 7.22152 39.6641 7.41753C39.6641 7.48092 39.6588 7.54291 39.6487 7.60304L39.6486 18.2486V18.2534V18.2624C39.6468 18.6423 39.4585 18.9675 39.1299 19.158L31.3247 23.6643ZM37.5361 30.9094L30.2685 35.1053V45.1412C30.2788 45.2016 30.284 45.2637 30.284 45.3273C30.2839 45.524 30.2335 45.7063 30.1397 45.8629L30.1393 45.8638C30.0501 46.0233 29.9172 46.1577 29.7468 46.2557C29.6921 46.2871 29.6363 46.3134 29.5796 46.3344L16.7382 53.7483L11.5082 56.7679V65.1598L37.5361 50.1325V30.9094ZM36.4799 29.08L29.2123 24.8839L25.6056 26.9663L21.9446 29.08L29.2123 33.2759L36.4799 29.08ZM27.0997 45.3267L19.8321 41.1308L11.1412 46.1486C11.094 46.1877 11.0428 46.2233 10.9876 46.2551C10.9324 46.2871 10.8759 46.3135 10.8184 46.3349L3.18422 50.7424L4.26492 51.3663L10.4519 54.9383L27.0997 45.3267ZM9.39568 65.1598V56.7678L2.12809 52.5718V60.9638L9.39568 65.1598ZM9.39568 43.4973V24.2741L2.12809 20.0782V39.3014L9.39568 43.4973ZM3.18433 18.2487L10.4519 22.4447L14.7702 19.9516L36.48 7.41753L29.2123 3.22159L3.18433 18.2487Z" fill="#FF8C00"/>
</svg>')
		);
	}

	/**
	 * Register page dashboard for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function solace_register_theme_submenu_dashboard() {

		add_submenu_page(
			'solace',
			__( 'Dashboard', 'solace-extra' ),
			__( 'Dashboard', 'solace-extra' ),
			'manage_options',
			'dashboard',
			array( $this, 'solace_template_theme_submenu_dashboard' )
		);

	}

	/**
	 * Register page sitebuilder for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function solace_register_theme_submenu_dashboard_sitebuilder() {

		add_submenu_page(
			'solace',
			__( 'Site Builder', 'solace-extra' ),
			__( 'Site Builder', 'solace-extra' ),
			'manage_options',
			'dashboard-sitebuilder',
			array( $this, 'solace_template_theme_submenu_dashboard_sitebuilder' )
		);

	}

	/**
	 * Register page dashboard video for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function solace_register_theme_submenu_dashboard_step1() {

		add_submenu_page(
			'solace',
			__( 'Dashboard Video', 'solace-extra' ),
			__( 'Dashboard Video', 'solace-extra' ),
			'manage_options',
			'dashboard-video',
			array( $this, 'solace_template_theme_submenu_dashboard_step1' )
		);

	}

	/**
	 * Register page dashboard type for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function solace_register_theme_submenu_dashboard_step2() {

		add_submenu_page(
			'solace',
			__( 'Dashboard Type', 'solace-extra' ),
			__( 'Dashboard Type', 'solace-extra' ),
			'manage_options',
			'dashboard-type',
			array( $this, 'solace_template_theme_submenu_dashboard_step2' )
		);

	}

	/**
	 * Register page dashboard step 5 for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function solace_register_theme_submenu_dashboard_step5() {

		add_submenu_page(
			'solace',
			__( 'Solace Step 5', 'solace-extra' ),
			__( 'Solace Step 5', 'solace-extra' ),
			'manage_options',
			'dashboard-step5',
			array( $this, 'solace_template_theme_submenu_dashboard_step5' )
		);

	}

	/**
	 * Register page dashboard step 6 for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function solace_register_theme_submenu_dashboard_step6() {

		add_submenu_page(
			'solace',
			__( 'Solace Step 6', 'solace-extra' ),
			__( 'Solace Step 6', 'solace-extra' ),
			'manage_options',
			'dashboard-step6',
			array( $this, 'solace_template_theme_submenu_dashboard_step6' )
		);

	}

	/**
	 * Register page dashboard congratulations for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function solace_register_theme_submenu_dashboard_congratulations() {

		add_submenu_page(
			'solace',
			__( 'Congratulations', 'solace-extra' ),
			__( 'Congratulations', 'solace-extra' ),
			'manage_options',
			'dashboard-congratulations',
			array( $this, 'solace_template_theme_submenu_dashboard_congratulations' )
		);

	}

	/**
	 * Register page dashboard starter templates for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function solace_register_theme_submenu_dashboard_starter_templates() {

		add_submenu_page(
			'solace',
			__( 'Dashboard Starter Templates', 'solace-extra' ),
			__( 'Dashboard Starter Templates', 'solace-extra' ),
			'manage_options',
			'dashboard-starter-templates',
			array( $this, 'solace_template_theme_submenu_dashboard_starter_templates' )
		);

	}

	/**
	 * Register page dashboard progress for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function solace_register_theme_submenu_dashboard_progress() {

		add_submenu_page(
			'solace',
			__( 'Dashboard Progress', 'solace-extra' ),
			__( 'Dashboard Progress', 'solace-extra' ),
			'manage_options',
			'dashboard-progress',
			array( $this, 'solace_template_theme_submenu_dashboard_progress' )
		);

	}	

	/**
	 * Template page dashboard for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function solace_template_theme_submenu_dashboard() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/submenu-dashboard.php';
    
	}

	/**
	 * Template page dashboard for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function solace_template_theme_submenu_dashboard_sitebuilder() {
		add_action('admin_enqueue_scripts', function() {
			wp_enqueue_script('jquery');
		});
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/submenu-dashboardsitebuilder.php';
    
	}

	/**
	 * Template page dashboard for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function solace_template_theme_submenu_dashboard_step1() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/submenu-video.php';
    
	}

	/**
	 * Template page dashboard for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function solace_template_theme_submenu_dashboard_step2() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/submenu-type.php';
    
	}	

	/**
	 * Template page dashboard for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function solace_template_theme_submenu_dashboard_step5() {
        add_action('admin_enqueue_scripts', function() {
			wp_enqueue_script('jquery');
		});
		// Source code can be found at readme.txt and solace-extra-admin/js/src/index.js
		wp_enqueue_script('dotlottie-player-component', plugin_dir_url(__FILE__) . 'js/dotlottie-player.js', array(), $this->version, true);
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/submenu-dashboardstep5.php';
    
	}	

	/**
	 * Template page dashboard for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function solace_template_theme_submenu_dashboard_step6() {
		add_action('admin_enqueue_scripts', function() {
			wp_enqueue_script('jquery');
		});
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/submenu-dashboardstep6.php';
    
	}	

	/**
	 * Template page dashboard for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function solace_template_theme_submenu_dashboard_congratulations() {
		add_action('admin_enqueue_scripts', function() {
			wp_enqueue_script('jquery');
		});

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/submenu-dashboardcongratulations.php';
    
	}	

	/**
	 * Template page dashboard for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function solace_template_theme_submenu_dashboard_starter_templates() {
		// Source code can be found at readme.txt and solace-extra-admin/js/src/index.js
		wp_enqueue_script('dotlottie-player-component', plugin_dir_url(__FILE__) . 'js/dotlottie-player.js', array(), $this->version, true);
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/submenu-starter.php';
    
	}

	/**
	 * Template page dashboard for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function solace_template_theme_submenu_dashboard_progress() {
		// Source code can be found at readme.txt and solace-extra-admin/js/src/index.js
		wp_enqueue_script('dotlottie-player-component', plugin_dir_url(__FILE__) . 'js/dotlottie-player.js', array(), $this->version, true);
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/submenu-progress.php';
    
	}	

    /**
     * Remove notif
     */		
	public function hide_notifications_for_solace_page() {
		if ( is_admin() && get_admin_page_parent() === 'solace' ) {
			remove_all_actions('admin_notices');
		}
	}

	
	
	function update_sol_color_base_font_elementor_system_color(){
		// error_log('masuk fungsi update_sol_color_base_font_elementor_system_color');
		$solace_global_colors = get_theme_mod( 'solace_global_colors' );

		$border_color = strtoupper($solace_global_colors['palettes']['base']['colors']['sol-color-border']);
		if (empty($solace_global_colors['palettes']['base']['colors']['sol-color-bg-menu-dropdown'])){
			$solace_global_colors['palettes']['base']['colors']['sol-color-bg-menu-dropdown'] = $border_color;	
		}
		
		set_theme_mod( 'solace_global_colors' , $solace_global_colors );
		$active_palette = $solace_global_colors['activePalette'];

		$primary = strtoupper($solace_global_colors['palettes'][$active_palette]['colors']['sol-color-button-initial']);
		$secondary = strtoupper($solace_global_colors['palettes'][$active_palette]['colors']['sol-color-page-title-background']);
		$text = strtoupper($solace_global_colors['palettes'][$active_palette]['colors']['sol-color-base-font']);
		$border_color = strtoupper($solace_global_colors['palettes'][$active_palette]['colors']['sol-color-border']);
		$accent = strtoupper($solace_global_colors['palettes'][$active_palette]['colors']['sol-color-bg-menu-dropdown']);
		$accent = empty($accent)?$border_color:$accent;

		if (defined('ELEMENTOR_PATH') && class_exists('\Elementor\Plugin')) {
			$system_colors = array(
				array(
					'_id' => 'primary',
					'color' => isset($primary)?$primary:'',
				),
				array(
					'_id' => 'secondary',
					'color' => isset($secondary)?$secondary:'',
				),
				array(
					'_id' => 'text',
					'color' => isset($text)?$text:'',
				),
				array(
					'_id' => 'accent',
					'color' => isset($accent)?$accent:'',
				),
			);

			\Elementor\Plugin::$instance->kits_manager->update_kit_settings_based_on_option( 'system_colors', $system_colors );

			\Elementor\Plugin::$instance->files_manager->clear_cache();
		}
	}

	

	function update_solace_font_and_color(){
		// error_log('masuk fungsi: update_solace_font_and_color');

		// Verify nonce
		if (!isset($_POST['nonce']) || !wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'] ) ), 'ajax-nonce' )) {
			$response = array('error' => 'Invalid nonce!');
			// error_log (wp_json_encode($response));
			wp_die();
		}			
		// error_log('masuk fungsi: update_solace_font_and_color lolos nonce: ');
		// error_log('$_POST Body Font: '.$_POST['new_solace_body_font_family'] .'$_POST Heading Font: '.$_POST['new_solace_heading_font_family_general']);

		// =========== BEGIN GET NEW FONT AND COLOR ===========
		$new_solace_body_font_family = isset( $_POST['new_solace_body_font_family'] ) ? sanitize_text_field( wp_unslash( $_POST['new_solace_body_font_family'] ) ) : '';
		$new_solace_heading_font_family_general = isset( $_POST['new_solace_heading_font_family_general'] ) ? sanitize_text_field( wp_unslash( $_POST['new_solace_heading_font_family_general'] ) ) : '';
		$new_base_color = ! empty( $_POST['new_base_color'] ) ? sanitize_hex_color( wp_unslash( $_POST['new_base_color'] ) ) : '';
		$new_heading_color = ! empty( $_POST['new_heading_color'] ) ? sanitize_hex_color( wp_unslash( $_POST['new_heading_color'] ) ) : '';
		$new_link_button_color = ! empty( $_POST['new_link_button_color'] ) ? sanitize_hex_color( wp_unslash( $_POST['new_link_button_color'] ) ) : '';
		$new_link_button_hover_color = ! empty( $_POST['new_link_button_hover_color'] ) ? sanitize_hex_color( wp_unslash( $_POST['new_link_button_hover_color'] ) ) : '';
		$new_button_color = ! empty( $_POST['new_button_color'] ) ? sanitize_hex_color( wp_unslash( $_POST['new_button_color'] ) ) : '';
		$new_button_hover_color = ! empty( $_POST['new_button_hover_color'] ) ? sanitize_hex_color( wp_unslash( $_POST['new_button_hover_color'] ) ) : '';
		$new_text_selection_color = ! empty( $_POST['new_text_selection_color'] ) ? sanitize_hex_color( wp_unslash( $_POST['new_text_selection_color'] ) ) : '';
		$new_text_selection_hover_color = ! empty( $_POST['new_text_selection_bg_color'] ) ? sanitize_hex_color( wp_unslash( $_POST['new_text_selection_bg_color'] ) ) : '';
		$new_border_color = ! empty( $_POST['new_border_color'] ) ? sanitize_hex_color( wp_unslash( $_POST['new_border_color'] ) ) : '';
		$new_background_color = ! empty( $_POST['new_background_color'] ) ? sanitize_hex_color( wp_unslash( $_POST['new_background_color'] ) ) : '';
		$new_page_title_text_color = ! empty( $_POST['new_page_title_text_color'] ) ? sanitize_hex_color( wp_unslash( $_POST['new_page_title_text_color'] ) ) : '';
		$new_page_title_bg_color = ! empty( $_POST['new_page_title_bg_color'] ) ? sanitize_hex_color( wp_unslash( $_POST['new_page_title_bg_color'] ) ) : '';
		$new_bg_menu_dropdown_color = ! empty( $_POST['new_bg_menu_dropdown_color'] ) ? sanitize_hex_color( wp_unslash( $_POST['new_bg_menu_dropdown_color'] ) ) : '';
		// $new_bg_menu_dropdown_color = !empty($new_bg_menu_dropdown_color)?$new_bg_menu_dropdown_color:$new_border_color;
		// error_log('$new_bg_menu_dropdown_color:'.$new_bg_menu_dropdown_color);
		// error_log('$new_solace_body_font_family: '.$new_solace_body_font_family);
		// error_log('$new_button_color: '.$new_button_color);

		// =========== END OF NEW FONT AND COLOR ===========

		// =========== BEGIN GET CURRENT SETTING ELEMENTOR COLOR ==============
		$current = \Elementor\Plugin::$instance->kits_manager->get_current_settings();
		$base_color = strtoupper($current['solace_colors'][0]['color']);
		$heading_color = strtoupper($current['solace_colors'][1]['color']);
		$link_button_color = strtoupper($current['solace_colors'][2]['color']);
		$link_button_hover_color = strtoupper($current['solace_colors'][3]['color']);
		$button_color = strtoupper($current['solace_colors'][4]['color']);
		$button_hover_color = strtoupper($current['solace_colors'][5]['color']);
		$text_selection_color = strtoupper($current['solace_colors'][6]['color']);
		$text_selection_bg_color = strtoupper($current['solace_colors'][7]['color']);
		$border_color = strtoupper($current['solace_colors'][8]['color']);
		$background_color = strtoupper($current['solace_colors'][9]['color']);
		$page_title_text_color = strtoupper($current['solace_colors'][10]['color']);
		$page_title_bg_color = strtoupper($current['solace_colors'][11]['color']);
		$bg_menu_dropdown_color = strtoupper($current['solace_colors'][12]['color']);
		// $bg_menu_dropdown_color = empty($bg_menu_dropdown_color)?$border_color:$bg_menu_dropdown_color;
		// error_log('current button elememntor color: '.$button_color);

		// IF Current Elementor Color is empty, then set Default Solace Color
		$base_color = isset($base_color)?$base_color:'#000000';
		$heading_color = isset($heading_color)?$heading_color:'#1D70DB';
		$link_button_color = isset($link_button_color)?$link_button_color:'#1D70DB';
		$link_button_hover_color = isset($link_button_hover_color)?$link_button_hover_color:'#1D70DB';
		$button_color = isset($button_color)?$button_color:'#1D70DB';
		$button_hover_color = isset($button_hover_color)?$button_hover_color:'#1D70DB';
		$text_selection_color = isset($text_selection_color)?$text_selection_color:'#FF9500';
		$text_selection_bg_color = isset($text_selection_bg_color)?$text_selection_bg_color:'#FF9500';
		$border_color = isset($border_color)?$border_color:'#DEDEDE';
		$background_color = isset($background_color)?$background_color:'#EBEBEB';
		$page_title_text_color = isset($page_title_text_color)?$page_title_text_color:'#FFFFFF';
		$page_title_bg_color = isset($page_title_bg_color)?$page_title_bg_color:'#000F44';
		$bg_menu_dropdown_color = isset($bg_menu_dropdown_color)?$bg_menu_dropdown_color:'#DEDEDE';
		// =========== END OF GET CURRENT SETTING ELEMENTOR COLOR ==============
		
		// =========== BEGIN SAVE FONT AND COLOR TO CUSTOMIZER ==============
		set_theme_mod('solace_body_font_family', $new_solace_body_font_family);
		set_theme_mod('solace_smaller_font_family', $new_solace_body_font_family);
		set_theme_mod('solace_logotitle_font_family', $new_solace_body_font_family);
		set_theme_mod('solace_button_font_family', $new_solace_body_font_family);
		set_theme_mod('solace_h1_font_family_general',$new_solace_heading_font_family_general);
		set_theme_mod('solace_h2_font_family_general',$new_solace_heading_font_family_general);
		set_theme_mod('solace_h3_font_family_general',$new_solace_heading_font_family_general);
		set_theme_mod('solace_h4_font_family_general',$new_solace_heading_font_family_general);
		set_theme_mod('solace_h5_font_family_general',$new_solace_heading_font_family_general);
		set_theme_mod('solace_h6_font_family_general',$new_solace_heading_font_family_general);
		$solace_base_font	= get_theme_mod('solace_body_font_family','Manrope' );
		// error_log('get_theme_mod solace_body_font_family: '.$solace_base_font);

		$solace_global_colors['activePalette'] = 'base';
		$solace_global_colors['palettes']['base']['name'] = 'Base';
		$solace_global_colors['palettes']['base']['allowDeletion'] = '';
		$solace_global_colors['palettes']['base']['colors']['sol-color-base-font'] = isset($new_base_color)?strtoupper($new_base_color):$base_color;
		$solace_global_colors['palettes']['base']['colors']['sol-color-heading'] = isset($new_heading_color)?strtoupper($new_heading_color):$heading_color;
		$solace_global_colors['palettes']['base']['colors']['sol-color-link-button-initial'] = isset($new_link_button_color)?strtoupper($new_link_button_color):$link_button_color;
		$solace_global_colors['palettes']['base']['colors']['sol-color-link-button-hover'] = isset($new_link_button_hover_color)?strtoupper($new_link_button_hover_color):$link_button_hover_color;
		$solace_global_colors['palettes']['base']['colors']['sol-color-button-initial'] = isset($new_button_color)?strtoupper($new_button_color):$button_color;
		$solace_global_colors['palettes']['base']['colors']['sol-color-button-hover'] = isset($new_button_hover_color)?strtoupper($new_button_hover_color):$button_hover_color;
		$solace_global_colors['palettes']['base']['colors']['sol-color-selection-initial'] = isset($new_text_selection_color)?strtoupper($new_text_selection_color):$text_selection_color;
		$solace_global_colors['palettes']['base']['colors']['sol-color-selection-high'] = isset($new_text_selection_hover_color)?strtoupper($new_text_selection_hover_color):$text_selection_bg_color;
		$solace_global_colors['palettes']['base']['colors']['sol-color-border'] = isset($new_border_color)?strtoupper($new_border_color):$border_color;
		$solace_global_colors['palettes']['base']['colors']['sol-color-background'] = isset($new_background_color)?strtoupper($new_background_color):$background_color;
		$solace_global_colors['palettes']['base']['colors']['sol-color-page-title-text'] = isset($new_page_title_text_color)?strtoupper($new_page_title_text_color):$page_title_text_color;
		$solace_global_colors['palettes']['base']['colors']['sol-color-page-title-background'] = isset($new_page_title_bg_color)?strtoupper($new_page_title_bg_color):$page_title_bg_color;
		$solace_global_colors['palettes']['base']['colors']['sol-color-bg-menu-dropdown'] = isset($new_bg_menu_dropdown_color)?strtoupper($new_bg_menu_dropdown_color):$bg_menu_dropdown_color;
		set_theme_mod( 'solace_global_colors' , $solace_global_colors );
		$solace_global_colors = get_theme_mod( 'solace_global_colors' );
		// error_log('get_theme_mod solace_global_colors: '.$solace_global_colors);
		// =========== END OF SAVE FONT AND COLOR TO CUSTOMIZER ==============

		$attachment_id_cookie = isset( $_COOKIE['solace_step5_logoid'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['solace_step5_logoid'] ) ) : null;

		if (!empty($attachment_id_cookie)){
			// $logo_url = esc_url( esc_url_raw( $_POST['logo_url'] ) );
			$logo_id = $attachment_id_cookie;
			$theme_mods = get_theme_mod('theme_mods_solace');  
			$logo_data = json_decode($theme_mods['logo_logo'], true);  
			$logo_logo_data = '{"light":'.$logo_id.',"dark":'.$logo_id.',"same":true}';
			set_theme_mod('logo_logo',$logo_logo_data);
			set_theme_mod('logo-footer_logo', $logo_logo_data);

		}
		// SET LOGO



		// =========== BEGIN SAVE FONT AND COLOR TO ELEMENTOR ===========
		$custom_typography = [
			[
				'_id' => 'primary',
				'title' => 'Smaller',
				'typography_font_family' => $new_solace_body_font_family
			],
			[
				'_id' => 'secondary',
				'title' => 'Logo Title / Subtitle',
				'typography_font_family' => $new_solace_body_font_family
			],
			[
				'_id' => 'text',
				'title' => 'Solace Base',
				'typography_font_family' => $new_solace_body_font_family
			],
			[
				'_id' => 'accent',
				'title' => 'Button',
				'typography_font_family' => $new_solace_body_font_family
			],
			['
				_id' => 'solace_body_font_family',
				'typography_font_family' => $new_solace_body_font_family
			],
			[
				'typography_font_family' => $new_solace_heading_font_family_general
			],
			[
				'typography_font_family' => $new_solace_heading_font_family_general
			],
			[
				'typography_font_family' => $new_solace_heading_font_family_general
			],
			[
				'typography_font_family' => $new_solace_heading_font_family_general
			],
			[
				'typography_font_family' => $new_solace_heading_font_family_general
			],
			[
				'typography_font_family' => $new_solace_heading_font_family_general
			]
		];
		
		$system_colors = array(
			array(
				'_id' => 'primary',
				'color' => isset($new_button_color)?strtoupper($new_button_color):$button_color,
			),
			array(
				'_id' => 'secondary',
				'color' => isset($new_page_title_bg_color)?strtoupper($new_page_title_bg_color):$page_title_bg_color,
			),
			array(
				'_id' => 'text',
				'color' => isset($new_base_color)?strtoupper($new_base_color):$base_color,
			),
			array(
				'_id' => 'accent',
				'color' => isset($new_bg_menu_dropdown_color)?strtoupper($new_bg_menu_dropdown_color):$bg_menu_dropdown_color,
			),
		);
		
		$theme_colors = array(
			array(
				'_id' => 'sol-color-base-font',
				'title'  => __( 'Base Font', 'solace-extra' ),
				'color' => isset($new_base_color)?strtoupper($new_base_color):$base_color,
			),
			array(
				'_id' => 'sol-color-heading',
				'title'  => __( 'Heading', 'solace-extra' ),
				'color' => isset($new_heading_color)?strtoupper($new_heading_color):$heading_color,
				
			),
			array(
				'_id' => 'sol-color-link-button-initial',
				'title'  => __( 'Link', 'solace-extra' ),
				'color' => isset($new_link_button_color)?strtoupper($new_link_button_color):$link_button_color,
			),
			array(
				'_id' => 'sol-color-link-button-hover',
				'title'  => __( 'Link Hover', 'solace-extra' ),
				'color' => isset($new_link_button_hover_color)?strtoupper($new_link_button_hover_color):$link_button_hover_color,
			),
			array(
				'_id' => 'sol-color-button-initial',
				'title'  => __( 'Button', 'solace-extra' ),
				'color' => isset($new_button_color)?strtoupper($new_button_color):$button_color,
			),
			array(
				'_id' => 'sol-color-button-hover',
				'title'  => __( 'Button Hover', 'solace-extra' ),
				'color' => isset($new_button_hover_color)?strtoupper($new_button_hover_color):$button_hover_color,
			),
			array(
				'_id' => 'sol-color-selection',
				'title'  => __( 'Text Selection', 'solace-extra' ),
				'color' => isset($new_text_selection_color)?strtoupper($new_text_selection_color):$text_selection_color,
			),
			array(
				'_id' => 'sol-color-selection-high',
				'title'  => __( 'Text Selection Background', 'solace-extra' ),
				'color' => isset($new_text_selection_hover_color)?strtoupper($new_text_selection_hover_color):$text_selection_bg_color,
			),
			array(
				'_id' => 'sol-color-border',
				'title'  => __( 'Border', 'solace-extra' ),
				'color' => isset($new_border_color)?strtoupper($new_border_color):$border_color,
			),
			array(
				'_id' => 'sol-color-background',
				'title'  => __( 'Background', 'solace-extra' ),
				'color' => isset($new_background_color)?strtoupper($new_background_color):$background_color,
			),
			array(
				'_id' => 'sol-color-page-title-text',
				'title'  => __( 'Page Title', 'solace-extra' ),
				'color' => isset($new_page_title_text_color)?strtoupper($new_page_title_text_color):$page_title_text_color,
			),
			array(
				'_id' => 'sol-color-page-title-background',
				'title'  => __( 'Page Title Background', 'solace-extra' ),
				'color' => isset($new_page_title_bg_color)?strtoupper($new_page_title_bg_color):$page_title_bg_color,
			),
			array(
				'_id' => 'sol-color-bg-menu-dropdown',
				'title'  => __( 'Submenu Background', 'solace-extra' ),
				'color' => isset($new_bg_menu_dropdown_color)?strtoupper($new_bg_menu_dropdown_color):$bg_menu_dropdown_color,
			),
		);
		if (class_exists('Elementor\Plugin')) {
			\Elementor\Plugin::$instance->kits_manager->update_kit_settings_based_on_option( 'system_colors', $system_colors );
			\Elementor\Plugin::$instance->kits_manager->update_kit_settings_based_on_option( 'solace_colors', $theme_colors );
			\Elementor\Plugin::$instance->kits_manager->update_kit_settings_based_on_option( 'system_typography', $custom_typography );
			\Elementor\Plugin::$instance->files_manager->clear_cache();
		}
		// =========== END OF SAVE FONT AND COLOR TO ELEMENTOR ===========


		wp_send_json_success('Color and Font updated: '.$new_solace_body_font_family.' & '. $new_solace_heading_font_family_general.' successfully.');
	}


	function update_logo_url_callback() {
		if (isset($_POST['logo_url'])) {

			// Verify nonce
			if (!isset($_POST['nonce']) || !wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'] ) ), 'ajax-nonce' )) {
				$response = array('error' => 'Invalid nonce!');
				echo wp_json_encode($response);
				wp_die();
			}
			
			$logo_url = ! empty( $_POST['logo_url'] ) ? esc_url_raw( wp_unslash( $_POST['logo_url'] ) ) : '';

			$logo_id = $logo_url;
			$theme_mods = get_theme_mod('theme_mods_solace');  
			$logo_data = json_decode($theme_mods['logo_logo'], true);  
			$logo_logo_data = '{"light":'.$logo_id.',"dark":'.$logo_id.',"same":true}';
			set_theme_mod('logo_logo',$logo_logo_data);
			set_theme_mod('logo-footer_logo', $logo_logo_data);

			wp_send_json_success('Logo URL updated successfully.');
		} else {
			wp_send_json_error('No logo URL provided.');
		}
	}

    /**
     * Get required plugin.
     */
    public static function get_required_plugin()
    {

		// Initialize response data
		$data = array();

        // Verify nonce
        if (!isset($_GET['nonce']) || !wp_verify_nonce( sanitize_text_field( wp_unslash ( $_GET['nonce'] ) ), 'ajax-nonce' )) {
			$data['error'] = esc_html__( 'Invalid nonce!', 'solace-extra' );
			return $data;
        }		

        // Demo name
		$get_demo = ! empty( $_GET['demo'] ) ? sanitize_text_field( wp_unslash( $_GET['demo'] ) ) : '';
        if (empty($get_demo)) {
			$data['error'] = esc_html__( 'Error demo URL', 'solace-extra' );
			return $data;
        }

		// Remote and local API URLs
		$url = trailingslashit('https://solacewp.com/' . $get_demo) . 'wp-json/solace/v1/required-plugin';

        // Make remote request using wp_remote_get
        $response = wp_remote_get($url);

        // Check for errors
        if (is_wp_error($response)) {
			$data['error'] = esc_html__( 'Error response', 'solace-extra' );
			return $data;
        }

		// Decode the response body
		$body = wp_remote_retrieve_body($response);
		$decoded_data = json_decode($body, true);

		// Data checks
		if (!is_array($decoded_data)) {
			$data['error'] = 'Required plugin not found.';
			return $data;
		}

		return $decoded_data;		
    }	
	
    /**
     * Remove cookie
     */	
	function remove_cookies_continue_page_access() {

        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'] ) ), 'ajax-nonce' )) {
            $response = array('error' => 'Invalid nonce!');
			echo wp_json_encode($response);
            wp_die();
        }   
		
		// Page congratulations
		$mypage = ! empty( $_POST['mypage'] ) ? sanitize_text_field( wp_unslash( $_POST['mypage'] ) ) : '';
		if ( 'dashboard-congratulations' === $mypage || 'dashboard-step6' === $mypage ) {
			// Set cookie with expired time to delete the cookie
			setcookie( 'solace_page_access', '', time() - 3600 );
			setcookie( 'solace_step5_font', '', time() - 3600 );
			setcookie( 'solace_step5_color', '', time() - 3600 );
			setcookie( 'solace_step5_logoid', '', time() - 3600 );
			
		}

		wp_die();
	}

	/**
	 * Redirects user after theme/plugin activation if option is set.
	 *
	 * This function checks if the 'solace_extra_redirect_after_activation_option' is set to true.
	 * If true, it deletes the option and redirects the user to 'admin.php?page=solace'.
	 */
	public function activation_redirect() {
		// Check if the 'solace_extra_redirect_after_activation_option' is set to true
		if ( get_option( 'solace_extra_redirect_after_activation_option', false ) ) {
			// Delete the option to prevent redirection on subsequent activations
			delete_option( 'solace_extra_redirect_after_activation_option' );

			// Construct the URL
			$redirect_url = admin_url( 'admin.php?page=solace' );

			// Make a GET request to the URL and check the HTTP response code
			$response = wp_remote_get( $redirect_url );

			if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 ) {
				// If the response code is 200, redirect the user
				wp_redirect( esc_url( $redirect_url ) );
				exit;
			} else {
				// If there is an error or the response code is not 200, handle accordingly
				// You can log the error, display a message, or take other actions
				// error_log( 'Error accessing ' . $redirect_url );
				// Handle the error, for example, redirect to another page
				wp_redirect( esc_url( admin_url() ) );
				exit;
			}
		}
	}	

	/**
	 * Handles the theme switch redirection.
	 *
	 * This function is triggered when a theme switch occurs. It checks if the new theme is 'Solace' and the new theme name is also 'Solace'.
	 * If these conditions are met, it constructs a redirect URL to the 'Solace' admin page and makes a GET request to check the response code.
	 * If the response code is 200, it redirects the user to the 'Solace' admin page.
	 *
	 * @param string $new_name The new theme name.
	 * @param WP_Theme $new_theme The new theme object.
	 * @param WP_Theme $old_theme The old theme object.
	 */	
	public function switch_theme_redirect($new_name, $new_theme, $old_theme) {
	
		if ( 'Solace' === $new_theme->get('Name') && 'Solace' === $new_name ) {
			// Construct the URL
			$redirect_url = admin_url( 'admin.php?page=solace' );
	
			// Make a GET request to the URL and check the HTTP response code
			$response = wp_remote_get( $redirect_url );
	
			if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 ) {
				// If the response code is 200, redirect the user
				wp_redirect( esc_url( $redirect_url ) );
				exit;
			}
		}
	
	}
}
