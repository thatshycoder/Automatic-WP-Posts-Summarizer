<?php
defined('ABSPATH') || exit;

class AWPPS
{
    public static function activate()
    {
    }

    public static function load()
    {
        // create neaded tables

        // add settings page
        include_once(plugin_dir_path(plugin_dir_path(__FILE__)) . 'settings.php');
    }
}
