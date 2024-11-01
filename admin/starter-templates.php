<?php
defined( 'ABSPATH' ) || exit;

/**
 * Backend submenu Starter Templates
 *
 * @link       https://solacewp.com
 * @since      1.0.0
 *
 * @package    Solace_Extra
 * @subpackage Solace_Extra/admin
 */

/**
 * The admin-specific functionality of the plugin (starter templates).
 *
 * @package    Solace_Extra
 * @subpackage Solace_Extra/starter-templates
 * @author     Solace <solacewp@gmail.com>
 */

class Solace_Extra_Starter_Templates {

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
	 * Enqueue dash icons
	 *
	 * @since    1.0.0
	 */
	public function enqueue_admin_dashicons() {
		wp_enqueue_style('dashicons');
	}

	/**
	 * Ajax Search
	 *
	 * @since    1.0.0
	 */
	public function action_ajax_search_server() {
		// Verify nonce
		if (isset($_POST['nonce']) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'] ) ), 'ajax-nonce' )) {

			// Keyword
			$keyword = ! empty( $_POST['keyword'] ) ? sanitize_text_field( wp_unslash( $_POST['keyword'] ) ) : '';
			$getType = ! empty( $_POST['getType'] ) ? sanitize_text_field( wp_unslash( $_POST['getType'] ) ) : '';

			// Primary and backup API URLs
			$url_solace_search = 'https://solacewp.com/api/wp-json/solace/v1/demo/';
			$url_local_search = plugin_dir_url(__FILE__) . 'partials/demo.json';

			// Use wp_remote_get() instead of cURL
			$response_search = wp_remote_get($url_solace_search);

			// Check for errors
			if (is_wp_error($response_search)) {
				// handle the error if needed
			} else {
				// Successful response retrieval
				$http_code_search = wp_remote_retrieve_response_code($response_search);

				// Process the JSON data here
				$data_search = json_decode(wp_remote_retrieve_body($response_search), true);
				$domain = ! empty( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';

				if ( 'solacewp.com' !== $domain ) {
					$data_search = array_filter($data_search, function($demo) {
						return $demo['demo_status'] !== 'draft' && $demo['demo_status'] !== 'pending';
					});
				}

				// Print the result
				$matching_api = array();
				$index = 1;
				$show_posts = 9;
				foreach ($data_search as $value) {

					$demo_image = $value['demo_image'];
					$get_solace_type = $getType;
					$arr_list_type = array('elementor', 'gutenberg');
					$is_type = false;
					$demo_search = strtolower($value['demo_search'] . ' ' . $value['demo_desc']);

					if (in_array($get_solace_type, $arr_list_type)) {
						$is_type = true;
					}
					if (!empty($demo_image) && !empty($get_solace_type)) {
						if ($is_type && $value['demo_type'] === $get_solace_type) {

							if (preg_match("/$keyword/i", $demo_search)) {
								$matching_api[] = $value;
								$label_new = false;
								if ( in_array( 'New', $value['demo_category'] ) ) {
									$label_new = true;
								}								

								$label_recommended = false;
								if ( in_array( 'Recommended', $value['demo_category'] ) ) {
									$label_recommended = true;
								}
								?>
								<div class='demo demo<?php echo esc_attr($index); ?>' data-url='<?php echo esc_attr($value['demo_link']); ?>' data-name='<?php echo esc_attr($value['title']); ?>'>
									<div class="box-image">
										<img src="<?php echo esc_url($demo_image); ?>" alt="Demo Image" />
									</div>
									<div class="box-content">
										<div class="top-content">
											<?php if (!empty($value['title'])) : ?>
												<span class="title"><?php echo esc_html( $value['title'] ); ?></span>
											<?php endif; ?>
											<?php if ( $label_recommended ) : ?>
												<span class="label-recommended"><?php esc_html_e( 'Recommended', 'solace-extra' ); ?></span>
											<?php endif; ?>											
											<?php if ( $label_new ) : ?>
												<span class="label"><?php esc_html_e( 'New', 'solace-extra' ); ?></span>
										<?php endif; ?>
										</div>
										<div class="bottom-content">
											<p><strong><?php echo esc_html__( 'Ideal for: ', 'solace-extra' ); ?></strong><?php echo esc_html( $value['demo_desc'] ); ?></p>
										</div>
									</div>
								</div>
								<?php
								echo '<span class="count-demo" style="display: none;">' . absint($index) . '</span>';
								$index++;
							}
						}
					}
				}

				// Show All Demos
				$solaceLoadMore = !empty($_COOKIE['solaceLoadMore']) ? (int)$_COOKIE['solaceLoadMore'] * 9: 9;
				$index_all_demos = 1;
				$show_default_posts = $solaceLoadMore;
				foreach ($data_search as $value) {
					$demo_image = $value['demo_image'];
					if ( $keyword == 'empty' && ! empty( $demo_image ) ) {
						$label_new = false;
						if ( in_array( 'New', $value['demo_category'] ) ) {
							$label_new = true;
						}

						$label_recommended = false;
						if ( in_array( 'Recommended', $value['demo_category'] ) ) {
							$label_recommended = true;
						}
						?>
						<div class='demo demo<?php echo esc_attr($index); ?>' data-url='<?php echo esc_attr($value['demo_link']); ?>' data-name='<?php echo esc_attr($value['title']); ?>'>
							<div class="box-image">
								<img src="<?php echo esc_url($demo_image); ?>" alt="Demo Image" />
							</div>
							<div class="box-content">
								<div class="top-content">
									<?php if (!empty($value['title'])) : ?>
										<span class="title"><?php echo esc_html( $value['title'] ); ?></span>
									<?php endif; ?>
									<?php if ( $label_recommended ) : ?>
										<span class="label-recommended"><?php esc_html_e( 'Recommended', 'solace-extra' ); ?></span>
									<?php endif; ?>
									<?php if ( $label_new && false ) : ?>
										<span class="label"><?php esc_html_e( 'New', 'solace-extra' ); ?></span>
									<?php endif; ?>
								</div>
								<div class="bottom-content">
									<p><strong><?php echo esc_html__( 'Ideal for: ', 'solace-extra' ); ?></strong><?php echo esc_html( $value['demo_desc'] ); ?></p>
								</div>
							</div>
						</div>
						<?php
						// Looping Only $show_default_posts
						if ($index_all_demos === $show_default_posts) {
							break;
						}
						$index_all_demos++;
					}
				}
			}

		} else {
			// Invalid nonce, respond with an error
			$response = array('error' => 'Invalid nonce!');
			echo wp_json_encode($response);
		}		

		wp_die();
	}

	/**
	 * Ajax checkbox
	 *
	 * @since    1.0.0
	 */
	public function action_ajax_checkbox() {

		// Verify nonce
		if (isset($_POST['nonce']) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'] ) ), 'ajax-nonce' )) {

			$getType = ! empty( $_POST['getType'] ) ? sanitize_text_field( wp_unslash( $_POST['getType'] ) ) : '';

			// Primary and backup API URLs
			$url_solace_checkbox = 'https://solacewp.com/api/wp-json/solace/v1/demo/';
			$url_local_checkbox = plugin_dir_url(__FILE__) . 'partials/demo.json';
	
			// Use wp_remote_get() instead of cURL
			$response_checkbox = wp_remote_get($url_solace_checkbox);
	
			// Check for errors
			if (is_wp_error($response_checkbox)) {
				// handle the error if needed
			} else {
				// Successful response retrieval
				$http_code_checkbox = wp_remote_retrieve_response_code($response_checkbox);
	
				// Process the JSON data here
				$data_checkbox = json_decode(wp_remote_retrieve_body($response_checkbox), true);
				$domain = ! empty( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';

				if ( 'solacewp.com' !== $domain ) {
					$data_checkbox = array_filter($data_checkbox, function($demo) {
						return $demo['demo_status'] !== 'draft' && $demo['demo_status'] !== 'pending';
					});
				}
	
				// Print the result
				$index = 1;
				$show_posts = 9;
				foreach ($data_checkbox as $value) {
	
					$demo_image = $value['demo_image'];
					$get_solace_type = $getType;
					$arr_list_type = array('elementor', 'gutenberg');
					$is_type = false;
					$checked = ! empty( $_POST['checked'] ) ? sanitize_text_field( wp_unslash( $_POST['checked'] ) ) : '';
					$list_checkbox = explode(', ', $checked );

	
					if (in_array($get_solace_type, $arr_list_type)) {
						$is_type = true;
					}
					if (!empty($demo_image) && !empty($get_solace_type)) {
	
						// Show Only Checked
						if ($is_type && $value['demo_type'] === $get_solace_type && array_intersect($value['demo_category'], $list_checkbox)) {

							$label_new = false;
							if ( in_array( 'New', $value['demo_category'] ) ) {
								$label_new = true;
							}							

							$label_recommended = false;
							if ( in_array( 'Recommended', $value['demo_category'] ) ) {
								$label_recommended = true;
							}

							?>
							<div class='demo demo<?php echo esc_attr($index); ?>' data-url='<?php echo esc_attr($value['demo_link']); ?>' data-name='<?php echo esc_attr($value['title']); ?>'>
								<div class="box-image">
									<img src="<?php echo esc_url($demo_image); ?>" alt="Demo Image" />
								</div>
								<div class="box-content">
									<div class="top-content">
										<?php if (!empty($value['title'])) : ?>
											<span class="title"><?php echo esc_html( $value['title'] ); ?></span>
										<?php endif; ?>
										<?php if ( $label_recommended ) : ?>
											<span class="label-recommended"><?php esc_html_e( 'Recommended', 'solace-extra' ); ?></span>
										<?php endif; ?>											
										<?php if ( $label_new ) : ?>
											<span class="label"><?php esc_html_e( 'New', 'solace-extra' ); ?></span>
									<?php endif; ?>
									</div>
									<div class="bottom-content">
										<p><strong><?php echo esc_html__( 'Ideal for: ', 'solace-extra' ); ?></strong><?php echo esc_html( $value['demo_desc'] ); ?></p>
									</div>
								</div>
							</div>
							<?php
							echo '<span class="count-demo" style="display: none;">' . absint($index) . '</span>';
							$index++;
						}
					}
				}

				// Show All Demos
				$solaceLoadMore = !empty($_COOKIE['solaceLoadMore']) ? (int)$_COOKIE['solaceLoadMore'] * 9: 9;
				$index_all_demos = 1;
				$show_default_posts = $solaceLoadMore;
				foreach ($data_checkbox as $value) {
					$demo_image = $value['demo_image'];
					if ( $_POST['checked'] == 'show-all-demos' && ! empty( $demo_image ) ) {
						$label_new = false;
						if ( in_array( 'New', $value['demo_category'] ) ) {
							$label_new = true;
						}

						$label_recommended = false;
						if ( in_array( 'Recommended', $value['demo_category'] ) ) {
							$label_recommended = true;
						}
						?>
						<div class='demo demo<?php echo esc_attr($index); ?>' data-url='<?php echo esc_attr($value['demo_link']); ?>' data-name='<?php echo esc_attr($value['title']); ?>'>
							<div class="box-image">
								<img src="<?php echo esc_url($demo_image); ?>" alt="Demo Image" />
							</div>
							<div class="box-content">
								<div class="top-content">
									<?php if (!empty($value['title'])) : ?>
										<span class="title"><?php echo esc_html( $value['title'] ); ?></span>
									<?php endif; ?>
									<?php if ( $label_recommended ) : ?>
										<span class="label-recommended"><?php esc_html_e( 'Recommended', 'solace-extra' ); ?></span>
									<?php endif; ?>
									<?php if ( $label_new && false ) : ?>
										<span class="label"><?php esc_html_e( 'New', 'solace-extra' ); ?></span>
									<?php endif; ?>
								</div>
								<div class="bottom-content">
									<p><strong><?php echo esc_html__( 'Ideal for: ', 'solace-extra' ); ?></strong><?php echo esc_html( $value['demo_desc'] ); ?></p>
								</div>
							</div>
						</div>
						<?php
						// Looping Only $show_default_posts
						if ($index_all_demos === $show_default_posts) {
							break;
						}
						$index_all_demos++;
					}
				}				
			}

		} else {
			// Invalid nonce, respond with an error
			$response = array('error' => 'Invalid nonce!');
			echo wp_json_encode($response);
		}		

		wp_die();		

	}

	/**
	 * Ajax Load More
	 */
	public function call_ajax_load_more() {
		// Verify nonce
		if (isset($_POST['nonce']) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'] ) ), 'ajax-nonce' )) {		

			$getType = ! empty( $_POST['getType'] ) ? sanitize_text_field( wp_unslash( $_POST['getType'] ) ) : '';
			$totalPosts = ! empty( $_POST['totalPosts'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['totalPosts'] ) ) : '';
			$postsPerPage = 9;
			$page_load_more = $totalPosts / $postsPerPage + 1;

			if (!empty($_COOKIE['solaceLoadMore'])) {
				$solaceLoadMore = ! empty( $_COOKIE['solaceLoadMore'] ) ? absint( wp_unslash( $_COOKIE['solaceLoadMore'] ) ) : '';

				if ($page_load_more > $solaceLoadMore) {
					setcookie('solaceLoadMore', $page_load_more, time() + (10 * 24 * 60 * 60));
				}
			} else {
				setcookie('solaceLoadMore', $page_load_more, time() + (10 * 24 * 60 * 60));
			}

			// Keyword
			$keyword = ! empty( $_POST['keyword'] ) ? sanitize_text_field( wp_unslash( $_POST['keyword'] ) ) : '';

			// Primary and backup API URLs
			$url_solace_load_more = 'https://solacewp.com/api/wp-json/solace/v1/demo/';
			$url_local_load_more = plugin_dir_url(__FILE__) . 'partials/demo.json';

			// Use wp_remote_get() instead of cURL
			$response_load_more = wp_remote_get($url_solace_load_more);

			// Check for errors
			if (is_wp_error($response_load_more)) {
				// handle the error if needed
			} else {
				// Successful response retrieval
				// if (wp_remote_retrieve_response_code($response_load_more) >= 400) {
				// 	// The response_load_more code is 400 or greater
				// 	// Switch to the backup URL
				// 	$response_load_more = wp_remote_get($url_local_load_more);
				// }

				// Process the JSON data here
				$data_load_more = json_decode(wp_remote_retrieve_body($response_load_more), true);
				$domain = ! empty( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';

				if ( 'solacewp.com' !== $domain ) {
					$data_load_more = array_filter($data_load_more, function($demo) {
						return $demo['demo_status'] !== 'draft' && $demo['demo_status'] !== 'pending';
					});
				}

				// Print the result
				$matching_api = array();
				$index = 1;
				foreach ($data_load_more as $value) {

					$demo_image = $value['demo_image'];
					$get_solace_type = $getType;
					$arr_list_type = array('elementor', 'gutenberg');
					$is_type = false;

					if (in_array($get_solace_type, $arr_list_type)) {
						$is_type = true;
					}
					if (!empty($demo_image) && !empty($get_solace_type)) {
						if ($is_type && $value['demo_type'] === $get_solace_type) {
							// Start load more
							if ($index > $totalPosts && $index <= ($totalPosts + $postsPerPage)) {
								?>
								<div class='demo demo<?php echo esc_attr($index); ?>' data-url='<?php echo esc_attr($value['demo_link']); ?>' data-name='<?php echo esc_attr($value['title']); ?>'>
									<?php
                                    $label_new = false;
                                    if ( in_array( 'New', $value['demo_category'] ) ) {
                                        $label_new = true;
                                    }

									$label_recommended = false;
									if ( in_array( 'Recommended', $value['demo_category'] ) ) {
										$label_recommended = true;
									}

									?>
									<div class="box-image">
										<img src="<?php echo esc_url($demo_image); ?>" alt="Demo Image" />
									</div>
									<div class="box-content">
										<div class="top-content">
											<?php if (!empty($value['title'])) : ?>
												<span class="title"><?php echo esc_html( $value['title'] ); ?></span>
											<?php endif; ?>
											<?php if ( $label_recommended ) : ?>
												<span class="label-recommended"><?php esc_html_e( 'Recommended', 'solace-extra' ); ?></span>
											<?php endif; ?>												
											<?php if ( $label_new ) : ?>
												<span class="label"><?php esc_html_e( 'New', 'solace-extra' ); ?></span>
										<?php endif; ?>
										</div>
										<div class="bottom-content">
											<p><strong><?php echo esc_html__( 'Ideal for: ', 'solace-extra' ); ?></strong><?php echo esc_html( $value['demo_desc'] ); ?></p>
										</div>
									</div>									
								</div>
								<?php
								echo '<span class="count-demo" style="display: none;">' . absint($index) . '</span>';
							}
							$index++;
						}
					}
				}
			}
		} else {
			// Invalid nonce, respond with an error
			$response = array('error' => 'Invalid nonce!');
			echo wp_json_encode($response);
		}

		wp_die();
	}

	/**
	 * Add cookie page access.
	 */
	public function add_cookie_page_access() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'] ) ), 'ajax-nonce' )) {
            $response = array('error' => 'Invalid nonce!');
			echo wp_json_encode($response);
            wp_die();
        }

		// Set cookie
		if ( empty( $_COOKIE['solace_page_access'] ) ) {
			setcookie( 'solace_page_access', true, time() + 86400 );
		}

		wp_die();
	}	
	
}
