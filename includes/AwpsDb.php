<?php

namespace Awps;

defined('ABSPATH') || exit;

class AwpsDb
{

    /**
     * Gets cached summary from db
     * 
     * @param int $post_id
     * @return string
     */
    public function get_post_summary_from_db($post_id): string
    {
        global $wpdb;

        $summary = '';
        $table = $wpdb->prefix . AWPS_SUMMARIZER_TABLE;
        $query = $wpdb->prepare("SELECT `summary` FROM $table WHERE `post_id` = %d", $post_id);
        $result = $wpdb->get_row($query);

        if (!empty($result) && isset($result->summary)) {
            $summary =  strip_shortcodes($result->summary);
        }

        return sanitize_text_field($summary);
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
     * Delete summary table from db
     * 
     * @return bool
     */
    public function delete_summary_table(): void
    {
        global $wpdb;

        $table_name = $wpdb->prefix . AWPS_SUMMARIZER_TABLE;
        $query = "DROP table $table_name";
        $wpdb->query($query);
    }
}
