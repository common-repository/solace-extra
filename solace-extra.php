<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://solacewp.com
 * @since             1.1.6
 * @package           Solace_Extra
 *
 * Plugin Name:       Solace Extra
 * Plugin URI:        https://solacewp.com/solace-extra
 * Description:       Additional features for Solace Theme
 * Version:           1.1.8
 * Requires PHP:      7.4
 * Author:            Solace
 * Author URI:        https://solacewp.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       solace-extra
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

defined( 'ABSPATH' ) || exit;

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'SOLACE_EXTRA_VERSION', '1.1.6' );
define( 'SOLACE_EXTRA_DIR', plugin_dir_url( __FILE__ ) . '/' );
define( 'SOLACE_EXTRA_ASSETS_URL', plugin_dir_url( __FILE__ ) . 'assets/' );

global $solace_is_run_in_shortcode;

// set_time_limit(0);

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-solace-extra-activator.php
 */
function solace_extra_activate() {
    add_option( 'solace_extra_redirect_after_activation_option', true );
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-solace-extra-activator.php';
	Solace_Extra_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-solace-extra-deactivator.php
 */
function solace_extra_deactivate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-solace-extra-deactivator.php';
	Solace_Extra_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'solace_extra_activate' );
register_deactivation_hook( __FILE__, 'solace_extra_deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-solace-extra.php';

if (class_exists('Elementor\Plugin')) {
    require plugin_dir_path( __FILE__ ) . 'elementor/widgets/elementor.php';
}
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function solace_extra_run() {
    $plugin = new Solace_Extra();
	$plugin->run();

}
solace_extra_run();

add_action('init', 'solace_extra_handle_logo_upload');

function solace_extra_handle_logo_upload() {
    $method = ! empty( $_SERVER['REQUEST_METHOD'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) : '';
    if ( $method === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload_logo_image') {

        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'] ) ), 'ajax-nonce' )) {
            $response = array('error' => 'Invalid nonce!');
            echo wp_json_encode($response);
            wp_die();
        }

        $image_url = ! empty( $_POST['logo_image_url'] ) ? esc_url_raw( wp_unslash( $_POST['logo_image_url'] ) ) : '';

        // Use wp_remote_get() instead of file_get_contents()
        $response = wp_remote_get($image_url);

        if (is_wp_error($response)) {
            // Handle request error
            wp_die(esc_html('Logo download failed. Error: ' . $response->get_error_message()));
        }

        $body = wp_remote_retrieve_body($response);

        // Upload the image to media library
        $upload = wp_upload_bits(basename($image_url), null, $body);

        if (!$upload['error']) {
            // Attach the image to the media library
            $attachment = array(
                'post_mime_type' => wp_remote_retrieve_header($response, 'content-type'), // Use content-type from response
                'post_title' => sanitize_file_name(basename($image_url)),
                'post_content' => '',
                'post_status' => 'inherit'
            );

            $attach_id = wp_insert_attachment($attachment, $upload['file']);

            require_once ABSPATH . 'wp-admin/includes/image.php';
            $attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
            wp_update_attachment_metadata($attach_id, $attach_data);

            // Set the logo setting in Customizer
            set_theme_mod('custom_logo', $attach_id);

            // Redirect back to the page where the form was submitted
            $referer = ! empty( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';
            wp_redirect( $referer );
            exit();
        } else {
            // Handle upload error
            wp_die(esc_html('Logo upload failed. Error: ' . $upload['error']));
        }
    }
}
add_action('wp_ajax_solace_extra_upload_logo', 'solace_extra_upload_logo'); 
add_action('wp_ajax_nopriv_solace_extra_upload_logo', 'solace_extra_upload_logo');  

function solace_extra_install_plugin_from_url($plugin_url) {
    include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php'; 
    include_once ABSPATH . 'wp-admin/includes/plugin-install.php'; 
    include_once ABSPATH . 'wp-admin/includes/plugin.php'; 
    include_once ABSPATH . 'wp-admin/includes/file.php'; 

    $temporary_file = download_url($plugin_url); 

    if (is_wp_error($temporary_file)) {
        echo esc_html( 'Error: ' . $temporary_file->get_error_message() );
        return;
    }

    $plugin_upgrader = new Plugin_Upgrader(); 
    $installation = $plugin_upgrader->install($temporary_file); 

    wp_delete_file($temporary_file); 

    if (is_wp_error($installation)) {
        echo esc_html( 'Installation failed: ' . $temporary_file->get_error_message() );
    } else {
        esc_html_e( 'Installation successful!', 'solace-extra' );
    }
}


// solace_extra_install_plugin_from_url('http://solacewp.com/download/solace-extra.zip');

function solace_extra_install_plugin_from_wp_repo($plugin_slug) {
    include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    include_once ABSPATH . 'wp-admin/includes/plugin-install.php';

    $api = plugins_api('plugin_information', array('slug' => $plugin_slug, 'fields' => array('sections' => false)));
    if (is_wp_error($api)) {
        echo esc_html( 'Error: ' . $api->get_error_message() );
        return;
    }

    $upgrader = new Plugin_Upgrader();
    $result = $upgrader->install($api->download_link);

    if (is_wp_error($result)) {
        echo esc_html( 'Installation failed: ' . $result->get_error_message() );
    } else {
        esc_html_e( 'Installation successful!', 'solace-extra' );
    }
}

// solace_extra_install_plugin_from_wp_repo('elementor'); 

if ( ! function_exists( 'solace_is_run_in_shortcode' ) ) {

	/**
	 * solace_is_run_in_shortcode - Returns true when posts run in from shortcode.
	 *
	 * @return bool
	 */
	function solace_is_run_in_shortcode() {
        global $solace_is_run_in_shortcode;
        
		return $solace_is_run_in_shortcode;
	}
}