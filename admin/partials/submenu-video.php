<?php defined( 'ABSPATH' ) || exit; ?>
<?php $customizer_link = admin_url('customize.php'); ?>
<div class="wrap">
    <?php require_once plugin_dir_path( dirname( __FILE__ ) ) . 'partials/header.php'; ?>
    <section class="video">
        <div class="mycontainer">
            <div class="left">
                <div class="iframe-container">
                    <iframe width="674" height="377" src="https://www.youtube.com/embed/NdGfSifOPu4" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
            <div class="right">
                <h2><?php esc_html_e( 'Get Started With Our Ready Pre-Made Templates', 'solace-extra' ); ?></h2>

                <p class="desc"><?php esc_html_e( 'Kickstart your projects effortlessly with our ready pre-made templates. Perfect for any task, from presentations to websites, our user-friendly designs ensure high-quality results with minimal effort. Explore our diverse collection and find your ideal template today.', 'solace-extra' ); ?></p>

                <div class="boxes-image">
                    <img src="<?php echo esc_url( SOLACE_EXTRA_ASSETS_URL . 'images/dashboard/start-template.png' ); ?>" />
                    <img src="<?php echo esc_url( SOLACE_EXTRA_ASSETS_URL . 'images/dashboard/customizer.png' ); ?>" />
                </div>

                <div class="box-btn">
                    <a href="<?php echo esc_url($myadmin . '/wp-admin/admin.php?page=dashboard-starter-templates&type=elementor'); ?>"><?php esc_html_e( 'START NOW', 'solace-extra' ); ?></a>
                </div>

                <img class="decor" src="<?php echo esc_url( SOLACE_EXTRA_ASSETS_URL . 'images/video/decor.png' ); ?>" />
            </div>
        </div>
    </section>
    <footer class="bottom">
        <div class="mycontainer">
            <div class="box left">
                <a href="<?php echo esc_url($myadmin . '/wp-admin/admin.php?page=dashboard'); ?>">
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

            <div class="box right">
                <a href="<?php echo esc_url($myadmin . '/wp-admin/admin.php?page=dashboard-starter-templates&type=elementor'); ?>">
                    <span><?php esc_html_e('Next', 'solace-extra'); ?></span>
                    <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"/></svg>
                </a>
            </div>
        </div>
    </footer>    
</div>
