<?php

/**
 * Plugin Name:		Automatic WP Posts Summarizer
 * Author:		    Shycoder
 * Author URI:		https://shycoder.com/
 * Description:		AI Powered automatic posts summarization plugin for WordPress
 * Version:		    1.0
 * Plugin URI:		https://wordpress.org/plugins/automatic-wp-posts-summarizer
 * Requires PHP:	7.4
 * Requires at least:	5.1
 */

// Exit if accessed directly
defined('ABSPATH') || exit;

defined('AWPPS_DIR') || define('AWPPS_DIR', plugin_dir_path(__FILE__));
defined('AWPPS_ENCRYPTION_METHOD') || define('AWPPS_ENCRYPTION_METHOD', 'AES-256-CBC');

require_once 'autoload.php';

if (empty($GLOBALS['awpps'])) {

    $GLOBALS['awpps'] = Awpps\Awpps::getInstance();

    register_activation_hook(__FILE__, [$GLOBALS['awpps'], 'activate']);
    register_deactivation_hook(__FILE__, [$GLOBALS['awpps'], 'deactivate']);
}
