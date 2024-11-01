<?php defined( 'ABSPATH' ) || exit;

if ( empty( $_COOKIE['solace_page_access'] ) ) {
    $url = get_admin_url() . 'admin.php?page=dashboard-starter-templates&type=elementor';
    wp_redirect( $url, 301 ); 
    exit;
}

$customizer_link = admin_url('customize.php'); 
?>
<div class="wrap">
    <?php require_once plugin_dir_path(dirname(__FILE__)) . 'partials/header.php'; ?>
    <section class="progress-import">
        <div class="mycontainer">
            <div class="boxes">
                <h1><?php esc_html_e('We are building your website', 'solace-extra'); ?></h1>
                <div class="box-step-import">
                    <span class="step-import">
                        <?php esc_html_e('Installing required theme, plugins, forms, etc', 'solace-extra'); ?>
                    </span>
                    <span class="percent">
                        <?php esc_html_e('0%', 'solace-extra'); ?>
                    </span>
                </div>
                <div class="boxes-bar">
                    <div class="bar bar1">
                        <div class="progress"></div>
                    </div>
                    <div class="bar bar2">
                        <div class="progress"></div>
                    </div>
                    <div class="bar bar4">
                        <div class="progress"></div>
                    </div>
                    <div class="bar progress"></div>
                </div>
                <span class="info-import">
                    <?php esc_html_e('Installing & Activated Theme & Plugins...','solace-extra');?>
                    <?php //_e('Importing content...', 'solace-extra'); ?>
                </span>
                <div class="boxes-loading">
                    <dotlottie-player src="<?php echo esc_url( SOLACE_EXTRA_ASSETS_URL . 'images/import/import3.json' ); ?>" background="transparent" speed="1" style="width: 300px; height: 300px;" loop autoplay></dotlottie-player>
                </div>

                <div class="box-did-you-know">
                    <div class="box-title">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_2323_2082)">
                            <path d="M8.99951 2.10901C8.70828 2.10901 8.47217 2.34512 8.47217 2.63635C8.47217 2.92759 8.70828 3.1637 8.99951 3.1637C10.7445 3.1637 12.1641 4.58309 12.1641 6.32776C12.1641 6.61899 12.4002 6.8551 12.6914 6.8551C12.9827 6.8551 13.2188 6.61899 13.2188 6.32776C13.2188 4.00154 11.326 2.10901 8.99951 2.10901Z" fill="#FF9100"/>
                            <path d="M7.64083 0.141691C5.25829 0.639539 3.32646 2.55288 2.81813 4.95022C2.38891 6.97459 2.91784 9.00697 4.26928 10.5262C4.9299 11.2689 5.30874 12.2491 5.30874 13.2184V14.2731C5.30874 14.9748 5.76813 15.571 6.40186 15.7774C6.61026 17.0088 7.67901 17.9996 9.00015 17.9996C10.3209 17.9996 11.39 17.0091 11.5984 15.7774C12.2322 15.571 12.6916 14.9748 12.6916 14.2731V13.2184C12.6916 12.2472 13.0717 11.2787 13.7618 10.4913C14.772 9.33892 15.3283 7.86025 15.3283 6.32779C15.3283 2.33527 11.6738 -0.700863 7.64083 0.141691ZM9.00015 16.945C8.32227 16.945 7.72792 16.4785 7.50843 15.8516H10.4918C10.2724 16.4785 9.67803 16.945 9.00015 16.945ZM11.6369 14.2731C11.6369 14.5639 11.4003 14.8004 11.1095 14.8004H6.89078C6.6 14.8004 6.36343 14.5639 6.36343 14.2731V13.7458H11.6369V14.2731ZM12.9687 9.7962C12.2332 10.6353 11.7807 11.6471 11.6659 12.6911H6.33453C6.21985 11.6465 5.76852 10.6248 5.05738 9.82527C3.93104 8.55908 3.49092 6.86191 3.8499 5.169C4.26893 3.19273 5.87908 1.58732 7.85659 1.17412C11.2324 0.468644 14.2736 3.00383 14.2736 6.32779C14.2736 7.60438 13.8102 8.83615 12.9687 9.7962Z" fill="#FF9100"/>
                            <path d="M1.58203 6.32776H0.527344C0.236109 6.32776 0 6.56387 0 6.8551C0 7.14634 0.236109 7.38245 0.527344 7.38245H1.58203C1.87327 7.38245 2.10938 7.14634 2.10938 6.8551C2.10938 6.56387 1.87327 6.32776 1.58203 6.32776Z" fill="#FF9100"/>
                            <path d="M1.80029 3.69106L1.05453 2.94529C0.848615 2.73935 0.514701 2.73935 0.308756 2.94529C0.102811 3.15124 0.102811 3.48512 0.308756 3.69106L1.05453 4.43683C1.26044 4.64278 1.59435 4.64281 1.80029 4.43683C2.00624 4.23089 2.00624 3.89701 1.80029 3.69106Z" fill="#FF9100"/>
                            <path d="M1.80029 9.27342C1.59435 9.06747 1.26044 9.06747 1.05453 9.27342L0.308756 10.0192C0.102811 10.2251 0.102811 10.559 0.308756 10.765C0.514666 10.9709 0.84858 10.9709 1.05453 10.765L1.80029 10.0192C2.00624 9.81324 2.00624 9.47936 1.80029 9.27342Z" fill="#FF9100"/>
                            <path d="M17.4727 6.32776H16.418C16.1267 6.32776 15.8906 6.56387 15.8906 6.8551C15.8906 7.14634 16.1267 7.38245 16.418 7.38245H17.4727C17.7639 7.38245 18 7.14634 18 6.8551C18 6.56387 17.7639 6.32776 17.4727 6.32776Z" fill="#FF9100"/>
                            <path d="M17.6909 2.94529C17.485 2.73935 17.1511 2.73935 16.9452 2.94529L16.1994 3.69106C15.9934 3.89701 15.9934 4.23089 16.1994 4.43683C16.4053 4.64274 16.7392 4.64278 16.9452 4.43683L17.6909 3.69106C17.8969 3.48512 17.8969 3.15124 17.6909 2.94529Z" fill="#FF9100"/>
                            <path d="M17.6909 10.0192L16.9452 9.27342C16.7392 9.06747 16.4053 9.06747 16.1994 9.27342C15.9934 9.47936 15.9934 9.81324 16.1994 10.0192L16.9452 10.765C17.1511 10.9709 17.485 10.9709 17.6909 10.765C17.8969 10.559 17.8969 10.2251 17.6909 10.0192Z" fill="#FF9100"/>
                            </g>
                            <defs>
                            <clipPath id="clip0_2323_2082">
                            <rect width="18" height="18" fill="white"/>
                            </clipPath>
                            </defs>
                        </svg>
                        <span class="title"><?php esc_html_e( 'Did you know?', 'solace-extra' ); ?></span>
                    </div>
                    <div class="box-desc">
                        <span class="desc"></span>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
