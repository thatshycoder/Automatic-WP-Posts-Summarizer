<?php

namespace Awpps;

defined('ABSPATH') || exit;

class Summarizer
{
    private $api;

    public function __construct()
    {
        $option = get_option('awpps_options');

        if ($option) {
            $api_key = $option['awpps_mc_api_key'];
        }

        $this->api = new Api($api_key);
    }

    public function hooks(): void
    {
        add_action('edit_post', [$this, 'save_post_summary']);
    }

    public function save_post_summary($post_id): mixed
    {
        global $wpdb;

        $sentences = 2;
        $post = get_post($post_id);

        $post_summary = $this->get_post_summary($post->post_content, $sentences);

        if (!empty($post_summary)) {

            $summary_data = ['post_id' => $post_id, 'summary' => $post_summary];

            // check if summary exists then uodate it or
            return $wpdb->insert($wpdb->prefix . AWPPS_SUMMARIZER_TABLE, $summary_data);
        }

        return false;
    }

    public function update_post_summary(): void
    {
    }

    public function get_post_summary($post, $sentences): string
    {
        $summary = $this->api->get_text_summary($post, $sentences);
        return $summary;
    }
}
