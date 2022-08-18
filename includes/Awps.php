<?php

namespace Awps;

defined('ABSPATH') || exit;

class Awps
{
    private static $instance;

    /**
     * Settings class instance
     * 
     * @var Settings
     */
    public $settings;

    /**
     * Summarizer class instance
     * 
     * @var Summarizer
     */
    private $summarizer;

    /**
     * Summary class instance
     * 
     * @var Summary
     */
    private $summary;

    private function __construct()
    {
        $this->settings = new Settings();
        $this->summarizer = new Summarizer($this);
        $this->summary = new Summary($this);
        $this->hooks();
        $this->summarizer->hooks();
        $this->summary->hooks();
    }

    private function hooks(): void
    {
        add_action('plugins_loaded', [$this, 'load_settings_page']);
    }

    public function load_settings_page(): void
    {
        $this->settings->hooks();
    }

    public static function get_instance(): self
    {

        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Things to do when plugin is activated
     */
    public function activate(): void
    {
        // create needed db table
        $this->create_summarizer_table();
        // store settings encryption key
        \Awps\SettingsUtils::store_key();
        // initialize needed option
        add_option($this->settings::OPTIONS);
    }

    /**
     * Add summary table to db
     */
    public function create_summarizer_table(): void
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "( `post_id` INT NOT NULL , `summary` TEXT NOT NULL , PRIMARY KEY (`post_id`))";
        $query = 'CREATE TABLE ' . $wpdb->prefix . AWPS_SUMMARIZER_TABLE . $sql . $charset_collate;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        maybe_create_table($wpdb->prefix . AWPS_SUMMARIZER_TABLE, $query);
    }

    /**
     * Things to do when plugin is being deactivated
     */
    public function deactivate(): void
    {
        // cleanup db
        $this->delete_summary_table();
        delete_option('awps_options');
    }

    /**
     * Delete summary table from db
     * 
     * @return bool
     */
    private function delete_summary_table(): bool
    {
        global $wpdb;

        $table_name = $wpdb->prefix . AWPS_SUMMARIZER_TABLE;
        $query = "DROP table $table_name";
        $wpdb->query($query);

        return true;
    }
}
