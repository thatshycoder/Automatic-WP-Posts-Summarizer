<?php

/**
 * Plugin Name:		        Automatic WP Posts Summarizer
 * Author:		            Shycoder
 * Author URI:		        https://shycoder.com/
 * Description:		        AI Powered automatic posts summarization plugin for WordPress
 * Version:		            1.0.1
 * Plugin URI:		        https://wordpress.org/plugins/automatic-wp-posts-summarizer
 * Requires PHP:	        5.2 or higher
 * Requires at least:	    4.0
 * Text Domain:             automatic-wp-posts-summarizer
 * License:                 GPLv3 or later
 */

// Exit if accessed directly
defined('ABSPATH') || exit;

defined('AWPS_DIR') || define('AWPS_DIR', plugin_dir_path(__FILE__));
defined('AWPS_ENCRYPTION_METHOD')  || define('AWPS_ENCRYPTION_METHOD', 'AES-256-CBC');
defined('AWPS_SUMMARIZER_TABLE')   || define('AWPS_SUMMARIZER_TABLE', 'awps_summarizer');

require_once 'vendor/autoload.php';

$awps = Awps\Awps::get_instance();


register_activation_hook(__FILE__, [$awps, 'activate']);
register_uninstall_hook(__FILE__, 'uninstall');

function uninstall()
{
    global $wpdb;

    delete_option('awps_options');

    $table_name = $wpdb->prefix . AWPS_SUMMARIZER_TABLE;
    $query = "DROP table $table_name";
    $wpdb->query($query);
}
