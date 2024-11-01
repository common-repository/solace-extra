<?php defined( 'ABSPATH' ) || exit; ?>
<?php $customizer_link = admin_url('customize.php'); ?>
<?php $myadmin = site_url(); ?>

<?php

if ( empty( $_COOKIE['solace_page_access'] ) ) {
    $url = get_admin_url() . 'admin.php?page=dashboard-starter-templates&type=elementor';
    wp_redirect( $url, 301 ); 
    exit;
}

?>

<div class="wrap-step6 wrap">
    <?php require_once plugin_dir_path(dirname(__FILE__)) . 'partials/header.php'; ?>
    <div class="box-optin">
        <span class="step6_title"><?php esc_html_e('Okay, just one last step ', 'solace-extra'); ?></span>
        <span class="step6_desc"><?php esc_html_e('Tell us a litle bit about yourself', 'solace-extra'); ?></span>
        <form>
            <div class='input'>
                <input type='text' class='firstname' name='firstname' placeholder='Your First Name' />
                <input type="email" class='email' name='email' placeholder='Your Work Email' />
            </div>
            <div class='checkbox_agreement'>
                <input type="checkbox" id="agreement" name="agreement" value="1" required checked>
                <label for="agreement"><?php esc_html_e('I agree to receive your newsletters and accept the data privacy statement.', 'solace-extra'); ?></label>
            </div>
            <label class='note' style='display: none;'><?php esc_html_e('This will delete your previous WordPress starter template.', 'solace-extra'); ?></label>
            <button class="step6_submit"  id="submit-button" type="button">
                <?php esc_html_e('Submit & Build My Website', 'solace-extra'); ?>
            </button>
            <button class="skip-this-step" type="button">
                <?php esc_html_e('Skip this step', 'solace-extra'); ?>
            </button>
        </form>
    </div>
</div>
