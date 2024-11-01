<?php defined( 'ABSPATH' ) || exit; ?>
<?php $customizer_link = admin_url('customize.php'); ?>
<div class="wrap">
    <?php require_once plugin_dir_path( dirname( __FILE__ ) ) . 'partials/header.php'; ?>
    <section class="start-templates">
        <div class="content-top">
            <div class="mycontainer">
                <div class="boxes">
                    <div class="col col1"></div>
                    <div class="col col2">
                        <h2>
                            <?php esc_html_e( 'Get Started With Our Free', 'solace-extra' ); ?>
                            <span><?php esc_html_e( 'Starter Templates', 'solace-extra' ); ?></span>
                        </h2>
                    </div>
                    <div class="col col3">
                        <div class="dropdown1">
                            <select name="filter1" id="filter1">
                                <option value="all"><?php esc_html_e( 'All', 'solace-extra' ); ?></option>
                                <option value="blog"><?php esc_html_e( 'Blog', 'solace-extra' ); ?></option>
                                <option value="news"><?php esc_html_e( 'News', 'solace-extra' ); ?></option>
                            </select>
                        </div>
                        <div class="dropdown2"></div>
                        <div class="dropdown3">
                            <select name="filter1" id="filter1">
                                <option value="populer"><?php esc_html_e( 'Populer', 'solace-extra' ); ?></option>
                                <option value="all"><?php esc_html_e( 'All', 'solace-extra' ); ?></option>
                                <option value="new"><?php esc_html_e( 'New', 'solace-extra' ); ?></option>
                            </select>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
            <img class="decor1" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . '../images/starter-templates/decor1.png' ); ?>" alt="decor1" />
        </div>
        <div class="content-main">
            <?php 
            $show_posts = 9;
            $get_show_posts = 0;
            $solaceLoadMore = 0;
            ?>
            <aside>
                <div class="mycontainer">
                    <span class="title"><?php esc_html_e('Pick your layout', 'solace-extra'); ?></span>
                    <span class="desc">
                        <?php esc_html_e('Search in over', 'solace-extra'); ?>
                        <?php
                        // Primary and backup API URLs
                        $url_solace_count_demo = 'https://solacewp.com/api/wp-json/solace/v1/demo/';
                        $url_local_count_demo = plugin_dir_url(__FILE__) . 'demo.json';

                        // Make the request using wp_remote_get()
                        $response_count_demo = wp_remote_get($url_solace_count_demo);

                        // Check for errors
                        if (is_wp_error($response_count_demo)) {
                            // echo 'cURL Error: ' . $response_count_demo->get_error_message();
                        } else {
                            // Successful response retrieval
                            $http_code_count_demo = wp_remote_retrieve_response_code($response_count_demo);

                            if ($http_code_count_demo >= 400) {
                                // The response code is 400 or greater
                                // Switch to the backup URL
                                $response_count_demo = wp_remote_get($url_local_count_demo);
                            }

                            // You can process the JSON data here, for example:
                            $body_count_demo = wp_remote_retrieve_body($response_count_demo);
                            $data_count_demo = json_decode($body_count_demo, true);
                            $domain    = ! empty( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';

                            if ( 'solacewp.com' !== $domain ) {
                                $data_count_demo = array_filter($data_count_demo, function($demo) {
                                    return $demo['demo_status'] !== 'draft' && $demo['demo_status'] !== 'pending';
                                });
                            }

                            // Calc total count demo
                            $data_count_demo = absint(count($data_count_demo));
                            echo '<span class="count">' . absint( $data_count_demo ) . '</span>';
                        }
                        ?>
                        <?php esc_html_e(' total layouts', 'solace-extra'); ?>
                    </span>
                    <div class="box-search">
                        <input type="text" class="search-input" placeholder="Search" value="" name="s">
                        <div class="box-btn">
                            <button type="submit" class="search-submit" tabindex="2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12.0068 10.8948C14.1726 8.09749 13.7934 4.09906 11.1404 1.75874C8.48748 -0.581568 4.47297 -0.45903 1.9677 2.03874C-0.539287 4.54203 -0.66661 8.56354 1.677 11.2204C4.02062 13.8773 8.02658 14.2528 10.8232 12.0778L10.8595 12.1158L14.4154 15.6729C14.7431 16.0005 15.2742 16.0005 15.6019 15.6729C15.9295 15.3453 15.9295 14.8141 15.6019 14.4865L12.0448 10.9305C12.0325 10.9184 12.0198 10.9061 12.0068 10.8948ZM10.2666 3.22461C12.231 5.18853 12.231 8.37311 10.2668 10.3373C8.30263 12.3015 5.11806 12.3015 3.15387 10.3373C1.18956 8.37324 1.18956 5.18866 3.15374 3.22448C5.11793 1.26029 8.30237 1.26042 10.2666 3.22461Z" fill="#2EBBEF" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <span class="cat"><?php esc_html_e('Categories', 'solace-extra'); ?></span>
                    <form action="#">
                        <?php
                        if (!isset($_POST['nonce']) || !wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'] ) ), 'ajax-nonce' )) {
                            // Primary and backup API URLs
                            $url_solace_category = 'https://solacewp.com/api/wp-json/solace/v1/category';
                            $url_local_category = plugin_dir_url(__FILE__) . 'category.json';

                            // Make the request using wp_remote_get()
                            $response_category = wp_remote_get($url_solace_category);

                            // Check for errors
                            if (is_wp_error($response_category)) {
                                // echo 'cURL Error: ' . $response_category->get_error_message();
                            } else {
                                // Successful response retrieval
                                $http_code_category = wp_remote_retrieve_response_code($response_category);

                                if ($http_code_category >= 400) {
                                    // The response code is 400 or greater
                                    // Switch to the backup URL
                                    $response_category = wp_remote_get($url_local_category);
                                }

                                // You can process the JSON data here, for example:
                                $body_category = wp_remote_retrieve_body($response_category);
                                $data_category = json_decode($body_category, true);

                                // Print the result
                                $index_data_category = 1;
                                foreach ($data_category as $value) {
                                    if ($value['category']) {
                                        $value_sanitize = str_replace('&', '&amp;', $value['category']);
                                    }

                                    $get_solace_type    = ! empty( $_GET['type'] ) ? sanitize_text_field( wp_unslash( $_GET['type'] ) ) : '';

                                    $arr_list_type = array('elementor', 'gutenberg');

                                    if (!empty($get_solace_type)) {
                                        if (in_array($get_solace_type, $arr_list_type)) {
                                            if ($value['type'] === $get_solace_type) {
                                                ?>
                                                <div class="box-checkbox">
                                                    <input type="checkbox" id="<?php echo esc_html($value['category']); ?>" name="<?php echo esc_html($value['category']); ?>" value="<?php echo esc_html($value_sanitize); ?>">
                                                    <label for="<?php echo esc_html($value['category']); ?>"><?php echo esc_html($value['category']); ?></label><br>
                                                </div>
                                                <?php
                                            }
                                        }
                                    }
                                    $index_data_category++;
                                }
                            }
                        }
                        ?>
                    </form>
                </div>
                <img class="decor2" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . '../images/starter-templates/decor2.png' ); ?>" alt="decor2" />
                <img class="decor3" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . '../images/starter-templates/decor3.png' ); ?>" alt="decor3" />
                <img class="decor4" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . '../images/starter-templates/decor4.png' ); ?>" alt="decor4" />
            </aside>
            <main>
                <div class="mycontainer">
                    <?php
                    // Primary and backup API URLs
                    $url_solace_demo = 'https://solacewp.com/api/wp-json/solace/v1/demo/';
                    $url_local_demo = plugin_dir_url(__FILE__) . 'demo.json';
                    $total_demo = 0;

                    // Make the request using wp_remote_get()
                    $response_demo = wp_remote_get($url_solace_demo);

                    // Check for errors
                    if (is_wp_error($response_demo)) {
                        // echo 'cURL Error: ' . $response_demo->get_error_message();
                    } else {
                        // Successful response retrieval
                        $http_code_demo = wp_remote_retrieve_response_code($response_demo);

                        if ($http_code_demo >= 400) {
                            // The response code is 400 or greater
                            // Switch to the backup URL
                            $response_demo = wp_remote_get($url_local_demo);
                        }

                        // You can process the JSON data here, for example:
                        $body_demo = wp_remote_retrieve_body($response_demo);
                        $data_demo = json_decode($body_demo, true);
                        $total_demo = count($data_demo);
                        $domain    = ! empty( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';           

                        if ( 'solacewp.com' !== $domain ) {
                            $data_demo = array_filter($data_demo, function($demo) {
                                return $demo['demo_status'] !== 'draft' && $demo['demo_status'] !== 'pending';
                            });
                        }

                        // Print the result
                        $index = 1;
                        foreach ($data_demo as $value) {
                            $demo_image = $value['demo_image'];
                            $get_solace_type    = ! empty( $_GET['type'] ) ? sanitize_text_field( wp_unslash( $_GET['type'] ) ) : '';                              
                            $arr_list_type = array('elementor', 'gutenberg');
                            $is_type = false;

                            if (in_array($get_solace_type, $arr_list_type)) {
                                $is_type = true;
                            }

                            if (!empty($demo_image) && !empty($get_solace_type)) {
                                if ($is_type && $value['demo_type'] === $get_solace_type) {

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
                                }
                            }

                            $index++;

                            $solaceLoadMore = !empty($_COOKIE['solaceLoadMore']) ? (int)$_COOKIE['solaceLoadMore'] : 0;
                            $solaceLoadMore = $solaceLoadMore * $show_posts;
                            if ($total_demo < $solaceLoadMore) {
                                $solaceLoadMore = $total_demo;
                            }

                            if (empty($_COOKIE['solaceLoadMore'])) {
                                if ($index > $show_posts) {
                                    $get_show_posts = $show_posts;
                                    break;
                                }
                            } else {
                                if ($index > $solaceLoadMore) {
                                    $get_show_posts = $solaceLoadMore;
                                    break;
                                }
                            }
                        }
                    }
                    ?>
                    
                </div>
                <?php if ( $get_show_posts < $total_demo ) : ?>
                    <div class="box-load-more">
                        <button type="button" show-posts="<?php echo esc_attr( $get_show_posts ); ?>">
                            <?php esc_html_e( 'Load More', 'solace-extra' ); ?>
                            <div class="box-bubble">
                                <dotlottie-player src="<?php echo esc_url( SOLACE_EXTRA_ASSETS_URL . 'images/starter/loadmore.json' ); ?>" background="transparent" speed="1" style="width: 250px; height: 130px;" loop autoplay></dotlottie-player>
                            </div>
                        </button>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </section>
    <footer class="bottom">
        <div class="mycontainer">
            <div class="box left">
                <a href="<?php echo esc_url($myadmin . '/wp-admin/admin.php?page=dashboard-video'); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512">
                        <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z" />
                    </svg>
                    <span><?php esc_html_e('Back', 'solace-extra'); ?></span>
                </a>
            </div>

            <div class="box center">
                <a href="<?php echo esc_url($myadmin . '/wp-admin'); ?>">
                    <span><?php esc_html_e('Back to WordPress Dashboard', 'solace-extra'); ?></span>
                </a>
            </div>              

            <div class="box right" style="visibility: hidden;">
                <a href="#">
                    <span><?php esc_html_e('Next', 'solace-extra'); ?></span>
                    <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"/></svg>
                </a>
            </div>
        </div>
    </footer>    
</div>
