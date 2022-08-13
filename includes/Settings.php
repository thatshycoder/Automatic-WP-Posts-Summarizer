<?php

namespace Awpps;

defined('ABSPATH') || exit;

use Awpps\Utils as Utils;

class Settings
{
    public function hooks()
    {
        add_action('admin_menu', [$this, 'awpps_add_settings_menu']);
        add_action('admin_init', [$this, 'awpps_settings_init']);
    }

    public function awpps_settings_init()
    {
        register_setting('awpps', 'awpps_options',  ['sanitize_callback' => [$this, 'sanitize_inputs']]);

        add_settings_section(
            'awpps',
            '',
            '',
            'awpps'
        );

        add_settings_field(
            'awpps_enable_summarizer',
            __('Enable Summarizer', 'awpps'),
            [$this, 'awpps_enable_summarizer_field_cb'],
            'awpps',
            'awpps',
            array(
                'label_for'         => 'awpps_enable_summarizer'
            )
        );

        add_settings_field(
            'awpps_summary_position',
            __('Display Summary', 'awpps'),
            [$this, 'awpps_summary_position_field_cb'],
            'awpps',
            'awpps',
            array(
                'label_for'         => 'awpps_summary_position'
            )
        );

        add_settings_field(
            'awpps_mc_api_key',
            __('MeaningCloud API Key', 'awpps'),
            [$this, 'awpps_mc_api_key_field_cb'],
            'awpps',
            'awpps',
            array(
                'label_for'         => 'awpps_mc_api_key'
            )
        );
    }

    public function awpps_summary_position_field_cb($args)
    {
        $options = get_option('awpps_options');
?>
        <div class="">
            <select name="awpps_options[<?php echo esc_attr($args['label_for']); ?>]">
                <option value="before">Before Post Content</option>
                <option value="before">After Post Content</option>
            </select>
        </div>
    <?php
    }

    public function awpps_enable_summarizer_field_cb($args)
    {
        $options = get_option('awpps_options');
    ?>
        <div class="">
            <input type="checkbox" name="awpps_options[<?php echo esc_attr($args['label_for']); ?>]" value="<?php echo isset($options[$args['label_for']]) ? $options[$args['label_for']] : ''; ?>">
        </div>
    <?php
    }

    public function awpps_mc_api_key_field_cb($args)
    {
        $options = get_option('awpps_options');
    ?>
        <div class="">
            <input type="text" name="awpps_options[<?php echo esc_attr($args['label_for']); ?>]" value="<?php echo isset($options[$args['label_for']]) ? $options[$args['label_for']] : ''; ?>">
        </div>
    <?php
    }

    public function awpps_add_settings_menu()
    {

        add_submenu_page(
            'edit.php',
            'Settings - Automatic WP Posts Summarizer',
            'Automatic WP Posts Summarizer',
            'manage_options',
            'awpps-settings',
            [$this, 'awpps_settings_page_html'],
        );
    }

    public function awpps_settings_page_html()
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

    public function sanitize_inputs($input)
    {
        return Utils::sanitize_inputs($input);
    }
}
