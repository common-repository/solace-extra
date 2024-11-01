<?php defined( 'ABSPATH' ) || exit;

if ( empty( $_COOKIE['solace_page_access'] ) ) {
    $url = get_admin_url() . 'admin.php?page=dashboard-starter-templates&type=elementor';
    wp_redirect( $url, 301 ); 
    exit;
}

$customizer_link = admin_url('customize.php'); 
?>

<script type="text/javascript">
    var ajaxurl = "<?php echo esc_url( admin_url('admin-ajax.php') ); ?>";
</script>
<script>

    jQuery(document).ready(function($) {

        // =================== 2nd TRYING TO COMBINE AJAX FONT AND COLOR ===================
        var fontValue = localStorage.getItem('solace_step5_font');
        var new_solace_body_font = null;
        var new_solace_heading_font = null;

        if (fontValue) {
            try {
                var arrayFont = JSON.parse(fontValue);
                new_solace_body_font = arrayFont.new_solace_body_font_family || null;
                new_solace_heading_font = arrayFont.new_solace_heading_font_family_general || null;
            } catch (e) {
                console.error('Error parsing solace_step5_font:', e);
            }
        }

        var colorValue = localStorage.getItem('solace_step5_color');
        var new_base_color = null;
        var new_heading_color = null;
        var new_link_button_color = null;
        var new_link_button_hover_color = null;
        var new_button_color = null;
        var new_button_hover_color = null;
        var new_text_selection_color = null;
        var new_text_selection_bg_color = null;
        var new_border_color = null;
        var new_background_color = null;
        var new_page_title_text_color = null;
        var new_page_title_bg_color = null;
        var new_bg_menu_dropdown_color = null;

        if (colorValue) {
            try {
                var arrayColor = JSON.parse(colorValue);
                new_base_color = arrayColor.new_base_color || null;
                new_heading_color = arrayColor.new_heading_color || null;
                new_link_button_color = arrayColor.new_link_button_color || null;
                new_link_button_hover_color = arrayColor.new_link_button_hover_color || null;
                new_button_color = arrayColor.new_button_color || null;
                new_button_hover_color = arrayColor.new_button_hover_color || null;
                new_text_selection_color = arrayColor.new_text_selection_color || null;
                new_text_selection_bg_color = arrayColor.new_text_selection_bg_color || null;
                new_border_color = arrayColor.new_border_color || null;
                new_background_color = arrayColor.new_background_color || null;
                new_page_title_text_color = arrayColor.new_page_title_text_color || null;
                new_page_title_bg_color = arrayColor.new_page_title_bg_color || null;
                new_bg_menu_dropdown_color = arrayColor.new_bg_menu_dropdown_color || null;
            } catch (e) {
                console.error('Error parsing solace_step5_color:', e);
            }
        }

        console.log('BodyFont:', new_solace_body_font);
        console.log('HeadingFont:', new_solace_heading_font);
        console.log('BaseColor:', new_base_color);
        console.log('HeadingColor:', new_heading_color);

        var ajaxData = {
            action: 'update_solace_font_and_color',
            nonce: ajax_object.nonce,
        };

        // Hanya menambahkan nilai yang tidak null ke dalam objek ajaxData
        if (new_solace_body_font) ajaxData.new_solace_body_font_family = new_solace_body_font;
        if (new_solace_heading_font) ajaxData.new_solace_heading_font_family_general = new_solace_heading_font;
        if (new_base_color) ajaxData.new_base_color = new_base_color;
        if (new_heading_color) ajaxData.new_heading_color = new_heading_color;
        if (new_link_button_color) ajaxData.new_link_button_color = new_link_button_color;
        if (new_link_button_hover_color) ajaxData.new_link_button_hover_color = new_link_button_hover_color;
        if (new_button_color) ajaxData.new_button_color = new_button_color;
        if (new_button_hover_color) ajaxData.new_button_hover_color = new_button_hover_color;
        if (new_text_selection_color) ajaxData.new_text_selection_color = new_text_selection_color;
        if (new_text_selection_bg_color) ajaxData.new_text_selection_bg_color = new_text_selection_bg_color;
        if (new_border_color) ajaxData.new_border_color = new_border_color;
        if (new_background_color) ajaxData.new_background_color = new_background_color;
        if (new_page_title_text_color) ajaxData.new_page_title_text_color = new_page_title_text_color;
        if (new_page_title_bg_color) ajaxData.new_page_title_bg_color = new_page_title_bg_color;
        if (new_bg_menu_dropdown_color) ajaxData.new_bg_menu_dropdown_color = new_bg_menu_dropdown_color;

        $.post(ajax_object.ajax_url, ajaxData)
            .done(function(response) {
                $('.cong_button').addClass('done_fonts');
                $('.cong_button').addClass('done_colors');
                console.log(response);
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX request failed:', textStatus, errorThrown);

                if (jqXHR.responseText) {
                    console.log('Error response:', jqXHR.responseText);
                }
            });


        // Get the 'solaceDuration' value from local storage
        var solaceDuration = localStorage.getItem('solaceDuration');

        // Check if solaceDuration is not null or undefined
        if (solaceDuration) {
            // Convert solaceDuration to a Date object
            var startTime = new Date(solaceDuration);

            // Get the current time as the endTime
            var endTime = new Date();

            // Calculate the time difference in milliseconds
            var timeDifference = endTime - startTime;

            // Convert the time difference from milliseconds to seconds
            var timeDifferenceInSeconds = timeDifference / 1000;

            // Check if the time difference is at least 60 seconds
            if (timeDifferenceInSeconds >= 60) {
                // Calculate and display minutes and remaining seconds
                var minutes = Math.floor(timeDifferenceInSeconds / 60);
                var seconds = Math.floor(timeDifferenceInSeconds % 60);
                console.log(minutes + " menit " + seconds + " detik");
            } else {
                // Display the time difference in seconds
                console.log(timeDifferenceInSeconds + " detik");
            }

            // Remove information imported
            localStorage.removeItem('solaceRemoveImported');
        }
    });

</script>

<?php $myadmin = site_url(); ?>
<style>
    div.cong_button {
        display: none!important;
    }

    div.cong_button.done_fonts.done_colors {
        display: flex!important;
    }
</style>
<div class="wrap wrap-congratulations">
    <?php require_once plugin_dir_path(dirname(__FILE__)) . 'partials/header.php'; ?>
    <div class="box-congrat">
        <span class="cong_title"><?php esc_html_e('Congratulations!', 'solace-extra'); ?></span>
        <span class="cong_desc"><?php esc_html_e('Your import is complete. You can check your website now.', 'solace-extra'); ?></span>
        <div class="cong_border">
            <img src="<?php echo esc_url( SOLACE_EXTRA_ASSETS_URL . 'images/congratulations/border-congratulations.png' ); ?>" />
        </div>
        <span class="done"><?php esc_html_e( 'done', 'solace-extra' ); ?></span>
        <!--span class="cong_done">Done</span-->
        <a class="button" target="_blank" href="<?php echo esc_url( site_url() ); ?>"><div class="cong_button"><?php esc_html_e( 'CHECK WEBSITE', 'solace-extra' ); ?></div></a>
    </div>
</div>

