<?php
defined('ABSPATH') || exit;

function awpps_add_settings_menu()
{

    add_submenu_page(
        'edit.php',
        'Settings - Automatic WP Posts Summarizer',
        'Automatic WP Posts Summarizer',
        'manage_options',
        'awpps-settings',
        'awpps_settings_page_html',
    );
}

add_action('admin_menu', 'awpps_add_settings_menu');

function awpps_settings_init()
{
    register_setting('awpps', 'awpps_options',  ['sanitize_callback' => 'awpps_sanitize_input']);

    add_settings_section(
        'awpps',
        '',
        '',
        'awpps'
    );

    add_settings_field(
        'awpps_mc_api_key',
        __('MeaningCloud API Key', 'awpps'),
        'awpps_mc_api_key_field_cb',
        'awpps',
        'awpps',
        array(
            'label_for'         => 'awpps_mc_api_key'
        )
    );
}

add_action('admin_init', 'awpps_settings_init');

function awpps_mc_api_key_field_cb($args)
{
    $options = get_option('awpps_options');
?>
    <div class="form-group form-row">
        <div class="col-md-6">
            <input type="text" name="awpps_options[<?php echo esc_attr($args['label_for']); ?>]" class="form-control" value="<?php echo isset($options[$args['label_for']]) ? $options[$args['label_for']] : ''; ?>">
        </div>
    </div>
<?php
}

function awpps_settings_page_html()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_GET['settings-updated'])) {
        add_settings_error('awpps_messages', 'awpps_messages', 'Settings Saved', 'updated');
    }
?>
    <div class="wrap">
        <h1>Automatic WP Posts Summarizer</h1>
        <div class="awpps-page-body">
            <form action="options.php" method="post">
                <?php settings_errors('awpps_messages'); ?>
                <?php settings_fields('awpps'); ?>
                <?php do_settings_sections('awpps'); ?>
                <?php submit_button('Save Settings'); ?>
            </form>
        </div>
    </div>
<?php

}

function awpps_sanitize_input($inputs)
{

    $options = get_option('awpps_options');

    $sanitized_input = [];

    foreach ($inputs as $input_key => $input_value) {

        if (empty($input_value)) {
            $sanitized_input[$input_key] = $options[$input_key];
        } else {

            $input_value = str_replace(' ', '', $input_value);
            $input_value = trim(strip_tags(stripslashes($input_value)));
            $input_value = sanitize_text_field($input_value);

            if ($input_key !== 'awpps_subscription_product_id') {

                $input_value = awpps_encrypt_mc_keys($input_value);
            }

            $sanitized_input[$input_key] = $input_value;
        }
    }

    return $sanitized_input;
}

function awpps_encrypt_mc_keys($key)
{

    $security = new DAHACH_Security();
    return $security->encrypt($key);
}
