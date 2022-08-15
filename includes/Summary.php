<?php

namespace Awps;

defined('ABSPATH') || exit;

class Summary
{
    // add shortcode
    // if option is enabled to display summary
    // filter the_content and display summary based on option
    // make sure summary is not rendered twice eg shortcode + filter clash

    private $options;
    private $settings;

    public function __construct($awps)
    {
        $this->settings = $awps->settings;
        $this->options = $awps->settings->options;
    }

    public function hooks()
    {
        add_shortcode('awps', [$this, 'shortcode']);
        add_filter('the_content', [$this, 'display_summarizer_in_all_posts']);
    }

    public function display_summarizer_in_all_posts($content): string
    {
        // ensure we're filtering the right post
        if (is_singular() && in_the_loop() && is_main_query()) {

            if ($this->options) {

                // ensure the option is enabled for showing summary on all posts
                if (isset($this->options[$this->settings::ENABLE_SUMMARIZER_OPTION])) {
                    if ($this->options[$this->settings::ENABLE_SUMMARIZER_OPTION] === "checked") {

                        $summary = $this->get_post_summary_from_db(get_the_ID());

                        if (!empty($summary)) {

                            $output = '<h3>' . $this->settings::SUMMARY_TITLE_OPTION . '</h3>';
                            $output .= '<p>' . $summary . '</p>';
                            $content = $output . $content;

                            // TODO: Ensure this is done properly.
                            apply_filters('awps_post_summary', $content);
                        }
                    }
                }
            }
        }

        return $content;
    }

    public function shortcode($atts): void
    {
        $atts = array_change_key_case((array) $atts, CASE_LOWER);

        if (!empty($atts)) {

            //
        }
    }

    private function get_post_summary_from_db($post_id): string
    {
        global $wpdb;

        $summary = '';
        $table = $wpdb->prefix . AWPS_SUMMARIZER_TABLE;
        $query = $wpdb->prepare("SELECT `summary` FROM $table WHERE `post_id` = %d", $post_id);
        $result = $wpdb->get_row($query);

        if (!empty($result) && isset($result->summary)) {
            return $result->summary;
        }

        return $summary;
    }
}
