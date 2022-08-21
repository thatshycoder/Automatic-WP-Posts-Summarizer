<?php

namespace Awps;

defined('ABSPATH') || exit;

class Summarizer
{

    /**
     * Api class instance
     * 
     * @var Api
     */
    private $api;

    /**
     * AwpsDb class instance
     * 
     * @var AwpsDb
     */

    private $AwpsDb;

    private $summary_length;

    public function __construct($awps)
    {
        $this->AwpsDb = $awps->AwpsDb;
        $option = $awps->settings->options;

        if (
            $option && isset($option[$awps->settings::ENABLE_SUMMARIZER_OPTION]) &&
            isset($option[$awps->settings::API_KEY_OPTION])
        ) {

            if (
                $option[$awps->settings::ENABLE_SUMMARIZER_OPTION] == 'checked' &&
                !empty($option[$awps->settings::API_KEY_OPTION])
            ) {

                $api_key = $option[$awps->settings::API_KEY_OPTION];
                $api_key = \Awps\SettingsUtils::decrypt($api_key);

                $this->api = new Api($api_key);

                if (isset($option[$awps->settings::SUMMARY_LENGTH_OPTION])) {

                    $this->summary_length = (int) $option[$awps->settings::SUMMARY_LENGTH_OPTION];

                    // ensure summary length is not empty
                    if ($this->summary_length == 0) {
                        $this->summary_length = 2;
                    }
                } else {
                    $this->summary_length = 2;
                }
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
        if (isset($this->api)) {
            if (!empty($this->api)) {

                $post = get_post($post_id);
                $post_summary = $this->get_post_summary_from_api($post->post_content, $this->summary_length);

                if (!empty($post_summary)) {

                    return $this->AwpsDb->save_post_summary_to_db($post_id, $post_summary);
                } else {

                    // TODO: Add an admin notice later to show warning
                    // couldn't generate a summary for the post
                }
            }
        }

        return false;
    }

    /**
     * Gets a post summary from API
     * 
     * @param string $post
     * @param int $sentences
     */
    public function get_post_summary_from_api($post, $length): string
    {
        if (!empty($post)) {

            if (!is_null($this->api)) {

                $post = strip_shortcodes($post);
                $summary = $this->api->get_text_summary($post, $length);
                $summary = str_replace('[...]', '', $summary);

                return sanitize_text_field($summary);
            }
        }

        return '';
    }
}
