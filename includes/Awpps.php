<?php

namespace Awpps;

defined('ABSPATH') || exit;

class Awpps
{
    private static $instance;

    private $settings;

    private function __construct()
    {
        $this->settings = new Settings();
    }

    public static function getInstance(): self
    {

        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    public static function activate()
    {
        // create needed db tables
    }

    public static function deactivate()
    {
        // cleanup db
    }

    public function load_settings_page()
    {
        $this->settings->hooks();
    }
}
