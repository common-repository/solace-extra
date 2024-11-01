<?php 
defined( 'ABSPATH' ) || exit;

if ( empty( $_COOKIE['solace_page_access'] ) ) {
    $url = get_admin_url() . 'admin.php?page=dashboard-starter-templates&type=elementor';
    wp_redirect( $url, 301 ); 
    exit;
}

function solace_extra_getGoogleFontsFamilyName($googleFontsUrl) {
    $url_parts = wp_parse_url($googleFontsUrl);
    $query_string = isset($url_parts['query']) ? $url_parts['query'] : '';

    parse_str($query_string, $query_params);

    $font_family = isset($query_params['family']) ? $query_params['family'] : '';

    return $font_family;
}

// Font Awesome
wp_enqueue_style('solace-fontawesome', get_template_directory_uri() . '/assets-solace/fontawesome/css/all.min.css', array(), '5.15.4', 'all');

function solace_extra_upload_logo() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'] ) ), 'ajax-nonce' )) {
        $response = array('error' => 'Invalid nonce!');
        echo wp_json_encode($response);
        wp_die();
    }

    if (isset($_FILES['logo'])) {
        $file = esc_url( esc_url_raw( $_FILES['logo'] ) );
        $upload_overrides = array('test_form' => false);
        $upload_result = wp_handle_upload($file, $upload_overrides);

        if (!empty($upload_result['url'])) {
            // Set the logo URL in theme mods
            set_theme_mod('custom_logo', $upload_result['url']);

            $response = array(
                'success' => true,
                'data' => array(
                    'url' => $upload_result['url']
                )
            );
            echo wp_json_encode($response);
        } else {
            $response = array(
                'success' => false
            );
            echo wp_json_encode($response);
        }
    }
    die();
}

?>

<div class="wrap wrap-step5">
<?php require_once plugin_dir_path(dirname(__FILE__)) . 'partials/header.php'; ?>

<?php 
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'] ) ), 'ajax-nonce' )) {
        $response = array('error' => 'Invalid nonce!');
    }

    $dataUrl = ! empty( $_GET['dataUrl'] ) ? esc_url_raw( wp_unslash( $_GET['dataUrl'] ) ) : '';
    // $dataUrl = 'https://stagging-solace.djavaweb.com/homeharbor/';
    // $dataUrl = 'https://stagging-solace.djavaweb.com/toyjungle/';
    // $iframe_url = 'https://solacewp.com/petrovex';
        // $dataUrl = 'https://solacewp.com/testtest/';

    $iframe_url = $dataUrl;

    // Trim whitespace from the beginning and end of the URL
    $dataUrl = trim($dataUrl);

    // Check if the URL does not end with a slash
    if (substr($dataUrl, -1) !== '/') {
        // If it does not, append a slash to the end of the URL
        $dataUrl .= '/';
    }

    $url = $dataUrl; 

    // $bodyClasses = solace_extra_getBodyClasses($url);
    $cssUrl = $dataUrl .'core/views/solace/style-main-new.min.css';

    // $backgroundColor = solace_extra_getBodyBackgroundColor($url, $cssUrl);

    $api_url = $dataUrl .'wp-json/elementor-api/v1/settings?timestamp=' . time();

    $color_palettes = array();
    $palette_font_scheme = array();
    
    $response = wp_remote_get($api_url, array('timeout' => 30));
    
    if ($response !== false) {
        // $data = json_decode($response, true);
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
    
        if ($data) {
            // echo "From Elementor API: $api_url<br />";
            // echo "<pre>";
            // print_r($data);
            // echo "</pre>";
            $api_base_color = $data['colors_data']['base_color'];
            $api_heading_color = $data['colors_data']['heading_color'];
            $api_link_button_color = $data['colors_data']['link_button_color'];
            $api_link_button_hover_color = $data['colors_data']['link_button_hover_color'];
            $api_button_color = $data['colors_data']['button_color'];
            $api_button_hover_color = $data['colors_data']['button_hover_color'];
            $api_text_selection_color = $data['colors_data']['text_selection_color'];
            $api_text_selection_bg_color = $data['colors_data']['text_selection_bg_color'];
            $api_border_color = $data['colors_data']['border_color'];
            $api_background_color = $data['colors_data']['background_color'];
            $api_page_title_text_color = $data['colors_data']['page_title_text_color'];
            $api_page_title_bg_color = $data['colors_data']['page_title_bg_color'];
            $api_bg_menu_dropdown_color = !empty($data['colors_data']['bg_menu_dropdown_color'])?$data['colors_data']['bg_menu_dropdown_color']:$data['colors_data']['border_color'];

$colors_data_from_api = "
--e-global-color-primary: $api_button_color;
--e-global-color-secondary: $api_page_title_bg_color;
--e-global-color-text: $api_base_color;
--e-global-color-accent: $api_bg_menu_dropdown_color;
--sol-color-base-font: $api_base_color;
--e-global-color-text: $api_base_color;
--e-global-color-solcolorbasefont: $api_base_color;
--e-global-color-solcolorheading: $api_heading_color;
--sol-color-heading: $api_heading_color;
--e-global-color-solcolorlinkbuttoninitial: $api_link_button_color;
--sol-color-link-button-initial: $api_link_button_color;
--e-global-color-solcolorlinkbuttonhover: $api_link_button_hover_color;
--sol-color-link-button-hover: $api_link_button_hover_color;
--e-global-color-solcolorbuttoninitial: $api_button_color;
--sol-color-button-initial: $api_button_color;
--e-global-color-solcolorbuttonhover: $api_button_hover_color;
--sol-color-button-hover: $api_button_hover_color;
--e-global-color-solcolorselectioninitial: $api_text_selection_color;
--sol-color-selection-initial: $api_text_selection_color;
--e-global-color-solcolorselectionhigh: $api_text_selection_bg_color;
--sol-color-selection-high: $api_text_selection_bg_color;
--e-global-color-solcolorborder: $api_border_color;
--e-global-color-solcolorbackground: $api_background_color;
--sol-color-background: $api_background_color;
--e-global-color-solcolorheadpagetitletexting: $api_page_title_text_color;
--sol-color-page-title-text: $api_page_title_text_color;
--e-global-color-solcolorpagetitletext: $api_page_title_text_color;
--e-global-color-solcolorpagetitlebackground: $api_page_title_bg_color;
--sol-color-page-title-background: $api_page_title_bg_color;
--e-global-color-secondary: $api_page_title_bg_color;
--sol-color-bg-menu-dropdown: $api_bg_menu_dropdown_color;
--sol-color-border: $api_bg_menu_dropdown_color;";

            for ($i = 2; $i <= 6; $i++) {
                $color_palettes[] = array(
                    $data['color_scheme']['solace_colors_elementor_' . $i]['base_color'],
                    $data['color_scheme']['solace_colors_elementor_' . $i]['heading_color'],
                    $data['color_scheme']['solace_colors_elementor_' . $i]['link_button_color'],
                    $data['color_scheme']['solace_colors_elementor_' . $i]['link_button_hover_color'],
                    $data['color_scheme']['solace_colors_elementor_' . $i]['button_color'],
                    $data['color_scheme']['solace_colors_elementor_' . $i]['button_hover_color'],
                    $data['color_scheme']['solace_colors_elementor_' . $i]['text_selection_color'],
                    $data['color_scheme']['solace_colors_elementor_' . $i]['text_selection_bg_color'],
                    $data['color_scheme']['solace_colors_elementor_' . $i]['border_color'],
                    $data['color_scheme']['solace_colors_elementor_' . $i]['background_color'],
                    $data['color_scheme']['solace_colors_elementor_' . $i]['page_title_text_color'],
                    $data['color_scheme']['solace_colors_elementor_' . $i]['page_title_bg_color'],
                    $data['color_scheme']['solace_colors_elementor_' . $i]['bg_menu_dropdown_color'],
                    $data['color_scheme']['solace_colors_elementor_' . $i]['publish'],
                );
            }

            

            for ($i = 1; $i <= 8; $i++) {
                $palette_font_scheme[$i] = array(
                    $data['palette_font_scheme']['solace_palette_font_elementor_' . $i]['base_font'],
                    $data['palette_font_scheme']['solace_palette_font_elementor_' . $i]['heading_font'],
                    $data['palette_font_scheme']['solace_palette_font_elementor_' . $i]['image_url'],
                );
            }
            $defaultx_font = !empty($data['default_elementor_font']['base_font'])?$data['default_elementor_font']['base_font']:'Manrope';
            $default_elementor_font_base = 'https://fonts.googleapis.com/css?family='.$defaultx_font;
            $default_elementor_font_heading = 'https://fonts.googleapis.com/css?family='.$data['default_elementor_font']['heading_font'];

            $palette_font_scheme[1] = array(
                $default_elementor_font_base,
                $default_elementor_font_heading,
                '',
            );
            // print_r($palette_font_scheme[0]);            
        } else {
            esc_html_e( 'Failed to decode JSON response.', 'solace-extra');
        }
    } else {
        esc_html_e( 'Failed to fetch response from the API.', 'solace-extra');
    }

    ?>
    <!-- <div class="simple-plugin-columns" style="max-width: 1240px;height: 94vh;"> -->
    <div class="simple-plugin-columns" style="height: 94vh;">
        <!-- <div class="loading-overlay">
            <dotlottie-player src="<?php //echo esc_url( SOLACE_EXTRA_ASSETS_URL . 'images/step5/loading-overlay.json' ); ?>" background="transparent" speed="1" style="width: 300px; height: 300px;" loop autoplay></dotlottie-player>

        </div> -->
        <div class='col-left' >
            <div class="palette-buttons">
                <div class="selected-demo">
                    <span class='demotitle'><?php esc_html_e( 'Selected template', 'solace-extra'); ?></span>
                    <div class='labeldemo'></div>
                </div>
                <hr  />
                <div class="logo-buttons">
                    <span class='titlelogo'><?php esc_html_e( 'Logo', 'solace-extra'); ?></span>
                    <!-- <form id="upload-logo-form" action="" method="post" enctype="multipart/form-data">
                        <input type="submit" value="Upload Your Logo" id="upload-logo-formx">
                    </form> -->
                    <button id='upload-media-button' class='button'>Upload Your Logo</button>
                    
                    <a href="#" class="logo_default"><i class="fas fa-undo"></i></a>
                </div>
                <img class="logo" src="" alt="Logo">
                <hr />
                <span class='titlecolor'><?php esc_html_e( 'Change Color Palette', 'solace-extra'); ?></span>
                <div class='colorlist'>
                    <a href="#" id="color-1" class="color active change-styles-btn" data-styles="<?php echo esc_attr($colors_data_from_api); ?>">
                        <span class='color_hex' style='background-color:<?php echo esc_html( $api_base_color ); ?>;'></span>
                        <span class='color_hex' style='background-color:<?php echo esc_html( $api_heading_color ); ?>;'></span>
                        <span class='color_hex' style='background-color:<?php echo esc_html( $api_button_color ); ?>;'></span>
                        <span class='color_hex' style='background-color:<?php echo esc_html( $api_background_color ); ?>;'></span>
                    </a>
                    
                    <?php
                    $count = 2;
                    for ($i = 0; $i <=6; $i++) {
                        if ( isset( $color_palettes[$i] )) {
                            $base_color = $color_palettes[$i][0];
                            if (!empty($base_color) && $color_palettes[$i][13]=='on' && $count<=4){
                                $base_color = $color_palettes[$i][0];
                                $heading_color = $color_palettes[$i][1];
                                $link_button_color = $color_palettes[$i][2];
                                $link_button_hover_color = $color_palettes[$i][3];
                                $button_color = $color_palettes[$i][4];
                                $button_hover_color = $color_palettes[$i][5];
                                $text_selection_color = $color_palettes[$i][6];
                                $text_selection_bg_color = $color_palettes[$i][7];
                                $border_color = $color_palettes[$i][8];
                                $background_color = $color_palettes[$i][9];
                                $page_title_text_color = $color_palettes[$i][10];
                                $page_title_bg_color = $color_palettes[$i][11];
                                $bg_menu_dropdown_color = $color_palettes[$i][12];
$colors_data_from_palette = "
--e-global-color-primary: $button_color;
--e-global-color-secondary: $page_title_bg_color;
--e-global-color-text: $base_color;
--e-global-color-accent: $bg_menu_dropdown_color;
--sol-color-base-font: $base_color;
--e-global-color-solcolorbasefont: $base_color;
--e-global-color-solcolorheading: $heading_color;
--sol-color-heading: $heading_color;
--e-global-color-solcolorlinkbuttoninitial: $link_button_color;
--sol-color-link-button-initial: $link_button_color;
--e-global-color-solcolorlinkbuttonhover: $link_button_hover_color;
--sol-color-link-button-hover: $link_button_hover_color;
--e-global-color-solcolorbuttoninitial: $button_color;
--sol-color-button-initial: $button_color;
--e-global-color-solcolorbuttonhover: $button_hover_color;
--sol-color-button-hover: $button_hover_color;
--e-global-color-solcolorselectioninitial: $text_selection_color;
--sol-color-selection-initial: $text_selection_color;
--e-global-color-solcolorselectionhigh: $text_selection_bg_color;
--sol-color-selection-high: $text_selection_bg_color;
--e-global-color-solcolorborder: $border_color;
--e-global-color-solcolorbackground: $background_color;
--sol-color-background: $background_color;
--e-global-color-solcolorheadpagetitletexting: $page_title_text_color;
--sol-color-page-title-text: $page_title_text_color;
--e-global-color-solcolorpagetitletext: $page_title_text_color;
--e-global-color-solcolorpagetitlebackground: $page_title_bg_color;
--sol-color-page-title-background: $page_title_bg_color;
--e-global-color-secondary: $page_title_bg_color;
--sol-color-bg-menu-dropdown: $bg_menu_dropdown_color;
--sol-color-border: $bg_menu_dropdown_color;";?>
                                <a href="#" id="color-<?php echo esc_attr($count); ?>" class="color change-styles-btn" data-styles="<?php echo esc_attr($colors_data_from_palette);?>">
                                    <span class='color_hex' style='background-color:<?php echo esc_html( $base_color ); ?>;'></span>
                                    <span class='color_hex' style='background-color:<?php echo esc_html( $heading_color ); ?>;'></span>
                                    <span class='color_hex' style='background-color:<?php echo esc_html( $button_color ); ?>;'></span>
                                    <span class='color_hex' style='background-color:<?php echo esc_html( $background_color ); ?>;'></span>
                                </a>
                            <?php
                            $count++;
                            }
                        }
                    }?>
                </div>
                <hr />
                <span class='titlecolor'><?php esc_html_e( 'Change Font Style', 'solace-extra'); ?></span>                
                <div class="fontlist">
                <?php
                    $fontlist = '<div class="fontlist">';
                    foreach ($palette_font_scheme as $index => $fontPair){
                        if (!empty($fontPair[0])) {
                            $font1 = solace_extra_getGoogleFontsFamilyName($fontPair[0]);
                            $font2 = solace_extra_getGoogleFontsFamilyName($fontPair[1]);

                            // Membuat CSS untuk font styles
                            $font_data_from_scheme = "
--bodyfontfamily: '$font1';
--e-global-typography-primary-font-family: '$font1';
--e-global-typography-secondary-font-family: '$font1';
--e-global-typography-text-font-family: '$font1';
p:'$font1';
--e-global-typography-solace_h1_font_family_general-font-family: '$font2';
--e-global-typography-solace_h2_font_family_general-font-family: '$font2';
--e-global-typography-solace_h3_font_family_general-font-family: '$font2';
--e-global-typography-solace_h4_font_family_general-font-family: '$font2';
--e-global-typography-solace_h5_font_family_general-font-family: '$font2';
--e-global-typography-solace_h6_font_family_general-font-family: '$font2';";

                            // Membuat URL untuk Google Fonts
                            $googleFontsUrl = "https://fonts.googleapis.com/css?family=" . urlencode($font1) . "|" . urlencode($font2);

                            // Memuat Google Fonts ke halaman WordPress
                            wp_enqueue_style('solace-google-fonts-'.$index, esc_url($googleFontsUrl), array(), $this->version, false);
                            ?>

                            <!-- Link untuk mengubah font styles -->
                            <a href="#" class="font tooltip change-font-styles-btn" data-font-styles="<?php echo esc_attr($font_data_from_scheme); ?>">
                                <span class="font tooltip" id="font-<?php echo esc_html( $index ); ?>" fontname="<?php echo esc_html( $font1 ) . ' & '. esc_html( $font2 ); ?>">
                                    <div class="f_group">
                                        <span class="font1" style="font-family: <?php echo esc_html( $font1 ); ?>;">A</span>
                                        <span class="font2" style="font-family: <?php echo esc_html( $font2 ); ?>;">a</span>
                                    </div>
                                </span>
                            </a>

                            <?php

                        }
                    }
                    ?>
                    </div>
                <hr />
                <div class="box-delete">
                    <input type="checkbox" id="delete-imported" name="delete-imported" >
                    <label for="delete-imported"><?php esc_html_e( 'Delete Previously imported sites', 'solace-extra' ); ?></label>
                </div>
                
                    <?php  
                    $data_plugin = Solace_Extra_Admin::get_required_plugin();
                    if ( isset( $data_plugin['page_builder'] ) && $data_plugin['page_builder'] && !class_exists( 'Elementor\Plugin' ) ) :
                    ?>
                        <div class="box-required-plugin-elementor">
                            <input type="checkbox" id="required-plugin-elementor" name="required-plugin-elementor" />
                            <label for="required-plugin-elementor">
                                <?php 
                                    ?>
                                    <span class="text">
                                        <?php esc_html_e( 'Install and activate Elementor plugin ', 'solace-extra' ); ?>
                                    </span>
                                    <span class="text required">
                                        <?php esc_html_e( '(required) ', 'solace-extra' ); ?>
                                    </span>
                                    <a href="<?php echo esc_url( 'https://wordpress.org/plugins/elementor/' ); ?>" target="_blank">
                                        <span class="dashicons dashicons-admin-links"></span>
                                    </a>
                            </label>
                        </div>
                    <?php endif; ?>

                    <?php  
                    if ( isset( $data_plugin['ecommerce'] ) && $data_plugin['ecommerce'] && !class_exists( 'WooCommerce' ) ) :
                    ?>
                        <div class="box-required-plugin-ecommerce">
                            <input type="checkbox" id="required-plugin-ecommerce" name="required-plugin-ecommerce" />
                            <label for="required-plugin-ecommerce">
                                <?php 
                                    ?>
                                    <span class="text">
                                        <?php esc_html_e( 'Install and activate WooCommerce plugin ', 'solace-extra' ); ?>
                                    </span>
                                    <span class="text required">
                                        <?php esc_html_e( '(required) ', 'solace-extra' ); ?>
                                    </span>
                                    <a href="<?php echo esc_url( 'https://wordpress.org/plugins/woocommerce/' ); ?>" target="_blank">
                                        <span class="dashicons dashicons-admin-links"></span>
                                    </a> 
                            </label>
                        </div>
                    <?php endif; ?>
                
                <div id="solace-extra-action-button">
                    <a href="#" id="solace-extra-back-link"><img src="<?php echo esc_url( SOLACE_EXTRA_ASSETS_URL . 'images/dashboard/sol-back.png' ); ?>" /></a>
                    <?php
                    $toggle_btn_continue = 'deactive';
                    if ( isset( $data_plugin['page_builder'] ) && isset( $data_plugin['ecommerce'] ) ) {
                        if ( $data_plugin['page_builder'] && $data_plugin['ecommerce'] ) {
                            if ( class_exists( 'Elementor\Plugin' ) && class_exists( 'WooCommerce' ) ) {
                                $toggle_btn_continue = 'active';
                            }  
                        } else if ( $data_plugin['page_builder'] && ! $data_plugin['ecommerce'] ) {
                            if ( class_exists( 'Elementor\Plugin' ) ) {
                                $toggle_btn_continue = 'active';
                            }  
                        }
                    }

                    if ( isset( $data_plugin['page_builder'] ) || isset( $data_plugin['ecommerce'] ) ) {           
                    ?>
                        <a href="#" id="solace-extra-continue" class="<?php echo esc_attr( $toggle_btn_continue ); ?>">
                            <?php esc_html_e( 'Continue', 'solace-extra'); ?>
                        </a>
                    <?php } else { ?>
                        <a href="#" id="solace-extra-continue-disable" class="<?php echo esc_attr( $toggle_btn_continue ); ?>">
                            <?php esc_html_e( 'Continue', 'solace-extra'); ?>
                        </a>                        
                    <?php } ?>
                </div>

            </div>
            
        </div>
        <div class='col-right iframeContainer'  style="position: relative; width: 100%; height: 94vh; overflow: auto;background-image: url('<?php echo esc_url( SOLACE_EXTRA_ASSETS_URL . 'images/dashboard/loading-website.png' ); ?>');">
            <img class='urlbar' src="<?php echo esc_url( SOLACE_EXTRA_ASSETS_URL . 'images/step5/urlbar4.png'  ); ?>"  />
            <iframe id='solaceIframe' src='<?php echo esc_attr($iframe_url);?>' ></iframe>
        </div>
    </div>
</div>

<script type="text/javascript">
    var ajaxurl = "<?php echo esc_url( admin_url('admin-ajax.php') ); ?>";
</script>
<script>
    // let solHasSetPreviewHeight = false
    const datax = { type: 'deleteLocal', value: '' };

    setTimeout(() => {
        postMessageToIframex(datax);
    }, 5000); // 3000 milidetik = 3 detik
    
    const iframex = document.getElementById('solaceIframe');

    function postMessageToIframex(data) {
        // Mengirim pesan ke iframe dengan data yang ditentukan
        iframex.contentWindow.postMessage(data, 'https://solacewp.com'); // Sesuaikan URL sesuai dengan domain iframe
        // iframe.contentWindow.postMessage(data, 'https://stagging-solace.djavaweb.com'); // Sesuaikan URL sesuai dengan domain iframe
        console.log('Post message sent to iframe:', data);
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        const defa = "";
        const data = { type: 'deleteLocal', value: defa };
        postMessageToIframex(data);
        console.log('domloaded');
    });

    function clearStoredStyles() {
        localStorage.removeItem('appliedStyles');
        localStorage.removeItem('appliedFontStyles');
        document.body.style.cssText = ''; // Reset style di elemen body
        console.log('Stored styles cleared from LocalStorage');
    }

    clearStoredStyles();


    jQuery(document).ready(function($) { 
        
        let dataUrl = localStorage.getItem('solaceInfo');
        // let demoName = localStorage.getItem('solaceDemoName');
        let demoName = getParameterByName('demo');
        demoName = demoName.replace(/-/g, ' ').replace(/\b\w/g, char => char.toUpperCase());

        console.log (demoName);
        $('.labeldemo').text(demoName);

        if (dataUrl) {
            console.log('localStorage Data:', dataUrl);
        }else {
            const demoType = getParameterByName('demoType');
            window.location = pluginUrl.admin_url + 'admin.php?page=dashboard-starter-templates&type=' + demoType;
        }
            
        var colorPalette2 = <?php echo wp_json_encode($color_palettes[0]); ?>;
        var colorPalette3 = <?php echo wp_json_encode($color_palettes[1]); ?>;
        var colorPalette4 = <?php echo wp_json_encode($color_palettes[2]); ?>;
        var colorPalette5 = <?php echo wp_json_encode($color_palettes[3]); ?>;
        var colorPalette6 = <?php echo wp_json_encode($color_palettes[4]); ?>;

        var defaultColorValue = {
            new_base_color: '<?php echo sanitize_hex_color( $api_base_color ); ?>',
            new_heading_color: '<?php echo sanitize_hex_color( $api_heading_color ); ?>',
            new_link_button_color: '<?php echo sanitize_hex_color( $api_link_button_color ); ?>',
            new_link_button_hover_color: '<?php echo sanitize_hex_color( $api_link_button_hover_color ); ?>',
            new_button_color: '<?php echo sanitize_hex_color( $api_button_color ); ?>',
            new_button_hover_color: '<?php echo sanitize_hex_color( $api_button_hover_color ); ?>',
            new_text_selection_color: '<?php echo sanitize_hex_color( $api_text_selection_color ); ?>',
            new_text_selection_bg_color: '<?php echo sanitize_hex_color( $api_text_selection_bg_color ); ?>',
            new_border_color: '<?php echo sanitize_hex_color( $api_border_color ); ?>',
            new_background_color: '<?php echo sanitize_hex_color( $api_background_color ); ?>',
            new_page_title_text_color: '<?php echo sanitize_hex_color( $api_page_title_text_color ); ?>',
            new_page_title_bg_color: '<?php echo sanitize_hex_color( $api_page_title_bg_color ); ?>',
            new_bg_menu_dropdown_color: '<?php echo !empty($api_bg_menu_dropdown_color) ? sanitize_hex_color( $api_bg_menu_dropdown_color ) : sanitize_hex_color( $api_border_color ); ?>'
        }

        console.log(defaultColorValue);
        var jsonColorString = JSON.stringify(defaultColorValue);
        localStorage.setItem('solace_step5_color', jsonColorString);


        var bodyFontFamily = "";
        var headingFontFamily = "";
        var selectedColorPalette ="";

        // set property for body font
        document.documentElement.style.setProperty('--bodyfontfamily', "'<?php echo esc_html( solace_extra_getGoogleFontsFamilyName($palette_font_scheme[1][0]) );?>'");
        $('.preview h1').css('font-family', 'var(--bodyfontfamily)');

        //set property for heading font
        $('.elementor-heading-title').css('font-family','<?php echo esc_html( solace_extra_getGoogleFontsFamilyName($palette_font_scheme[1][1]) );?>');
        document.documentElement.style.setProperty('--e-global-typography-primary-font-family','<?php echo esc_html( solace_extra_getGoogleFontsFamilyName($palette_font_scheme[1][1]) );?>');

        var defaultFontValue = { 
            new_solace_body_font_family: '<?php echo esc_html( solace_extra_getGoogleFontsFamilyName($palette_font_scheme[1][0]) );?>',
            new_solace_heading_font_family_general: '<?php echo esc_html( solace_extra_getGoogleFontsFamilyName($palette_font_scheme[1][1]) );?>'
        };
        console.log(defaultFontValue);
        var jsonFontString = JSON.stringify(defaultFontValue);
        localStorage.setItem('solace_step5_font', jsonFontString);
        
        var link1 = document.createElement('link');
        link1.href = '<?php echo esc_html( $palette_font_scheme[1][0] );?>';
        link1.rel = 'stylesheet';
        $('head').append(link1);
        var link2 = document.createElement('link');
        link2.href = '<?php echo esc_html( $palette_font_scheme[1][1] );?>';
        link2.rel = 'stylesheet';
        $('head').append(link2);

        var previewElement = document.querySelector('.preview');

        function getParameterByName(name) {
            name = name.replace(/[\[\]]/g, '\\$&');
            var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
                results = regex.exec(window.location.search);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, ' '));
        }

        const demoURL = getParameterByName('dataUrl');

       
        var firstload = true;

       

        // Remove the 'solaceRemoveImported' value from localStorage
        localStorage.removeItem('solaceRemoveImported');

        // Attach a click event listener to the checkbox inside the '.box-delete' element
        $('.box-delete input').click(function(e){
            // Check if the checkbox is checked
            var isChecked = $('#delete-imported').is(':checked');

            // Get the updated status of the checkbox
            var updatedStatus = $(this).is(':checked');

            // Update the status to a string value: "remove" if checked, "skip" if unchecked
            if ( updatedStatus ) {
                updatedStatus = "remove";
            } else {
                updatedStatus = "skip";
            }

            // Store the updated status in localStorage with the key 'solaceRemoveImported'
            localStorage.setItem('solaceRemoveImported', updatedStatus);
        });

        $('#solace-extra-back-link').click(function(){
            const demoType = getParameterByName('type');
            let admin = step5.site_url + '/wp-admin/admin.php?page=dashboard-starter-templates&type=' + demoType;
            window.location.replace(admin);
        })

        $('#color-1').click(function(e) {
            e.preventDefault();
            $('.colorlist .color').removeClass('active');
            $(this).addClass('active');
            
            var colorValue = { 
                new_base_color: "<?php echo sanitize_hex_color( $api_base_color ); ?>",
                new_heading_color: "<?php echo sanitize_hex_color( $api_heading_color ); ?>",
                new_link_button_color: "<?php echo sanitize_hex_color( $api_link_button_color ); ?>",
                new_link_button_hover_color: "<?php echo sanitize_hex_color( $api_link_button_hover_color ); ?>",
                new_button_color: "<?php echo sanitize_hex_color( $api_button_color ); ?>",
                new_button_hover_color: "<?php echo sanitize_hex_color( $api_button_hover_color ); ?>",
                new_text_selection_color: "<?php echo sanitize_hex_color( $api_text_selection_color ); ?>",
                new_text_selection_bg_color: "<?php echo sanitize_hex_color( $api_text_selection_bg_color ); ?>",
                new_border_color: "<?php echo sanitize_hex_color( $api_border_color ); ?>",
                new_background_color: "<?php echo sanitize_hex_color( $api_background_color ); ?>",
                new_page_title_text_color: "<?php echo sanitize_hex_color( $api_page_title_text_color ); ?>",
                new_page_title_bg_color: "<?php echo sanitize_hex_color( $api_page_title_bg_color ); ?>",
                new_bg_menu_dropdown_color: "<?php echo sanitize_hex_color( $api_bg_menu_dropdown_color ); ?>",
            };
            console.log(colorValue);
            var jsonString = JSON.stringify(colorValue);
            localStorage.setItem('solace_step5_color', jsonString);
        });

        $('#color-2').click(function(e) {
            e.preventDefault();
            $('.colorlist .color').removeClass('active');
            $(this).addClass('active');
            
            var colorValue = { 
                new_base_color: "<?php echo esc_js($color_palettes[0][0]); ?>",
                new_heading_color: "<?php echo esc_js($color_palettes[0][1]); ?>",
                new_link_button_color: "<?php echo esc_js($color_palettes[0][2]); ?>",
                new_link_button_hover_color: "<?php echo esc_js($color_palettes[0][3]); ?>",
                new_button_color: "<?php echo esc_js($color_palettes[0][4]); ?>",
                new_button_hover_color: "<?php echo esc_js($color_palettes[0][5]); ?>",
                new_text_selection_color: "<?php echo esc_js($color_palettes[0][6]); ?>",
                new_text_selection_bg_color: "<?php echo esc_js($color_palettes[0][7]); ?>",
                new_border_color: "<?php echo esc_js($color_palettes[0][8]); ?>",
                new_background_color: "<?php echo esc_js($color_palettes[0][9]); ?>",
                new_page_title_text_color: "<?php echo esc_js($color_palettes[0][10]); ?>",
                new_page_title_bg_color: "<?php echo esc_js($color_palettes[0][11]); ?>",
                new_bg_menu_dropdown_color: "<?php echo esc_js($color_palettes[0][12]); ?>"
            };

            console.log(colorValue);
            var jsonString = JSON.stringify(colorValue);
            localStorage.setItem('solace_step5_color', jsonString);
        });
        $('#color-3').click(function(e) {
            e.preventDefault();
            $('.colorlist .color').removeClass('active');
            $(this).addClass('active');
            
            var colorValue = { 
                new_base_color: "<?php echo esc_js($color_palettes[1][0]); ?>",
                new_heading_color: "<?php echo esc_js($color_palettes[1][1]); ?>",
                new_link_button_color: "<?php echo esc_js($color_palettes[1][2]); ?>",
                new_link_button_hover_color: "<?php echo esc_js($color_palettes[1][3]); ?>",
                new_button_color: "<?php echo esc_js($color_palettes[1][4]); ?>",
                new_button_hover_color: "<?php echo esc_js($color_palettes[1][5]); ?>",
                new_text_selection_color: "<?php echo esc_js($color_palettes[1][6]); ?>",
                new_text_selection_bg_color: "<?php echo esc_js($color_palettes[1][7]); ?>",
                new_border_color: "<?php echo esc_js($color_palettes[1][8]); ?>",
                new_background_color: "<?php echo esc_js($color_palettes[1][9]); ?>",
                new_page_title_text_color: "<?php echo esc_js($color_palettes[1][10]); ?>",
                new_page_title_bg_color: "<?php echo esc_js($color_palettes[1][11]); ?>",
                new_bg_menu_dropdown_color: "<?php echo esc_js($color_palettes[1][12]); ?>"
            };

            console.log(colorValue);
            var jsonString = JSON.stringify(colorValue);
            localStorage.setItem('solace_step5_color', jsonString);
        });
        $('#color-4').click(function(e) {
            e.preventDefault();
            $('.colorlist .color').removeClass('active');
            $(this).addClass('active');
            
            var colorValue = { 
                new_base_color: "<?php echo esc_js($color_palettes[2][0]); ?>",
                new_heading_color: "<?php echo esc_js($color_palettes[2][1]); ?>",
                new_link_button_color: "<?php echo esc_js($color_palettes[2][2]); ?>",
                new_link_button_hover_color: "<?php echo esc_js($color_palettes[2][3]); ?>",
                new_button_color: "<?php echo esc_js($color_palettes[2][4]); ?>",
                new_button_hover_color: "<?php echo esc_js($color_palettes[2][5]); ?>",
                new_text_selection_color: "<?php echo esc_js($color_palettes[2][6]); ?>",
                new_text_selection_bg_color: "<?php echo esc_js($color_palettes[2][7]); ?>",
                new_border_color: "<?php echo esc_js($color_palettes[2][8]); ?>",
                new_background_color: "<?php echo esc_js($color_palettes[2][9]); ?>",
                new_page_title_text_color: "<?php echo esc_js($color_palettes[2][10]); ?>",
                new_page_title_bg_color: "<?php echo esc_js($color_palettes[2][11]); ?>",
                new_bg_menu_dropdown_color: "<?php echo esc_js($color_palettes[2][12]); ?>"
            };

            console.log(colorValue);
            var jsonString = JSON.stringify(colorValue);
            localStorage.setItem('solace_step5_color', jsonString);
        });

        $('#font-1').addClass('active');
        
        $('#font-1').click(function(e) {
            e.preventDefault();
            $('.fontlist .font').removeClass('active');
            $(this).addClass('active');
            bodyFontFamily = '<?php echo esc_html( solace_extra_getGoogleFontsFamilyName($palette_font_scheme[1][0]) );?>';
            headingFontFamily = '<?php echo esc_html( solace_extra_getGoogleFontsFamilyName($palette_font_scheme[1][1]) );?>';

            var fontValue = { 
                new_solace_body_font_family: bodyFontFamily,
                new_solace_heading_font_family_general: headingFontFamily
            };
            console.log(fontValue);
            var jsonString = JSON.stringify(fontValue);
            localStorage.setItem('solace_step5_font', jsonString);

            
        });

        $('#font-2').click(function(e) {
            e.preventDefault();
            $('.fontlist .font').removeClass('active');
            $(this).addClass('active');
            bodyFontFamily = '<?php echo esc_html( solace_extra_getGoogleFontsFamilyName($palette_font_scheme[2][0]) );?>';
            headingFontFamily = '<?php echo esc_html( solace_extra_getGoogleFontsFamilyName($palette_font_scheme[2][1]) );?>';

            var fontValue = { 
                new_solace_body_font_family: bodyFontFamily,
                new_solace_heading_font_family_general: headingFontFamily
            };
            console.log(fontValue);
            var jsonString = JSON.stringify(fontValue);
            localStorage.setItem('solace_step5_font', jsonString);

            
        });

        $('#font-3').click(function(e) {
            e.preventDefault();
            $('.fontlist .font').removeClass('active');
            $(this).addClass('active');
            bodyFontFamily = '<?php echo esc_html( solace_extra_getGoogleFontsFamilyName($palette_font_scheme[3][0]) );?>';
            headingFontFamily = '<?php echo esc_html( solace_extra_getGoogleFontsFamilyName($palette_font_scheme[3][1]) );?>';

            var fontValue = { 
                new_solace_body_font_family: bodyFontFamily,
                new_solace_heading_font_family_general: headingFontFamily
            };
            console.log(fontValue);
            var jsonString = JSON.stringify(fontValue);
            localStorage.setItem('solace_step5_font', jsonString);
            
        });

        $('#font-4').click(function(e) {
            e.preventDefault();
            $('.fontlist .font').removeClass('active');
            $(this).addClass('active');
            bodyFontFamily = '<?php echo esc_html( solace_extra_getGoogleFontsFamilyName($palette_font_scheme[4][0]) );?>';
            headingFontFamily = '<?php echo esc_html( solace_extra_getGoogleFontsFamilyName($palette_font_scheme[4][1]) );?>';

            var fontValue = { 
                new_solace_body_font_family: bodyFontFamily,
                new_solace_heading_font_family_general: headingFontFamily
            };
            console.log(fontValue);
            var jsonString = JSON.stringify(fontValue);
            localStorage.setItem('solace_step5_font', jsonString);
            
        });

        $('#font-5').click(function(e) {
            e.preventDefault();
            $('.fontlist .font').removeClass('active');
            $(this).addClass('active');
            bodyFontFamily = '<?php echo esc_html( solace_extra_getGoogleFontsFamilyName($palette_font_scheme[5][0]) );?>';
            headingFontFamily = '<?php echo esc_html( solace_extra_getGoogleFontsFamilyName($palette_font_scheme[5][1]) );?>';
            var fontValue = { 
                new_solace_body_font_family: bodyFontFamily,
                new_solace_heading_font_family_general: headingFontFamily
            };
            console.log(fontValue);
            var jsonString = JSON.stringify(fontValue);
            localStorage.setItem('solace_step5_font', jsonString);
            
        });

        $('#font-6').click(function(e) {
            e.preventDefault();
            $('.fontlist .font').removeClass('active');
            $(this).addClass('active');
            bodyFontFamily = '<?php echo esc_html( solace_extra_getGoogleFontsFamilyName($palette_font_scheme[6][0]) );?>';
            headingFontFamily = '<?php echo esc_html( solace_extra_getGoogleFontsFamilyName($palette_font_scheme[6][1]) );?>';

            var fontValue = { 
                new_solace_body_font_family: bodyFontFamily,
                new_solace_heading_font_family_general: headingFontFamily
            };
            console.log(fontValue);
            var jsonString = JSON.stringify(fontValue);
            localStorage.setItem('solace_step5_font', jsonString);
            
        });

        $('#font-7').click(function(e) {
            e.preventDefault();
            $('.fontlist .font').removeClass('active');
            $(this).addClass('active');
            bodyFontFamily = '<?php echo esc_html( solace_extra_getGoogleFontsFamilyName($palette_font_scheme[7][0]) );?>';
            headingFontFamily = '<?php echo esc_html( solace_extra_getGoogleFontsFamilyName($palette_font_scheme[7][1]) );?>';
            var fontValue = { 
                new_solace_body_font_family: bodyFontFamily,
                new_solace_heading_font_family_general: headingFontFamily
            };
            console.log(fontValue);
            var jsonString = JSON.stringify(fontValue);
            localStorage.setItem('solace_step5_font', jsonString);
                
        });

        $('#font-8').click(function(e) {
            e.preventDefault();
            $('.fontlist .font').removeClass('active');
            $(this).addClass('active');
            bodyFontFamily = '<?php echo esc_html( solace_extra_getGoogleFontsFamilyName($palette_font_scheme[8][0]) );?>';
            headingFontFamily = '<?php echo esc_html( solace_extra_getGoogleFontsFamilyName($palette_font_scheme[8][1]) );?>';

            var fontValue = { 
                new_solace_body_font_family: bodyFontFamily,
                new_solace_heading_font_family_general: headingFontFamily
            };
            console.log(fontValue);
            var jsonString = JSON.stringify(fontValue);
            localStorage.setItem('solace_step5_font', jsonString);
            
        });

        // Active / Disable style button continue
        $('.box-required-plugin-elementor input, .box-required-plugin-ecommerce input').change(function() {
            let requiredPluginElementor = $('.box-required-plugin-elementor input').prop('checked');
            let requiredPluginWoocommerce = $('.box-required-plugin-ecommerce input').prop('checked');

            // Input checkbox Elementor && Ecommerce found.
            if ( $('.box-required-plugin-elementor input').length > 0 && $('.box-required-plugin-ecommerce input').length > 0 ) {

                // Checkbox CHECKED (2 checked) Elementor && Ecommerce
                if (requiredPluginElementor && requiredPluginWoocommerce) {
                    $('#solace-extra-continue').addClass('active');
                }
                
                // Checkbox Elementor UNCHECKED
                if (!requiredPluginElementor) {
                    $('#solace-extra-continue').removeClass('active');
                }

                // Checkbox Ecommerce UNCHECKED
                if (!requiredPluginWoocommerce) {
                    $('#solace-extra-continue').removeClass('active');
                }

            } else if ( $('.box-required-plugin-elementor input').length > 0 ) { // Input checkbox Elementor found.

                // Checkbox CHECKED (1 checked) Elementor
                if (requiredPluginElementor) {
                    $('#solace-extra-continue').addClass('active');
                } else {
                    $('#solace-extra-continue').removeClass('active');
                }
            
            } else if ( $('.box-required-plugin-ecommerce input').length > 0 ) { // Input checkbox Ecommerce found.

                // Checkbox CHECKED (1 checked) Ecommerce
                if (requiredPluginWoocommerce) {
                    $('#solace-extra-continue').addClass('active');
                } else {
                    $('#solace-extra-continue').removeClass('active');
                }

            }
        });

        $('#solace-extra-continue').click(function(e) {

            let requiredPluginElementor = $('.box-required-plugin-elementor input').prop('checked');
            let requiredPluginWoocommerce = $('.box-required-plugin-ecommerce input').prop('checked');

            // Active page builder + ecommerce
            if (required_plugin.plugins.page_builder && required_plugin.plugins.ecommerce) {

                // Input checkbox Elementor found.
                if ( $('.box-required-plugin-elementor input').length > 0 ) {
                    // Input checkbox Elementor not checked.
                    if ( ! requiredPluginElementor ) {
                        swal({
                            title: "Warning!",
                            text: "This template needs the required plugins to continue the import process. Please check the checkbox to Install and activate the required plugins.",
                            icon: "warning"
                        });
                        return;
                    }
                }

                // Input checkbox Woocommerce found.
                if ( $('.box-required-plugin-ecommerce input').length > 0 ) {
                    // Input checkbox Woocommerce not checked.
                    if ( ! requiredPluginWoocommerce ) {
                        swal({
                            title: "Warning!",
                            text: "This template needs the required plugins to continue the import process. Please check the checkbox to Install and activate the required plugins.",
                            icon: "warning"
                        });
                        return;
                    }
                }

                if ( typeof requiredPluginElementor !== 'undefined' && typeof requiredPluginWoocommerce !== 'undefined' ) {
                    if (!requiredPluginElementor || !requiredPluginWoocommerce) {
                        swal({
                            title: "Warning!",
                            text: "This template needs the required plugins to continue the import process. Please check the checkbox to Install and activate the required plugins.",
                            icon: "warning"
                        });
                        return;
                    }
                }

            // Active page builder
            } else if (required_plugin.plugins.page_builder) {

                // Input checkbox Elementor found.
                if ( $('.box-required-plugin-elementor input').length > 0 ) {
                    // Input checkbox Elementor not checked.
                    if ( ! requiredPluginElementor ) {
                        swal({
                            title: "Warning!",
                            text: "This template needs the required plugins to continue the import process. Please check the checkbox to Install and activate the required plugins.",
                            icon: "warning"
                        });
                        return;
                    }
                }                

                if ( typeof requiredPluginElementor !== 'undefined' ) {
                    if (!requiredPluginElementor) {
                        swal({
                            title: "Warning!",
                            text: "This template needs the required plugins to continue the import process. Please check the checkbox to Install and activate the required plugins.",
                            icon: "warning"
                        });
                        return;
                    }
                }
            }

            // if (attachment_id){
            //     setLogoURL(attachment_id);
            // }

            var solaceStep6 = localStorage.getItem('solaceStep6');
            console.log ('done step6? :'+solaceStep6);
            if (solaceStep6 === 'success') {
                const demoType = getParameterByName('type');
                const demoUrl = getParameterByName('dataUrl');
                const demoName = getParameterByName('demo');

                // Function to get the current time
                function getCurrentTime() {
                    var now = new Date();
                    return now.toLocaleString(); // Using local date and time format
                }

                if (localStorage.getItem('solaceDuration')) {
                    // If it exists, remove it first
                    localStorage.removeItem('solaceDuration');
                }

                // Get the time when the button is clicked
                var currentTime = getCurrentTime();

                // Save the time value to local storage with the name 'solaceDuration'
                localStorage.setItem('solaceDuration', currentTime);

                // Check if the 'solaceListDemo' key already exists in local storage
                if (localStorage.getItem('solaceRemoveDataDemo')) {
                    // Retrieve the existing value
                    let existingValue = localStorage.getItem('solaceRemoveDataDemo');

                    // Combine the existing value with the new data, separated by a comma
                    let combinedValue = existingValue + ',' + demoName;

                    // Save the combined value back to local storage
                    localStorage.setItem('solaceRemoveDataDemo', combinedValue);
                } else {
                    // If 'solaceRemoveDataDemo' doesn't exist in local storage, create it and store the new data
                    localStorage.setItem('solaceRemoveDataDemo', demoName);
                }                

                window.location = pluginUrl.admin_url + 'admin.php?page=dashboard-progress&type=' + demoType + '&url=' + demoUrl +'&demo=' + demoName + '&timestamp=' + new Date().getTime();

            } else {
                const demoType = getParameterByName('type');
                const demoUrl = getParameterByName('dataUrl');
                const demoName = getParameterByName('demo');
                window.location = pluginUrl.admin_url + 'admin.php?page=dashboard-step6&type=' + demoType + '&url=' + demoUrl +'&demo=' + demoName + '&timestamp=' + new Date().getTime();
            }

        });
    });

</script>
