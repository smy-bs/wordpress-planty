<?php
/*
Plugin Name: Contact Form 7 Captcha
Description: Add No CAPTCHA reCAPTCHA to Contact Form 7 using [cf7sr-simple-recaptcha] shortcode
Version: 0.1.2
Author: 247wd
*/

$cf7sr_key = get_option('cf7sr_key');
$cf7sr_secret = get_option( 'cf7sr_secret' );
if (!empty($cf7sr_key) && !empty($cf7sr_secret) && !is_admin()) {
    function enqueue_cf7sr_script() {
        global $cf7sr;
        if (!$cf7sr) {
            return;
        }
        $cf7sr_script_url = 'https://www.google.com/recaptcha/api.js?onload=cf7srLoadCallback&render=explicit';
        $cf7sr_key = get_option( 'cf7sr_key' );
        ?>
        <script type="text/javascript">
            var widgetIds = [];
            var cf7srLoadCallback = function() {
                var cf7srWidgets = document.querySelectorAll('.cf7sr-g-recaptcha');
                for (var i = 0; i < cf7srWidgets.length; ++i) {
                    var cf7srWidget = cf7srWidgets[i];
                    var widgetId = grecaptcha.render(cf7srWidget.id, {
                        'sitekey' : <?php echo wp_json_encode($cf7sr_key); ?>
                    });
                    widgetIds.push(widgetId);
                }
            };
            (function($) {
                $('.wpcf7').on('wpcf7invalid wpcf7mailsent invalid.wpcf7 mailsent.wpcf7', function() {
                    for (var i = 0; i < widgetIds.length; i++) {
                        grecaptcha.reset(widgetIds[i]);
                    }
                });
            })(jQuery);
        </script>
        <script src="<?php echo esc_url($cf7sr_script_url); ?>" async defer></script>
        <?php
    }
    add_action('wp_footer', 'enqueue_cf7sr_script');

    function cf7sr_wpcf7_form_elements($form) {
        $form = do_shortcode($form);
        return $form;
    }
    add_filter('wpcf7_form_elements', 'cf7sr_wpcf7_form_elements');

    function cf7sr_shortcode($atts) {
        global $cf7sr;
        $cf7sr = true;
        $cf7sr_key = get_option('cf7sr_key');
        return '<div id="cf7sr-' . uniqid() . '" class="cf7sr-g-recaptcha" data-sitekey="' . esc_attr($cf7sr_key)
            . '"></div><span class="wpcf7-form-control-wrap cf7sr-recaptcha" data-name="cf7sr-recaptcha"><input type="hidden" name="cf7sr-recaptcha" value="" class="wpcf7-form-control"></span>';
    }
    add_shortcode('cf7sr-simple-recaptcha', 'cf7sr_shortcode');

    function cf7sr_verify_recaptcha($result) {
        if (! class_exists('WPCF7_Submission')) {
            return $result;
        }

        $_wpcf7 = ! empty($_POST['_wpcf7']) ? absint($_POST['_wpcf7']) : 0;
        if (empty($_wpcf7)) {
            return $result;
        }

        $submission = WPCF7_Submission::get_instance();
        $data = $submission->get_posted_data();

        $cf7_text = do_shortcode( '[contact-form-7 id="' . $_wpcf7 . '"]' );
        $cf7sr_key = get_option( 'cf7sr_key' );
        if (false === strpos($cf7_text, $cf7sr_key)) {
            return $result;
        }

        $message = get_option('cf7sr_message');
        if (empty($message)) {
            $message = 'Invalid captcha';
        }

        if (empty($data['g-recaptcha-response'])) {
            $result->invalidate(array('type' => 'captcha', 'name' => 'cf7sr-recaptcha'), $message);
            return $result;
        }

        $cf7sr_secret = get_option('cf7sr_secret');
        $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $cf7sr_secret . '&response=' . $data['g-recaptcha-response'];
        $request = wp_remote_get($url);
        $body = wp_remote_retrieve_body($request);
        $response = json_decode($body);
        if (!(isset ($response->success) && 1 == $response->success)) {
            $result->invalidate(array('type' => 'captcha', 'name' => 'cf7sr-recaptcha'), $message);
        }

        return $result;
    }
    add_filter('wpcf7_validate', 'cf7sr_verify_recaptcha', 20, 2);
}

if (is_admin()) {
    function cf7sr_add_action_links($links) {
        array_unshift($links , '<a href="' . admin_url( 'options-general.php?page=cf7sr_edit' ) . '">Settings</a>');
        array_unshift($links , '<a target="_blank" style="color: red;" href="http://www.cf7captcha.com">Upgrade To Pro Free</a>');
        return $links;
    }
    add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'cf7sr_add_action_links', 10, 2 );

    function cf7sr_adminhtml() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        if (! class_exists('WPCF7_Submission')) {
            echo '<p>To use <strong>Contact Form 7 Captcha</strong> please update <strong>Contact Form 7</strong> plugin as current version is not supported.</p>';
            return;
        }
        if (
            ! empty ($_POST['update'])
            && ! empty($_POST['cf7sr_nonce'])
            && wp_verify_nonce($_POST['cf7sr_nonce'],'cf7sr_update_settings' )
        ) {
            $cf7sr_key = ! empty ($_POST['cf7sr_key']) ? sanitize_text_field($_POST['cf7sr_key']) : '';
            update_option('cf7sr_key', $cf7sr_key);

            $cf7sr_secret = ! empty ($_POST['cf7sr_secret']) ? sanitize_text_field($_POST['cf7sr_secret']) : '';
            update_option('cf7sr_secret', $cf7sr_secret);

            $cf7sr_message = ! empty ($_POST['cf7sr_message']) ? sanitize_text_field($_POST['cf7sr_message']) : '';
            update_option('cf7sr_message', $cf7sr_message);

            $updated = 1;
        } else {
            $cf7sr_key = get_option('cf7sr_key');
            $cf7sr_secret = get_option('cf7sr_secret');
            $cf7sr_message = get_option('cf7sr_message');
        }
        ?>
        <div class="cf7sr-wrap" style="font-size: 15px; background: #fff; border: 1px solid #e5e5e5; margin-top: 20px; padding: 20px; margin-right: 20px;">
            <h2>
                Captcha Settings
                <a style="text-decoration: none" target="_blank" href="https://www.paypal.me/cf7captcha">
                    <img style="vertical-align:middle;display:inline-block;width:100px;margin-left:5px;" src="<?php echo plugin_dir_url( __FILE__ ); ?>donate.png" alt="Donate">
                </a>
                <a target="_blank" style="font-size:14px;color:#d54e21;border:1px solid #d54e21;padding:5px;text-decoration:none;margin-left:4px;border-radius:3px;" href="http://www.cf7captcha.com">Upgrade To Pro Free Limited Offer</a>
            </h2>
            This plugin implements "I'm not a robot" checkbox.<br><br>
            To add Recaptcha to CF7 form, add <strong>[cf7sr-simple-recaptcha]</strong> in your form ( preferable above submit button )<br>
            <form action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>" method="POST">
                <input type="hidden" value="1" name="update">
                <?php wp_nonce_field( 'cf7sr_update_settings', 'cf7sr_nonce' ); ?>
                <ul>
                    <li><input type="text" style="width: 370px;" value="<?php echo esc_attr($cf7sr_key); ?>" name="cf7sr_key"> Site key</li>
                    <li><input type="text" style="width: 370px;" value="<?php echo esc_attr($cf7sr_secret); ?>" name="cf7sr_secret"> Secret key</li>
                    <li><input type="text" style="width: 370px;" value="<?php echo esc_attr($cf7sr_message); ?>" name="cf7sr_message"> Invalid captcha error message</li>
                </ul>
                <input type="submit" class="button-primary" value="Save Settings">
            </form><br>
            You can generate Site key and Secret key <strong><a target="_blank" href="https://www.google.com/recaptcha/admin">here</a></strong><br>
            <strong style="color:red">Choose reCAPTCHA v2 -> Checkbox</strong><br>
            <a target="_blank" href="https://www.google.com/recaptcha/admin"><img src="<?php echo plugin_dir_url( __FILE__ ); ?>captcha.jpg" width="400" alt="captcha" /></a><br><br>
            <?php if (!empty($updated)): ?>
                <p>Settings were updated successfully!</p>
            <?php endif; ?>
        </div>
        <div class="cf7sr-wrap" style="font-size: 15px; background: #fff; border: 1px solid #e5e5e5; margin-top: 20px; padding: 20px; margin-right: 20px;">
            <strong>Pro Version features: </strong>
            <ul>
                <li>WPML and POLYLANG language integration</li>
                <li>Render captcha widget in a specific language, choose from 70 languages.</li>
                <li>Switch between the color theme of the widget, light or dark</li>
                <li>Switch between the type of the widget, image or audio</li>
                <li>Switch between the size of the widget, normal or compact</li>
            </ul>
        </div>
        <?php
    }

    function cf7sr_addmenu() {
        add_submenu_page (
            'options-general.php',
            'CF7 Simple Recaptcha',
            'CF7 Simple Recaptcha',
            'manage_options',
            'cf7sr_edit',
            'cf7sr_adminhtml'
        );
    }
    add_action('admin_menu', 'cf7sr_addmenu');
}
