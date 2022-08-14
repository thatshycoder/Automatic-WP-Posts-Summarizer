<?php

namespace Awps;

defined('ABSPATH') || exit;

use Awps\Utils as Utils;

class Settings
{
    private $options;

    public function __construct()
    {
        $this->options = get_option('awps_options');
    }

    public function hooks()
    {
        add_action('admin_menu', [$this, 'awps_add_settings_menu']);
        add_action('admin_init', [$this, 'awps_settings_init']);
    }

    /**
     * Register all settings fields and section
     */
    public function awps_settings_init()
    {
        register_setting('awps', 'awps_options');

        add_settings_section(
            'awps',
            '',
            '',
            'awps'
        );

        add_settings_field(
            'awps_enable_summarizer',
            __('Enable Summarizer', 'awps'),
            [$this, 'awps_enable_summarizer_field_cb'],
            'awps',
            'awps',
            array(
                'label_for'         => 'awps_enable_summarizer'
            )
        );

        add_settings_field(
            'awps_summary_position',
            __('Display Summary', 'awps'),
            [$this, 'awps_summary_position_field_cb'],
            'awps',
            'awps',
            array(
                'label_for'         => 'awps_summary_position'
            )
        );

        add_settings_field(
            'awps_mc_api_key',
            __('MeaningCloud API Key', 'awps'),
            [$this, 'awps_mc_api_key_field_cb'],
            'awps',
            'awps',
            array(
                'label_for'         => 'awps_mc_api_key'
            )
        );
    }

    /**
     * Displays the summary position field
     * 
     * @param array $args
     */
    public function awps_summary_position_field_cb($args)
    {
        $before = '';
        $after = '';

        if (isset($this->options[$args['label_for']])) {

            if ($this->options[$args['label_for']] === 'before') {
                $before = 'selected';
            } else {
                $after = 'selected';
            }
        }

?>
        <div class="">
            <select name="awps_options[<?php echo esc_attr($args['label_for']); ?>]">
                <option value="before" <?php echo $before ?>>Before Post Content</option>
                <option value="after" <?php echo $after ?>>After Post Content</option>
            </select>
        </div>
    <?php
    }

    /**
     * Displays the enable summarizer field
     * 
     * @param array $args
     */
    public function awps_enable_summarizer_field_cb($args)
    {
        $checked = '';

        if (isset($this->options[$args['label_for']])) {
            if ($this->options[$args['label_for']] === 'checked') {
                $checked = 'checked';
            }
        }
    ?>
        <div class="">
            <input type="checkbox" name="awps_options[<?php echo esc_attr($args['label_for']); ?>]" value="checked" <?php echo $checked; ?>>
        </div>
    <?php
    }

    /**
     * Displays the meaning cloud api key field
     * 
     * @param array $args
     */
    public function awps_mc_api_key_field_cb($args)
    {

    ?>
        <div class="">
            <input type="text" name="awps_options[<?php echo esc_attr($args['label_for']); ?>]" value="<?php echo isset($this->options[$args['label_for']]) ? $this->options[$args['label_for']] : ''; ?>">
        </div>
    <?php
    }

    /**
     * Adds plugin settings to admin menu
     */
    public function awps_add_settings_menu()
    {

        add_submenu_page(
            'edit.php',
            'Settings - Automatic WP Posts Summarizer',
            'Automatic WP Posts Summarizer',
            'manage_options',
            'awps-settings',
            [$this, 'awps_settings_page_html'],
        );
    }

    /**
     * Displays plugin settings page
     */
    public function awps_settings_page_html()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (isset($_GET['settings-updated'])) {
            add_settings_error('awps_messages', 'awps_messages', 'Settings Saved', 'updated');
        }
    ?>
        <div class="wrap">
            <h1>Automatic WP Posts Summarizer</h1>
            <div class="awps-page-body">
                <form action="options.php" method="post">
                    <?php settings_errors('awps_messages'); ?>
                    <?php settings_fields('awps'); ?>
                    <?php do_settings_sections('awps'); ?>
                    <?php submit_button('Save Settings'); ?>
                </form>
            </div>
        </div>
<?php
    }

    /**
     * Santizes settings field inputs
     */
    public function sanitize_inputs($input)
    {
        return Utils::sanitize_inputs($input, $this->options);
    }
}
