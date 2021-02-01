<?php
/*
Plugin Name: Cookie Consent Plugin
Description: Easily configurable plugin for cookie consent.
Author: Sandstream Development
Version: 1.0.0
Author URI: https://sanddev.com/
*/

class Cookie_Consent_Plugin
{
    function __construct()
    {
        add_action('admin_menu', array(
            $this,
            'create_plugin_settings'
        ));
        add_action('admin_init', array(
            $this,
            'setup_sections'
        ));
        add_action('admin_init', array(
            $this,
            'setup_fields'
        ));
        add_action('wp_enqueue_scripts', array(
            $this,
            'add_cookie_consent_js'
        ));
        add_action('wp_enqueue_scripts', array(
            $this,
            'add_cookie_consent_config_js'
        ));
        add_action('wp_enqueue_scripts', array(
            $this,
            'add_cookie_consent_css'
        ));
        add_action('wp_enqueue_scripts', array(
            $this,
            'add_cookie_consent_style_css'
        ));
        add_action('wp_head', array(
            $this,
            'add_custom_styles'
        ));

        register_deactivation_hook(__FILE__, array($this, 'cookie_consent_uninstall'));
    }


    function cookie_consent_uninstall()
    {
        delete_option('cc_cookie_name');
        delete_option('cc_message');
        delete_option('cc_gtm_id');
        delete_option('cc_ga_id');
        delete_option('cc_domain');
        delete_option('cc_privacy_policy_link');
        delete_option('cc_cookie_policy_link');
        delete_option('cc_theme_color');
    }

    function create_plugin_settings()
    {
        add_submenu_page('options-general.php', 'Cookie Consent Settings', 'Cookie Consent', 'manage_options', 'cookie_consent_settings', array(
            $this,
            'plugin_settings_page_content'
        ));
    }

    function plugin_settings_page_content()
    { ?>
        <div class="wrap">
            <h2>Cookie Consent Settings</h2>
            <form method="post" action="options.php">
                <?php
                settings_fields('cookie_consent_settings_fields');
                do_settings_sections('cookie_consent_settings_fields');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    function setup_sections()
    {
        add_settings_section('first_section', 'Configuration', array(
            $this,
            'render_section_description'
        ), 'cookie_consent_settings_fields');
        add_settings_section('second_section', 'Dialog content', array(
            $this,
            'render_section_description'
        ), 'cookie_consent_settings_fields');
        add_settings_section('third_section', 'Dialog appearance', array(
            $this,
            'render_section_description'
        ), 'cookie_consent_settings_fields');
    }

    function render_section_description($arguments)
    {
        switch ($arguments['id']) {
            case 'first_section':
                echo 'Settings for cookies and Analysis website analytics tools.';
                break;
            case 'second_section':
                echo 'Settings for the cookie consent dialog.';
                break;
            case 'third_section':
                echo 'Dialog appearance settings. Color settings for links and buttons.';
                break;
        }
    }

    function setup_fields()
    {
        $edit_fields = array(
            array(
                'uid' => 'cc_domain',
                'label' => 'Domain',
                'section' => 'first_section',
                'type' => 'text',
                'options' => false,
                'placeholder' => 'example.com',
                'default' => ''
            ),
            array(
                'uid' => 'cc_cookie_name',
                'label' => "Cookie name",
                'section' => 'first_section',
                'type' => 'text',
                'options' => false,
                'placeholder' => '',
                'default' => ''
            ),
            array(
                'uid' => 'cc_gtm_id',
                'label' => "Google Tag Manager ID",
                'section' => 'first_section',
                'type' => 'text',
                'options' => false,
                'placeholder' => '',
                'default' => ''
            ),
            array(
                'uid' => 'cc_gtm_id',
                'label' => "Google Tag Manager ID",
                'section' => 'first_section',
                'type' => 'text',
                'options' => false,
                'placeholder' => 'GTM-XXXXXXX',
                'default' => ''
            ),
            array(
                'uid' => 'cc_ga_id',
                'label' => "Google Analytics ID",
                'section' => 'first_section',
                'type' => 'text',
                'options' => false,
                'placeholder' => 'UA-XXXXXXXXX-X',
                'default' => ''
            ),
            array(
                'uid' => 'cc_message',
                'label' => 'Message',
                'section' => 'second_section',
                'type' => 'textarea',
                'options' => false,
                'placeholder' => '',
                'default' => 'We would like to measure how you browse our website to constantly improve it, based on your usage patterns. To accomplish this, we must store cookies on your device. If you’re cool with that, hit “Accept all cookies”.'
            ),
            array(
                'uid' => 'cc_privacy_policy_link',
                'label' => 'Privacy Policy URL',
                'section' => 'second_section',
                'type' => 'text',
                'options' => false,
                'placeholder' => '',
                'default' => ''
            ),
            array(
                'uid' => 'cc_cookie_policy_link',
                'label' => 'Cookie Policy URL',
                'section' => 'second_section',
                'type' => 'text',
                'options' => false,
                'placeholder' => '',
                'default' => ''
            ),
            array(
                'uid' => 'cc_theme_color',
                'label' => 'Theme color',
                'section' => 'third_section',
                'type' => 'color',
                'options' => false,
                'placeholder' => '',
                'default' => '#0052cc'
            )
        );

        foreach ($edit_fields as $field) {
            add_settings_field($field['uid'], $field['label'], array(
                $this,
                'render_field'
            ), 'cookie_consent_settings_fields', $field['section'], $field);
            register_setting('cookie_consent_settings_fields', $field['uid']);
        }
    }

    function render_field($arguments)
    {
        $value = get_option($arguments['uid']);
        $type = $arguments['type'];
        $placeholder = $arguments['placeholder'];
        $uid = $arguments['uid'];

        if (!$value) :
            $value = $arguments['default'];
        endif;

        if ($type === 'color' || $type === 'text') : ?>
            <input name="<?= $uid; ?>" id="<?= $uid; ?>" type="<?= $type; ?>" placeholder="<?= $placeholder; ?>" value="<?= $value; ?>" style="min-width: 379px;" />
        <?php endif;

        if ($type === 'textarea') : ?>
            <textarea name="<?= $uid; ?>" id="<?= $uid; ?>" placeholder="<?= $placeholder; ?>" rows="5" cols="50"><?= $value; ?></textarea>
        <?php endif;
    }

    function add_cookie_consent_js()
    {
        wp_enqueue_script('cookie_consent_js', plugins_url('cookie-consent.min.js', __FILE__));
    }

    function add_cookie_consent_config_js()
    {
        wp_register_script('cookie_consent_config_js', plugins_url('cookie-consent-config.js', __FILE__));
        wp_localize_script('cookie_consent_config_js', 'cookieConsentSettings', array(
            'cookiePolicyLink' => get_option('cc_cookie_policy_link'),
            'domain' => get_option('cc_domain'),
            'gaId' => get_option('cc_ga_id'),
            'gtmId' => get_option('cc_gtm_id'),
            'message' => get_option('cc_message'),
            'name' => get_option('cc_cookie_name'),
            'privacyPolicyLink' => get_option('cc_privacy_policy_link')
        ));
        wp_enqueue_script('cookie_consent_config_js', plugins_url('cookie-consent-config.js', __FILE__), array(), false, true);
    }

    function add_cookie_consent_css()
    {
        wp_register_style('cookie_consent_css', plugins_url('cookie-consent.min.css', __FILE__));
        wp_enqueue_style('cookie_consent_css');
    }

    function add_cookie_consent_style_css()
    {
        wp_register_style('cookie_consent_style_css', plugins_url('cookie-consent-style.css', __FILE__));
        wp_enqueue_style('cookie_consent_style_css');
    }

    function add_custom_styles()
    {
        $theme_color = get_option('cc_theme_color');
        ?>
        <style>
            .cc-btn {
                background: <?= $theme_color; ?>;
                border-color: <?= $theme_color; ?>;
                color: white;
            }

            .cc-btn:hover {
                background: <?= $theme_color; ?>;
                opacity: 0.85;
                border-color: <?= $theme_color; ?>;
            }

            .cc-btn.cc-customize,
            .cc-highlight .cc-btn:first-child {
                color: <?= $theme_color; ?> !important;
                background-color: white;
                border-color: <?= $theme_color; ?> !important;
            }

            .cc-btn.cc-customize:hover {
                background-color: transparent;
            }

            .cc-customize-content input[type="checkbox"]:checked::before {
                background: <?= $theme_color; ?>;
            }

            a.cc-link,
            a.cc-customize {
                color: <?= $theme_color; ?> !important;
            }
        </style>
<?php
    }
}

new Cookie_Consent_Plugin();
?>