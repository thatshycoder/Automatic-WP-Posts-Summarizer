<?php

namespace Awps;

use Awps\Templates\Main;

defined('ABSPATH') || exit;

class Summary
{

    /**
     * An instance of AwpsDb
     * 
     * @var AwpsDb
     */

    private $AwpsDb;

    /**
     * An instance of the settings class
     * 
     * @var Settings
     */
    private $settings;

    /**
     * AWPS setting options value
     * 
     * @var array
     */
    private $options;

    public function __construct($awps)
    {
        $this->AwpsDb = $awps->AwpsDb;
        $this->settings = $awps->settings;
        $this->options = $awps->settings->options;
    }

    public function hooks()
    {
        add_shortcode('awps', [$this, 'shortcode']);
        add_filter('the_content', [$this, 'display_summary_on_all_posts']);
    }

    /**
     * Displays summary on all posts
     * 
     * @param string $content
     * @return string
     */
    public function display_summary_on_all_posts($content): string
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
                        $this->options[$this->settings::ENABLE_SUMMARIZER_OPTION] === 'checked' &&
                        $this->options[$this->settings::DISPLAY_SUMMARIZER_ON_POSTS_OPTION] === 'checked'
                    ) {

                        $summary = $this->AwpsDb->get_post_summary_from_db(get_the_ID());

                        if (!empty($summary)) {

                            return $this->render_summary_output($summary, $content);
                        }
                    }
                }
            }
        }

        return $content;
    }

    /**
     * Renders Awps shortcode output
     * 
     * @param array $atts
     */
    public function shortcode($atts): string
    {
        $summary = '';
        $summary_title = '';

        if ($this->options) {

            if (isset($this->options[$this->settings::ENABLE_SUMMARIZER_OPTION])) {

                // prevents showing duplicate summary eg settings & shortcode clash
                if (
                    isset($this->options[$this->settings::DISPLAY_SUMMARIZER_ON_POSTS_OPTION]) &&
                    $this->options[$this->settings::DISPLAY_SUMMARIZER_ON_POSTS_OPTION] !== 'checked' ||
                    !isset($this->options[$this->settings::DISPLAY_SUMMARIZER_ON_POSTS_OPTION])
                ) {

                    $summary = $this->AwpsDb->get_post_summary_from_db(get_the_ID());

                    // process shortcode attributes
                    if (!empty($atts)) {

                        $atts = array_change_key_case((array) $atts, CASE_LOWER);

                        if (isset($atts['title'])) {
                            $summary_title = sanitize_text_field($atts['title']);
                        }

                        if (isset($atts['summary'])) {

                            if (!empty($atts['summary'])) {
                                $summary = sanitize_text_field($atts['summary']);
                            }
                        }
                    }

                    if (!empty($summary)) {
                        return $this->render_summary_output($summary, '', $summary_title);
                    }
                }
            }
        }

        return '';
    }

    /**
     * Render summary output with a template
     * 
     * @param string $content
     * @param string $summary
     * @return string
     */
    private function render_summary_output($summary, $content = '', $title = ''): string
    {
        $summary_title = __('A Quick Summary', 'automatic-wp-posts-summarizer');
        $output = '';
        $position = 'before';

        if ($title) {
            $summary_title = $title;
        } elseif (isset($this->options[$this->settings::SUMMARY_TITLE_OPTION])) {
            if ($this->options[$this->settings::SUMMARY_TITLE_OPTION]) {

                $summary_title = $this->options[$this->settings::SUMMARY_TITLE_OPTION];
            }
        }

        if (isset($this->options[$this->settings::SUMMARY_POSITION_OPTION])) {
            if ($this->options[$this->settings::SUMMARY_POSITION_OPTION] === 'after') {

                $position = 'after';
            }
        }

        $args = [
            'title'     => $summary_title,
            'summary'   => $summary,
            'position'  => $position,
            'content'   => $content,
        ];

        $output = Main::render($args);
        return apply_filters('awps_summary', wp_kses_post($output));
    }
}
