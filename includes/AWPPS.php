<?php
namespace AWPPS;

defined('ABSPATH') || exit;

class AWPPS
{
    public static function activate()
    {
        // create needed db tables
    }

    public static function deactivate()
    {
        // cleanup db
    }

    public static function load_settings_page()
    {
        include_once(plugin_dir_path(plugin_dir_path(__FILE__)) . 'settings.php');
    }
}
