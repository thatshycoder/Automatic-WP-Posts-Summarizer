<?php

namespace Awps;

defined('ABSPATH') || exit;

class Settings
{
    const OPTIONS = 'awps_options';
    const ENABLE_SUMMARIZER_OPTION = 'awps_enable_summarizer';
    const DISPLAY_SUMMARIZER_ON_POSTS_OPTION = 'awps_display_summarizer_allposts';
    const SUMMARY_TITLE_OPTION = 'awps_enable_summary_title';
    const SUMMARY_LENGTH_OPTION = 'awps_summary_length';
    const SUMMARY_POSITION_OPTION = 'awps_summary_position';
    const API_KEY_OPTION = 'awps_mc_api_key';
    public $options;

    public function __construct()
    {
        $this->options = get_option(self::OPTIONS);
    }

    public function hooks(): void
    {
        add_action('admin_menu', [$this, 'settings_menu']);
        add_action('admin_init', [$this, 'settings_init']);
    }

    /**
     * Register all settings fields and section
     */
    public function settings_init(): void
    {
        register_setting('awps', 'awps_options', ['sanitize_callback' => [$this, 'sanitize_inputs']]);

        add_settings_section(
            'awps',
            '',
            '',
            'awps'
        );

        add_settings_field(
            self::ENABLE_SUMMARIZER_OPTION,
            __('Enable Summarizer', 'automatic-wp-posts-summarizer'),
            [$this, 'checkbox_field_cb'],
            'awps',
            'awps',
            array(
                'label_for'         => self::ENABLE_SUMMARIZER_OPTION
            )
        );

        add_settings_field(
            self::DISPLAY_SUMMARIZER_ON_POSTS_OPTION,
            __('Display Summary on All Posts', 'automatic-wp-posts-summarizer'),
            [$this, 'checkbox_field_cb'],
            'awps',
            'awps',
            array(
                'label_for'         => self::DISPLAY_SUMMARIZER_ON_POSTS_OPTION
            )
        );

        add_settings_field(
            self::SUMMARY_POSITION_OPTION,
            __('Display Summary', 'automatic-wp-posts-summarizer'),
            [$this, 'dropdown_field_cb'],
            'awps',
            'awps',
            array(
                'label_for'         => self::SUMMARY_POSITION_OPTION
            )
        );

        add_settings_field(
            self::SUMMARY_LENGTH_OPTION,
            __('Summary Sentences Length', 'automatic-wp-posts-summarizer'),
            [$this, 'number_field_cb'],
            'awps',
            'awps',
            array(
                'label_for'         => self::SUMMARY_LENGTH_OPTION
            )
        );

        add_settings_field(
            self::SUMMARY_TITLE_OPTION,
            __('Summary Title', 'automatic-wp-posts-summarizer'),
            [$this, 'text_field_cb'],
            'awps',
            'awps',
            array(
                'label_for'         => self::SUMMARY_TITLE_OPTION
            )
        );

        add_settings_field(
            self::API_KEY_OPTION,
            __('MeaningCloud API Key', 'automatic-wp-posts-summarizer'),
            [$this, 'text_field_cb'],
            'awps',
            'awps',
            array(
                'label_for'         => self::API_KEY_OPTION,
            )
        );
    }

    /**
     * Displays the summary position field
     * 
     * @param array $args
     */
    public function dropdown_field_cb($args): void
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
                <option value="before" <?php echo esc_attr($before); ?>><?php esc_attr_e('Before Post Content', 'automatic-wp-posts-summarizer') ?></option>
                <option value="after" <?php echo esc_attr($after); ?>><?php esc_attr_e('After Post Content', 'automatic-wp-posts-summarizer') ?></option>
            </select>
        </div>
    <?php
    }

    /**
     * Displays the enable summarizer, and display summary
     * on all posts fields
     * 
     * @param array $args
     */
    public function checkbox_field_cb($args): void
    {
        $checked = '';

        if (isset($this->options[$args['label_for']])) {
            if ($this->options[$args['label_for']] === 'checked') {
                $checked = 'checked';
            }
        }
    ?>
        <div class="">
            <input type="checkbox" name="awps_options[<?php echo esc_attr($args['label_for']); ?>]" value="checked" <?php echo esc_attr($checked); ?>>
        </div>
        <?php
    }

    /**
     * Displays summary title, and meaning cloud api key field
     * 
     * @param array $args
     */
    public function text_field_cb($args): void
    {
        if ($args['label_for'] === self::API_KEY_OPTION) {
        ?>
            <div class="">
                <input type="text" name="awps_options[<?php echo esc_attr($args['label_for']); ?>]" placeholder="<?php echo isset($this->options[$args['label_for']]) ? esc_attr('****************') : ''; ?>">
            </div>
        <?php } else {
        ?>
            <div class="">
                <input type="text" name="awps_options[<?php echo esc_attr($args['label_for']); ?>]" value="<?php echo isset($this->options[$args['label_for']]) ? esc_attr($this->options[$args['label_for']]) : ''; ?>">
            </div>
        <?php
        }
    }

    /**
     * Displays summary sentences length field
     * 
     * @param array $args
     */
    public function number_field_cb($args): void
    {
        ?>
        <div class="">
            <input type="number" min="1" max="5" name="awps_options[<?php echo esc_attr($args['label_for']); ?>]" value="<?php echo isset($this->options[$args['label_for']]) ? esc_attr($this->options[$args['label_for']]) : ''; ?>">
        </div>
    <?php
    }

    /**
     * Adds plugin settings to admin menu
     */
    public function settings_menu(): void
    {

        add_submenu_page(
            'edit.php',
            'Settings - Automatic WP Posts Summarizer',
            'Automatic WP Posts Summarizer',
            'manage_options',
            'awps-settings',
            [$this, 'settings_page_html'],
        );
    }

    /**
     * Displays plugin settings page
     */
    public function settings_page_html(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (isset($_GET['settings-updated'])) {
            add_settings_error('awps_messages', 'awps_messages', 'Settings Saved', 'updated');
        }
    ?>
        <div class="wrap">
            <h1><?php echo esc_attr('Automatic WP Posts Summarizer'); ?></h1>
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
    public function sanitize_inputs($input): array
    {
        $api_key_fields = [self::API_KEY_OPTION];
        return \Awps\SettingsUtils::sanitize_inputs($input, $this->options, $api_key_fields);
    }
}
