<?php

namespace Awpps;

defined('ABSPATH') || exit;

class Awpps
{
    private static $instance;

    /**
     * Settings class instance
     * 
     * @var Settings
     */
    private $settings;

    private $summarizer;

    private function __construct()
    {
        $this->settings = new Settings();
        $this->hooks();
    }

    public static function getInstance(): self
    {

        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function activate(): void
    {
        // create needed db tables
    }

    public static function deactivate(): void
    {
        // cleanup db
    }

    private function hooks(): void
    {
        add_action('plugins_loaded', [$this, 'load_settings_page']);
    }

    public function load_settings_page(): void
    {
        $this->settings->hooks();
    }
}
