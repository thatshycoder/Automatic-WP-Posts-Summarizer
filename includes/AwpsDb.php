<?php

namespace Awps;

defined('ABSPATH') || exit;

class AwpsDb
{

    /**
     * Save post to summarizer table
     * 
     * @param int $post_id
     * @param string $summary
     */
    public function save_post_summary_to_db($post_id, $summary): void
    {
        global $wpdb;

        if (!empty($post_id) && !empty($summary)) {

            $summary_data = ['post_id' => $post_id, 'summary' => $summary];
            // check if summary exists and then update it or insert new
            $wpdb->replace($wpdb->prefix . AWPS_SUMMARIZER_TABLE, $summary_data, ['%d', '%s']);
        }
    }

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
}
