<?php

namespace Awps;

defined('ABSPATH') || exit;

class Summary
{
    // filter the_content and display summary based on option

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
        add_filter('the_content', [$this, 'display_summarizer_on_all_posts']);
    }

    /**
     * Displays summary on all posts
     * 
     * @param string $content
     * @return string
     */
    public function display_summarizer_on_all_posts($content): string
    {
        // ensure we're filtering the right post
        if (is_singular() && in_the_loop() && is_main_query()) {

            if ($this->options) {

                // ensure the option is enabled for showing summary on all posts
                if (
                    isset($this->options[$this->settings::ENABLE_SUMMARIZER_OPTION]) &&
                    isset($this->options[$this->settings::DISPLAY_SUMMARIZER_ON_POSTS_OPTION])
                ) {
                    if (
                        $this->options[$this->settings::ENABLE_SUMMARIZER_OPTION] === "checked" &&
                        $this->options[$this->settings::DISPLAY_SUMMARIZER_ON_POSTS_OPTION] === "checked"
                    ) {

                        $summary = $this->get_post_summary_from_db(get_the_ID());

                        if (!empty($summary)) {

                            $content = $this->render_summary_output($summary, $content);
                            // TODO: Ensure this is done properly.
                            apply_filters('awps_post_summary', $content);
                        }
                    }
                }
            }
        }

        return $content;
    }

    /**
     * Renders AWPS shortcode
     * 
     * @param array $atts
     */
    public function shortcode($atts): string
    {
        if ($this->options) {

            if (isset($this->options[$this->settings::ENABLE_SUMMARIZER_OPTION])) {

                // prevents showing duplicate summary eg settings & shortcode clash
                if (
                    isset($this->options[$this->settings::DISPLAY_SUMMARIZER_ON_POSTS_OPTION]) &&
                    $this->options[$this->settings::DISPLAY_SUMMARIZER_ON_POSTS_OPTION] !== 'checked' ||
                    !isset($this->options[$this->settings::DISPLAY_SUMMARIZER_ON_POSTS_OPTION])
                ) {

                    $atts = array_change_key_case((array) $atts, CASE_LOWER);

                    if (empty($atts)) {

                        //
                    } else {
                        $summary = $this->get_post_summary_from_db(get_the_ID());
                        $summary =  $this->render_summary_output($summary);

                        // TODO: Ensure this is done properly.
                        apply_filters('awps_summary_shortcode', $summary);
                    }
                }
            }
        }

        return '';
    }

    /**
     * Customizes how the summary is displayed in post content
     * 
     * @param string $content
     * @param string $summary
     * @return string
     */
    private function render_summary_output($summary, $content = ''): string
    {
        $output = '<h3>' . $this->options[$this->settings::SUMMARY_TITLE_OPTION] . '</h3>';
        $output .= '<p>' . $summary . '</p>';

        if (isset($this->options[$this->settings::SUMMARY_POSITION_OPTION])) {
            if ($this->options[$this->settings::SUMMARY_POSITION_OPTION] === 'after') {
                return $content . '<hr>' . $output;
            }
        }

        // TODO: Make the <hr> customizable/add class
        return $output . '<hr>' . $content;
    }

    /**
     * Gets cached summary from db
     * 
     * @param int $post_id
     * @return string
     */
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

        return do_action('awps_get_post_summary', $summary);
    }
}
