<?php
defined( 'ABSPATH' ) || exit;

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://solacewp.com
 * @since      1.0.0
 *
 * @package    Solace_Extra
 * @subpackage Solace_Extra/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Solace_Extra
 * @subpackage Solace_Extra/public
 * @author     Solace <solacewp@gmail.com>
 */
class Solace_Extra_Public {

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
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		if ( is_singular() ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/solace-extra-shortcodes.css', array(), $this->version, 'all' );
		}

		if ( is_single() ) {
			wp_enqueue_style( $this->plugin_name . 'public', plugin_dir_url( __FILE__ ) . 'css/solace-extra-public.css', array(), $this->version, 'all' );
		}		
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		if ( is_single() ) {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/solace-extra-public.js', array( 'jquery' ), $this->version, true );
		}

	}

	/**
	 * Register the shortcode solace year for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function solace_year_shortcode() {

		// Get current year
		$current_year = gmdate('Y');

		return $current_year;

	}

	/**
	 * Register the shortcode solace blog posts.
	 *
	 * @since    1.0.0
	 */
	public function solace_recent_posts_shortcode( $atts , $content = null ) {
		global $solace_is_run_in_shortcode;

		$solace_is_run_in_shortcode = true;

		// Attributes
		$atts = shortcode_atts(
			array(
				'posts' => '5',
				'col' => '2',
			),
			$atts,
			'recent-posts'
		);

		// Query
		$the_query = new WP_Query( array ( 'posts_per_page' => $atts['posts'], 'paged' => 1, 'order' => 'DESC', 'orderby' => 'date' ) );
		
		// Posts
		$classes[] = sanitize_html_class( 'sol_recent_posts_shortcode' );
		
		$col = (int)$atts['col'];
		if ( ( $col >= 1 ) and ( $col <= 5 ) ) {
			$classes[] = sanitize_html_class( 'sol_col_' . $col );
		} else {
			$classes[] = sanitize_html_class( 'sol_col_2' );
		}

		$css_class = implode( ' ', $classes );

		$output = sprintf( '<div class="%s">', $css_class );
		ob_start();
		while ( $the_query->have_posts() ) :
			$the_query->the_post();
			get_template_part('template-parts/blog', get_post_format());
		endwhile;
		$output .= ob_get_contents();
		$output .= '</div>';
		ob_end_clean();
		
		// Reset post data
		wp_reset_postdata();

		$solace_is_run_in_shortcode = false;

		return $output;
	}

	/**
	 * Register the shortcode solace blog posts.
	 *
	 * @since    1.0.0
	 */
	// public function solace_recent_posts_shortcode( $atts , $content = null ) {
	// 	wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/solace-extra-shortcodes.css', array(), $this->version, 'all' );

	// 	// Attributes
	// 	$atts = shortcode_atts(
	// 		array(
	// 			'posts' => '5',
	// 			'col' => '2',
	// 		),
	// 		$atts,
	// 		'recent-posts'
	// 	);

	// 	// Query
	// 	$the_query = new WP_Query( array ( 'posts_per_page' => $atts['posts'] ) );
		
	// 	// Posts
	// 	$classes[] = sanitize_html_class( 'sol_recent_posts_shortcode' );
		
	// 	$col = (int)$atts['col'];
	// 	if ( ( $col >= 1 ) and ( $col <= 5 ) ) {
	// 		$classes[] = sanitize_html_class( 'sol_col_' . $col );
	// 	} else {
	// 		$classes[] = sanitize_html_class( 'sol_col_2' );
	// 	}

	// 	$css_class = implode( ' ', $classes );

	// 	$formatted_css_class = sprintf( '<div class="%s">', $css_class );
	// 	echo $formatted_css_class;
	// 	while ( $the_query->have_posts() ) :
	// 		$the_query->the_post();
	// 		get_template_part('template-parts/blog', get_post_format());
	// 	endwhile;
	// 	echo '</div>';
		
	// 	// Reset post data
	// 	wp_reset_postdata();
	// }

	/**
	 * Add custom CSS rule to set the text color of elements with class '.solaceform-form-button' to inherit.
	 * This function is intended to be used in the WordPress footer to apply the styles globally.
	 */
	public function add_color_style_soalceform() {
		// Define the custom CSS rule
		$style = ".solaceform-form-button { color: inherit !important; }";
		$style .= ".solaceform-form-button-wrap { color: #fff; }";

		// Add the custom CSS rule to the inline styles of the specified stylesheet
		wp_add_inline_style( $this->plugin_name, $style );
	}

	/**
	* Render social share on single post
	*
	* @since    1.0.0
	*/

	public function solace_render_customizer_social_share() {
		$default_social_share = array(
			'facebook',
			'instagram',
			'twitter',
			'copylink',
		);
		$get_social_share = get_theme_mod('solace_layout_single_post_social_order', wp_json_encode( $default_social_share ));
		if (!empty($get_social_share)){
			$array = json_decode($get_social_share, true);
			$title = get_the_title();
			$urlpost = get_permalink();
			$baseUrl = '';
			?>
			<div class='solace-social-share'>
				<div class="notif-clipboard msg animate slide-in-down"></div>
				<?php
				foreach ($array as $item) {
					if ( $item === "copylink" ) {
						?>
						<a href="#" id="copy-clipboard">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path d="M579.8 267.7c56.5-56.5 56.5-148 0-204.5c-50-50-128.8-56.5-186.3-15.4l-1.6 1.1c-14.4 10.3-17.7 30.3-7.4 44.6s30.3 17.7 44.6 7.4l1.6-1.1c32.1-22.9 76-19.3 103.8 8.6c31.5 31.5 31.5 82.5 0 114L422.3 334.8c-31.5 31.5-82.5 31.5-114 0c-27.9-27.9-31.5-71.8-8.6-103.8l1.1-1.6c10.3-14.4 6.9-34.4-7.4-44.6s-34.4-6.9-44.6 7.4l-1.1 1.6C206.5 251.2 213 330 263 380c56.5 56.5 148 56.5 204.5 0L579.8 267.7zM60.2 244.3c-56.5 56.5-56.5 148 0 204.5c50 50 128.8 56.5 186.3 15.4l1.6-1.1c14.4-10.3 17.7-30.3 7.4-44.6s-30.3-17.7-44.6-7.4l-1.6 1.1c-32.1 22.9-76 19.3-103.8-8.6C74 372 74 321 105.5 289.5L217.7 177.2c31.5-31.5 82.5-31.5 114 0c27.9 27.9 31.5 71.8 8.6 103.9l-1.1 1.6c-10.3 14.4-6.9 34.4 7.4 44.6s34.4 6.9 44.6-7.4l1.1-1.6C433.5 260.8 427 182 377 132c-56.5-56.5-148-56.5-204.5 0L60.2 244.3z"/></svg>
							<p><?php esc_html_e( 'Copy to Clipboard', 'solace-extra' ); ?></p>
						</a>
						<?php
					} else if ( $item === "facebook" ) {
						$link_share = "https://www.facebook.com/sharer.php?u=" . esc_url( get_the_permalink() );
						?>
						<a href="<?php echo esc_url( $link_share ); ?>" id="facebook">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64h98.2V334.2H109.4V256h52.8V222.3c0-87.1 39.4-127.5 125-127.5c16.2 0 44.2 3.2 55.7 6.4V172c-6-.6-16.5-1-29.6-1c-42 0-58.2 15.9-58.2 57.2V256h83.6l-14.4 78.2H255V480H384c35.3 0 64-28.7 64-64V96c0-35.3-28.7-64-64-64H64z"/></svg>
							<p><?php esc_html_e( 'Share on Facebook', 'solace-extra' ); ?></p>
						</a>
						<?php
					} else if ( $item === "twitter" ) {
						$baseUrl = "https://twitter.com/intent/tweet?text=" . urlencode($title) . "&url=" . urlencode($urlpost);
						$link_share = $baseUrl . '?u=' . urlencode($urlpost) . '&t=' . urlencode($title);
						?>
						<a href="<?php echo esc_url( $link_share ); ?>" id="twitter">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H384c35.3 0 64-28.7 64-64V96c0-35.3-28.7-64-64-64H64zm297.1 84L257.3 234.6 379.4 396H283.8L209 298.1 123.3 396H75.8l111-126.9L69.7 116h98l67.7 89.5L313.6 116h47.5zM323.3 367.6L153.4 142.9H125.1L296.9 367.6h26.3z"/></svg>
							<p><?php esc_html_e( 'Share on X', 'solace-extra' ); ?></p>
						</a>
						<?php						
					} else if ( $item === "instagram" ) {
						$urlpost = get_permalink();
						$title = get_the_title();
						$link_share = "https://www.instagram.com/share?text=" . urlencode($title . ' ' . $urlpost);
						?>
						<a href="<?php echo esc_url( $link_share ); ?>" id="instagram">
							<svg xmlns="http://www.w3.org/2000/svg" width="102" height="102" viewBox="0 0 102 102" id="instagram"><defs><radialGradient id="a" cx="6.601" cy="99.766" r="129.502" gradientUnits="userSpaceOnUse"><stop offset=".09" stop-color="#fa8f21"></stop><stop offset=".78" stop-color="#d82d7e"></stop></radialGradient><radialGradient id="b" cx="70.652" cy="96.49" r="113.963" gradientUnits="userSpaceOnUse"><stop offset=".64" stop-color="#8c3aaa" stop-opacity="0"></stop><stop offset="1" stop-color="#8c3aaa"></stop></radialGradient></defs><path fill="url(#a)" d="M25.865,101.639A34.341,34.341,0,0,1,14.312,99.5a19.329,19.329,0,0,1-7.154-4.653A19.181,19.181,0,0,1,2.5,87.694,34.341,34.341,0,0,1,.364,76.142C.061,69.584,0,67.617,0,51s.067-18.577.361-25.14A34.534,34.534,0,0,1,2.5,14.312,19.4,19.4,0,0,1,7.154,7.154,19.206,19.206,0,0,1,14.309,2.5,34.341,34.341,0,0,1,25.862.361C32.422.061,34.392,0,51,0s18.577.067,25.14.361A34.534,34.534,0,0,1,87.691,2.5a19.254,19.254,0,0,1,7.154,4.653A19.267,19.267,0,0,1,99.5,14.309a34.341,34.341,0,0,1,2.14,11.553c.3,6.563.361,8.528.361,25.14s-.061,18.577-.361,25.14A34.5,34.5,0,0,1,99.5,87.694,20.6,20.6,0,0,1,87.691,99.5a34.342,34.342,0,0,1-11.553,2.14c-6.557.3-8.528.361-25.14.361s-18.577-.058-25.134-.361"></path><path fill="url(#b)" d="M25.865,101.639A34.341,34.341,0,0,1,14.312,99.5a19.329,19.329,0,0,1-7.154-4.653A19.181,19.181,0,0,1,2.5,87.694,34.341,34.341,0,0,1,.364,76.142C.061,69.584,0,67.617,0,51s.067-18.577.361-25.14A34.534,34.534,0,0,1,2.5,14.312,19.4,19.4,0,0,1,7.154,7.154,19.206,19.206,0,0,1,14.309,2.5,34.341,34.341,0,0,1,25.862.361C32.422.061,34.392,0,51,0s18.577.067,25.14.361A34.534,34.534,0,0,1,87.691,2.5a19.254,19.254,0,0,1,7.154,4.653A19.267,19.267,0,0,1,99.5,14.309a34.341,34.341,0,0,1,2.14,11.553c.3,6.563.361,8.528.361,25.14s-.061,18.577-.361,25.14A34.5,34.5,0,0,1,99.5,87.694,20.6,20.6,0,0,1,87.691,99.5a34.342,34.342,0,0,1-11.553,2.14c-6.557.3-8.528.361-25.14.361s-18.577-.058-25.134-.361"></path><path fill="#fff" d="M461.114,477.413a12.631,12.631,0,1,1,12.629,12.632,12.631,12.631,0,0,1-12.629-12.632m-6.829,0a19.458,19.458,0,1,0,19.458-19.458,19.457,19.457,0,0,0-19.458,19.458m35.139-20.229a4.547,4.547,0,1,0,4.549-4.545h0a4.549,4.549,0,0,0-4.547,4.545m-30.99,51.074a20.943,20.943,0,0,1-7.037-1.3,12.547,12.547,0,0,1-7.193-7.19,20.923,20.923,0,0,1-1.3-7.037c-.184-3.994-.22-5.194-.22-15.313s.04-11.316.22-15.314a21.082,21.082,0,0,1,1.3-7.037,12.54,12.54,0,0,1,7.193-7.193,20.924,20.924,0,0,1,7.037-1.3c3.994-.184,5.194-.22,15.309-.22s11.316.039,15.314.221a21.082,21.082,0,0,1,7.037,1.3,12.541,12.541,0,0,1,7.193,7.193,20.926,20.926,0,0,1,1.3,7.037c.184,4,.22,5.194.22,15.314s-.037,11.316-.22,15.314a21.023,21.023,0,0,1-1.3,7.037,12.547,12.547,0,0,1-7.193,7.19,20.925,20.925,0,0,1-7.037,1.3c-3.994.184-5.194.22-15.314.22s-11.316-.037-15.309-.22m-.314-68.509a27.786,27.786,0,0,0-9.2,1.76,19.373,19.373,0,0,0-11.083,11.083,27.794,27.794,0,0,0-1.76,9.2c-.187,4.04-.229,5.332-.229,15.623s.043,11.582.229,15.623a27.793,27.793,0,0,0,1.76,9.2,19.374,19.374,0,0,0,11.083,11.083,27.813,27.813,0,0,0,9.2,1.76c4.042.184,5.332.229,15.623.229s11.582-.043,15.623-.229a27.8,27.8,0,0,0,9.2-1.76,19.374,19.374,0,0,0,11.083-11.083,27.716,27.716,0,0,0,1.76-9.2c.184-4.043.226-5.332.226-15.623s-.043-11.582-.226-15.623a27.786,27.786,0,0,0-1.76-9.2,19.379,19.379,0,0,0-11.08-11.083,27.748,27.748,0,0,0-9.2-1.76c-4.041-.185-5.332-.229-15.621-.229s-11.583.043-15.626.229" transform="translate(-422.637 -426.196)"></path></svg>
							<p><?php esc_html_e( 'Share on Instagram', 'solace-extra' ); ?></p>
						</a>
						<?php					
					}
				}
				?>
			</div>
			<?php
		}
	}

}
