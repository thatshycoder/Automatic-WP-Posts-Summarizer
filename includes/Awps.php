<?php

namespace Awps;

defined('ABSPATH') || exit;

class Awps
{
    private static $instance;

    /**
     * Settings instance
     * @var Settings
     */
    public $settings;

    /**
     * Summarizer instance
     * @var Summarizer
     */
    private $summarizer;

    /**
     * Summary instance
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

    public function activate(): void
    {
        // create needed db table
        $this->create_summarizer_table();
        // store settings encryption key
        \Awps\SettingsUtils::store_key();
        // initialize needed option
        add_option($this->settings::OPTIONS);
    }

    public function create_summarizer_table(): void
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "( `post_id` INT NOT NULL , `summary` TEXT NOT NULL , PRIMARY KEY (`post_id`))";
        $query = 'CREATE TABLE ' . $wpdb->prefix . AWPS_SUMMARIZER_TABLE . $sql . $charset_collate;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        maybe_create_table($wpdb->prefix . AWPS_SUMMARIZER_TABLE, $query);
    }

    public function deactivate(): void
    {
        // cleanup db
        $this->delete_summarizer_table();
        delete_option('awps_options');
    }

    private function delete_summarizer_table(): mixed
    {
        global $wpdb;

        $table_name = $wpdb->prefix . AWPS_SUMMARIZER_TABLE;
        $query = "DROP table $table_name";
        return $wpdb->query($query);
    }
}
