<?php

namespace Awps;

defined('ABSPATH') || exit;

class Summary
{
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
                    // process shortcode attributes
                    if (!empty($atts)) {

                        $atts = array_change_key_case((array) $atts, CASE_LOWER);

                        if (isset($atts['title'])) {

                            $summary_title = sanitize_text_field($atts['title']);

                            if (!empty($summary_title)) {

                                $summary = $this->get_post_summary_from_db(get_the_ID());
                                $summary =  $this->render_summary_output($summary, '', $summary_title);

                                return $summary;
                            }
                        }
                    } else {
                        $summary = $this->get_post_summary_from_db(get_the_ID());
                        $summary =  $this->render_summary_output($summary);

                        return $summary;
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
    private function render_summary_output($summary, $content = '', $title = ''): string
    {
        $summary_title = __('A Quick Summary');
        $output = '';

        if (!empty($title)) {
            $summary_title = $title;
        } elseif (isset($this->options[$this->settings::SUMMARY_TITLE_OPTION])) {
            if (!empty($this->options[$this->settings::SUMMARY_TITLE_OPTION])) {

                $summary_title = $this->options[$this->settings::SUMMARY_TITLE_OPTION];
            }
        }

        $output .= '<h3>' . $summary_title . '</h3>';
        $output .= '<p>' . $summary . '</p>';

        if (isset($this->options[$this->settings::SUMMARY_POSITION_OPTION])) {
            if ($this->options[$this->settings::SUMMARY_POSITION_OPTION] === 'after') {
                $output .= $content . '<hr>' . $output;
            }
        }

        // TODO: Make the <hr> customizable/add class
        $output .= $output . '<hr>' . $content;
        // TODO: Ensure this is done properly
        return apply_filters('awps_summary', $output);
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

        // TODO: Ensure this is done properly
        return do_action('awps_get_post_summary', $summary);
    }
}
