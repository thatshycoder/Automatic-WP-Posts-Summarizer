<?php

namespace Awps;

defined('ABSPATH') || exit;

class Awps
{
    private static $instance;

    /**
     * AwpsDb class instance
     * 
     * @var AwpsDb
     */
    public $AwpsDb;

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
        $this->AwpsDb = new AwpsDb();
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
        $this->AwpsDb->create_summarizer_table();
        // store settings encryption key
        \Awps\SettingsUtils::store_key();
        // initialize needed option
        add_option($this->settings::OPTIONS);
    }
}
