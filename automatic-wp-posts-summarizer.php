<?php
/**
 * Plugin Name:		Automatic WP Posts Summarizer
 * Author:		    Shycoder
 * Author URI:		https://shycoder.com/
 * Description:		AI Powered automatic posts summarization plugin for WordPress
 * Version:		    1.0
 * Plugin URI:		https://wordpress.org/plugins/automatic-wp-posts-summarizer
 */

// Exit if accessed directly
defined('ABSPATH') || exit;

require_once(plugin_dir_path(__FILE__) . 'includes/setup.php');

register_activation_hook( __FILE__, 'AWPPS::activate' );

add_action('plugins_loaded', 'AWPPS::load');