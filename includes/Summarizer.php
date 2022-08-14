<?php

namespace Awps;

defined('ABSPATH') || exit;

class Summarizer
{
    private $api;

    public function __construct()
    {
        $option = get_option('awps_options');

        if ($option && isset($option['awps_enable_summarizer'])) {

            if ($option['awps_enable_summarizer'] == 'checked') {

                $api_key = $option['awps_mc_api_key'];
                $this->api = new Api($api_key);
            }
        }
    }

    public function hooks(): void
    {
        add_action('publish_post', [$this, 'save_post_summary']);
    }

    /**
     * Save a post summary to db whenever it's published
     * or updated
     * 
     * @param int $post_id
     * @return mixed
     */
    public function save_post_summary($post_id): mixed
    {
        if (!empty($this->api)) {

            global $wpdb;

            $sentences = 2;
            $post = get_post($post_id);

            $post_summary = $this->get_post_summary($post->post_content, $sentences);

            if (!empty($post_summary)) {

                $summary_data = ['post_id' => $post_id, 'summary' => $post_summary];

                // check if summary exists then uodate it or
                return $wpdb->insert($wpdb->prefix . AWPS_SUMMARIZER_TABLE, $summary_data);
            }
        }

        return false;
    }

    public function update_post_summary(): void
    {
    }

    /**
     * Gets a post summary from API
     * 
     * @param string $post
     * @param int $sentences
     */
    public function get_post_summary($post, $sentences): string
    {
        if (!is_null($this->api)) {
            $summary = $this->api->get_text_summary($post, $sentences);
            return $summary;
        }

        return '';
    }
}
